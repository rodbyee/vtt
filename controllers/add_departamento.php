<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit; }

$nombre = trim($_POST['nombre'] ?? '');
$color  = trim($_POST['color']  ?? '#eab308');

if ($nombre === '') { echo json_encode(['ok'=>false,'msg'=>'El nombre es requerido']); exit; }
if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) $color = '#eab308';

$stmt = mysqli_prepare($conexion, "INSERT INTO departamentos (nombre, color) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, 'ss', $nombre, $color);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok'=>true, 'id'=>mysqli_insert_id($conexion)]);
} else {
    echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion)]);
}
mysqli_stmt_close($stmt);
mysqli_close($conexion);