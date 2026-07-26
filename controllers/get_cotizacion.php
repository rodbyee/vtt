<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
if (!$id) { echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit; }

$res = mysqli_query($conexion, "SELECT id, folio, nombre_cliente, incluir_iva, subtotal, iva, total, notas, created_at FROM cotizaciones WHERE id = $id AND activo = 1");
$cot = mysqli_fetch_assoc($res);
if (!$cot) { echo json_encode(['ok'=>false,'msg'=>'No encontrada']); exit; }

$res2  = mysqli_query($conexion, "SELECT * FROM cotizacion_items WHERE id_cotizacion = $id");
$items = [];
while ($row = mysqli_fetch_assoc($res2)) $items[] = $row;

$cot['items'] = $items;
echo json_encode(['ok'=>true, 'data'=>$cot]);
mysqli_close($conexion);