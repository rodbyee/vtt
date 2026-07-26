<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit;
}

$id         = intval($_POST['id']         ?? 0);
$monto      = floatval($_POST['monto']    ?? 0);
$metodo     = trim($_POST['metodo']       ?? '');
$referencia = trim($_POST['referencia']   ?? '') ?: null;
$notas      = trim($_POST['notas']        ?? '') ?: null;

if (!$id)    { echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit; }
if (!$monto) { echo json_encode(['ok'=>false,'msg'=>'El monto debe ser mayor a 0']); exit; }

// Obtener abono actual para calcular diferencia
$res    = mysqli_query($conexion, "SELECT * FROM abonos WHERE id = $id AND activo = 1");
$abono  = mysqli_fetch_assoc($res);
if (!$abono) { echo json_encode(['ok'=>false,'msg'=>'Abono no encontrado']); exit; }

$monto_anterior = floatval($abono['monto']);
$diff           = $monto - $monto_anterior;
$id_pago        = intval($abono['id_pago']);

// Actualizar abono
$stmt = mysqli_prepare($conexion, "UPDATE abonos SET monto=?, metodo=?, referencia=?, notas=? WHERE id=?");
mysqli_stmt_bind_param($stmt, 'dsssi', $monto, $metodo, $referencia, $notas, $id);
if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion)]); exit;
}

// Actualizar monto_cobrado en el pago padre
$rp   = mysqli_query($conexion, "SELECT * FROM pagos WHERE id = $id_pago");
$pago = mysqli_fetch_assoc($rp);
if ($pago) {
    $nuevo_cobrado = max(0, floatval($pago['monto_cobrado']) + $diff);
    mysqli_query($conexion, "UPDATE pagos SET monto_cobrado=$nuevo_cobrado WHERE id=$id_pago");

    // Actualizar documento vinculado
    $id_fac = intval($pago['id_factura']    ?? 0) ?: null;
    $id_cot = intval($pago['id_cotizacion'] ?? 0) ?: null;

    if ($id_fac) {
        $r = mysqli_query($conexion, "SELECT total, COALESCE(monto_pagado,0) AS monto_pagado FROM facturas WHERE id=$id_fac");
        $f = mysqli_fetch_assoc($r);
        if ($f) {
            $np = max(0, floatval($f['monto_pagado']) + $diff);
            $ep = $np >= floatval($f['total']) ? 'pagado' : ($np > 0 ? 'parcial' : 'pendiente');
            mysqli_query($conexion, "UPDATE facturas SET monto_pagado=$np, estatus_pago='$ep' WHERE id=$id_fac");
        }
    }
    if ($id_cot) {
        $r = mysqli_query($conexion, "SELECT total, COALESCE(monto_pagado,0) AS monto_pagado FROM cotizaciones WHERE id=$id_cot");
        $c = mysqli_fetch_assoc($r);
        if ($c) {
            $np = max(0, floatval($c['monto_pagado']) + $diff);
            $ep = $np >= floatval($c['total']) ? 'pagado' : ($np > 0 ? 'parcial' : 'pendiente');
            mysqli_query($conexion, "UPDATE cotizaciones SET monto_pagado=$np, estatus_pago='$ep' WHERE id=$id_cot");
        }
    }
}

echo json_encode(['ok'=>true]);
mysqli_close($conexion);