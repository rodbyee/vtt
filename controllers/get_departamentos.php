<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

$res = mysqli_query($conexion, "SELECT id, nombre, color FROM departamentos WHERE activo = 1 ORDER BY nombre ASC");
$data = [];
while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
echo json_encode($data);
mysqli_close($conexion);