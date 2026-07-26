<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit; }

$id = intval($_POST['id'] ?? 0);
if (!$id) { echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit; }

$stmt = mysqli_prepare($conexion, "UPDATE cotizaciones SET activo = 0 WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
if (mysqli_stmt_execute($stmt)) echo json_encode(['ok'=>true]);
else echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion)]);
mysqli_close($conexion);