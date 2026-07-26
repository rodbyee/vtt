<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit;
}

$id_pago    = intval($_POST['id_pago']    ?? 0);
$monto      = floatval($_POST['monto']    ?? 0);
$metodo     = trim($_POST['metodo']       ?? 'efectivo');
$referencia = trim($_POST['referencia']   ?? '') ?: null;
$notas      = trim($_POST['notas']        ?? '') ?: null;

if (!$id_pago) { echo json_encode(['ok'=>false,'msg'=>'ID de pago inválido']); exit; }
if ($monto <= 0) { echo json_encode(['ok'=>false,'msg'=>'El monto debe ser mayor a 0']); exit; }

// Obtener pago actual
$res  = mysqli_query($conexion, "SELECT * FROM pagos WHERE id = $id_pago AND activo = 1");
$pago = mysqli_fetch_assoc($res);
if (!$pago) { echo json_encode(['ok'=>false,'msg'=>'Pago no encontrado']); exit; }

$cobrado_actual = floatval($pago['monto_cobrado'] ?? 0);
$total          = floatval($pago['monto']);
$pendiente      = $total - $cobrado_actual;

if ($monto > $pendiente + 0.01) {
    echo json_encode(['ok'=>false,'msg'=>'El abono excede el pendiente de $' . number_format($pendiente, 2)]); exit;
}

// Número de abono
$res2 = mysqli_query($conexion, "SELECT COUNT(*) AS c FROM abonos WHERE id_pago = $id_pago");
$r2   = mysqli_fetch_assoc($res2);
$num  = intval($r2['c']) + 1;

// FIX 1: bind_param sin espacio — 'iidsss' correcto para 6 parámetros
$stmt = mysqli_prepare($conexion,
    "INSERT INTO abonos (id_pago, numero_abono, monto, metodo, referencia, notas) VALUES (?, ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, 'iidsss', $id_pago, $num, $monto, $metodo, $referencia, $notas);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion)]); exit;
}

// Actualizar monto_cobrado y abonos_pagados en pagos
$nuevo_cobrado = $cobrado_actual + $monto;
mysqli_query($conexion, "UPDATE pagos SET monto_cobrado = $nuevo_cobrado, abonos_pagados = $num WHERE id = $id_pago");

// FIX 2: usar COALESCE para que monto_pagado NULL se trate como 0
$id_fac = intval($pago['id_factura']    ?? 0) ?: null;
$id_cot = intval($pago['id_cotizacion'] ?? 0) ?: null;

if ($id_fac) {
    $r = mysqli_query($conexion, "SELECT total, COALESCE(monto_pagado, 0) AS monto_pagado FROM facturas WHERE id = $id_fac");
    $f = mysqli_fetch_assoc($r);
    if ($f) {
        $np = floatval($f['monto_pagado']) + $monto;
        $ep = $np >= floatval($f['total']) ? 'pagado' : ($np > 0 ? 'parcial' : 'pendiente');
        mysqli_query($conexion, "UPDATE facturas SET monto_pagado = $np, estatus_pago = '$ep' WHERE id = $id_fac");
    }
}

if ($id_cot) {
    $r = mysqli_query($conexion, "SELECT total, COALESCE(monto_pagado, 0) AS monto_pagado FROM cotizaciones WHERE id = $id_cot");
    $c = mysqli_fetch_assoc($r);
    if ($c) {
        $np = floatval($c['monto_pagado']) + $monto;
        $ep = $np >= floatval($c['total']) ? 'pagado' : ($np > 0 ? 'parcial' : 'pendiente');
        mysqli_query($conexion, "UPDATE cotizaciones SET monto_pagado = $np, estatus_pago = '$ep' WHERE id = $id_cot");
    }
}

echo json_encode(['ok'=>true]);
mysqli_close($conexion);