<?php
require_once __DIR__ . '/../config/conexion.php';
ini_set('display_errors', 0);
error_reporting(0);
set_exception_handler(function($e) {
    echo json_encode(['ok'=>false,'msg'=>$e->getMessage()]);
    exit;
});
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit;
}

$tipo           = trim($_POST['tipo']           ?? 'pago');
$modalidad      = trim($_POST['modalidad']      ?? 'completo');
$nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
$monto          = floatval($_POST['monto']      ?? 0);
$metodo         = trim($_POST['metodo']         ?? 'efectivo');
$referencia     = trim($_POST['referencia']     ?? '') ?: null;
$notas          = trim($_POST['notas']          ?? '') ?: null;
$recordatorio   = intval($_POST['recordatorio'] ?? 0);
$telefono_rec   = trim($_POST['telefono_recordatorio'] ?? '') ?: null;
$mensualidades  = intval($_POST['mensualidades'] ?? 0) ?: null;
$folio_doc      = trim($_POST['folio_doc']      ?? '') ?: null;
$monto_cobrado_post = floatval($_POST['monto_cobrado'] ?? -1);

// Documento relacionado
$tipo_doc       = trim($_POST['tipo_doc']       ?? '');
$id_doc         = intval($_POST['id_doc']       ?? 0) ?: null;
$id_cot_directo = intval($_POST['id_cotizacion'] ?? 0) ?: null;

if ($nombre_cliente === '') { echo json_encode(['ok'=>false,'msg'=>'El cliente es requerido']); exit; }
if ($monto <= 0)            { echo json_encode(['ok'=>false,'msg'=>'El monto debe ser mayor a 0']); exit; }

$monto_mens = null;
if ($modalidad === 'fijo' && $mensualidades >= 2) {
    $monto_mens = round($monto / $mensualidades, 2);
}

// monto_cobrado: si se envía explícito lo usamos, si no calculamos por modalidad
if ($monto_cobrado_post >= 0) {
    $monto_cobrado = $monto_cobrado_post;
} else {
    $monto_cobrado = $modalidad === 'completo' ? $monto : 0;
}

// Relaciones con documentos
$id_factura    = null;
$id_cotizacion = null;

if ($tipo === 'remision') {
    // Remisión: siempre viene de una cotización
    $id_cotizacion = $id_cot_directo;
} else {
    // Pago normal: viene de id_doc + tipo_doc
    $id_factura    = ($id_doc && $tipo_doc === 'facturas')     ? $id_doc : null;
    $id_cotizacion = ($id_doc && $tipo_doc === 'cotizaciones') ? $id_doc : null;
}

$total_diferido  = floatval($_POST['total_diferido']  ?? 0) ?: null;

$stmt = mysqli_prepare($conexion,
    "INSERT INTO pagos (tipo, modalidad, nombre_cliente, metodo, monto, monto_cobrado,
                        mensualidades, monto_mensualidad, total_diferido,
                        referencia, notas, recordatorio, telefono_recordatorio,
                        id_factura, id_cotizacion, folio_doc)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param($stmt, 'ssssddiddssiisss',
    $tipo, $modalidad, $nombre_cliente, $metodo,
    $monto, $monto_cobrado,
    $mensualidades, $monto_mens, $total_diferido,
    $referencia, $notas, $recordatorio, $telefono_rec,
    $id_factura, $id_cotizacion, $folio_doc
);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion), 'errno'=>mysqli_errno($conexion)]); 
    exit;
}

$id_pago = mysqli_insert_id($conexion);

// Solo actualizar doc si es pago completo de contado
if ($modalidad === 'completo') {
    actualizarDoc($conexion, $id_factura, $id_cotizacion, $monto);
}

echo json_encode(['ok'=>true, 'id'=>$id_pago]);
mysqli_close($conexion);

function actualizarDoc($con, $id_fac, $id_cot, $monto) {
    if ($id_fac) {
        $r = mysqli_query($con, "SELECT total, COALESCE(monto_pagado,0) AS monto_pagado FROM facturas WHERE id = $id_fac");
        $f = mysqli_fetch_assoc($r);
        if ($f) {
            $np = floatval($f['monto_pagado']) + $monto;
            $ep = $np >= floatval($f['total']) ? 'pagado' : ($np > 0 ? 'parcial' : 'pendiente');
            mysqli_query($con, "UPDATE facturas SET monto_pagado = $np, estatus_pago = '$ep' WHERE id = $id_fac");
        }
    }
    if ($id_cot) {
        $r = mysqli_query($con, "SELECT total, COALESCE(monto_pagado,0) AS monto_pagado FROM cotizaciones WHERE id = $id_cot");
        $c = mysqli_fetch_assoc($r);
        if ($c) {
            $np = floatval($c['monto_pagado']) + $monto;
            $ep = $np >= floatval($c['total']) ? 'pagado' : ($np > 0 ? 'parcial' : 'pendiente');
            mysqli_query($con, "UPDATE cotizaciones SET monto_pagado = $np, estatus_pago = '$ep' WHERE id = $id_cot");
        }
    }
}