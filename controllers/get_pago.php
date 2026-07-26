<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);
if (!$id) { echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit; }

$res = mysqli_query($conexion, "SELECT * FROM pagos WHERE id = $id AND activo = 1");
$pago = mysqli_fetch_assoc($res);
if (!$pago) { echo json_encode(['ok'=>false,'msg'=>'No encontrado']); exit; }

$res2 = mysqli_query($conexion, "SELECT * FROM abonos WHERE id_pago = $id AND activo = 1 ORDER BY numero_abono ASC");
$abonos = [];
while ($row = mysqli_fetch_assoc($res2)) $abonos[] = $row;
$pago['abonos'] = $abonos;

echo json_encode(['ok'=>true, 'data'=>$pago]);
mysqli_close($conexion);