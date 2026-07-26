<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_rol'] != 1) {
    echo json_encode(['ok'=>false,'msg'=>'Sin permisos']); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit;
}

$id = intval($_POST['id'] ?? 0);
if (!$id) { echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit; }

// Protección: no eliminar admins
$r = mysqli_query($conexion, "SELECT id_rol FROM usuarios WHERE id = $id");
$u = mysqli_fetch_assoc($r);
if (!$u) { echo json_encode(['ok'=>false,'msg'=>'Usuario no encontrado']); exit; }
if ($u['id_rol'] == 1) { echo json_encode(['ok'=>false,'msg'=>'No se puede eliminar un administrador']); exit; }

$stmt = mysqli_prepare($conexion, "UPDATE usuarios SET activo = 0 WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok'=>true]);
} else {
    echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion)]);
}
mysqli_stmt_close($stmt);
mysqli_close($conexion);