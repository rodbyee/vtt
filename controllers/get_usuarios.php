<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_rol'] != 1) {
    echo json_encode(['ok'=>false,'msg'=>'Sin permisos']); exit;
}

$res = mysqli_query($conexion,
    "SELECT id, nombre, correo, id_rol, activo, created_at FROM usuarios WHERE activo = 1 ORDER BY id_rol ASC, nombre ASC"
);
$data = [];
while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
echo json_encode($data);
mysqli_close($conexion);