<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);
if (!$id) { echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit; }

$res = mysqli_query($conexion,
    "SELECT f.*, c.regimen_fiscal, c.codigo_postal
     FROM facturas f
     LEFT JOIN clientes c ON c.id = f.id_cliente
     WHERE f.id = $id AND f.activo = 1"
);
$fac = mysqli_fetch_assoc($res);
if (!$fac) { echo json_encode(['ok'=>false,'msg'=>'No encontrada']); exit; }

$res2  = mysqli_query($conexion, "SELECT * FROM factura_items WHERE id_factura = $id");
$items = [];
while ($row = mysqli_fetch_assoc($res2)) $items[] = $row;

$fac['items'] = $items;
echo json_encode(['ok'=>true, 'data'=>$fac]);
mysqli_close($conexion);