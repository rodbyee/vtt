<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit; }

$nombre          = trim($_POST['nombre']       ?? '');
$sku             = trim($_POST['sku']          ?? '')?: null;
$descripcion     = trim($_POST['descripcion']  ?? '') ?: null;
$precio_final    = floatval($_POST['precio_final'] ?? 0);
$id_departamento = intval($_POST['id_departamento'] ?? 0) ?: null;

if ($nombre === '') { echo json_encode(['ok'=>false,'msg'=>'Nombre es requerido']); exit; }

$stmt = mysqli_prepare($conexion,
    "INSERT INTO inventario (nombre, sku, descripcion, precio_final, id_departamento) VALUES (?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, 'sssdi', $nombre, $sku, $descripcion, $precio_final, $id_departamento);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok'=>true, 'id'=>mysqli_insert_id($conexion)]);
} else {
    echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion)]);
}
mysqli_stmt_close($stmt);
mysqli_close($conexion);