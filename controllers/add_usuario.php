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

$nombre   = trim($_POST['nombre']   ?? '');
$correo   = trim($_POST['correo']   ?? '');
$password = trim($_POST['password'] ?? '');
$id_rol   = intval($_POST['id_rol'] ?? 2);

if (!$nombre)              { echo json_encode(['ok'=>false,'msg'=>'El nombre es requerido']); exit; }
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) { echo json_encode(['ok'=>false,'msg'=>'Correo inválido']); exit; }
if (strlen($password) < 8) { echo json_encode(['ok'=>false,'msg'=>'La contraseña debe tener al menos 8 caracteres']); exit; }

// Verificar correo único
$r = mysqli_query($conexion, "SELECT id FROM usuarios WHERE correo = '" . mysqli_real_escape_string($conexion, $correo) . "'");
if (mysqli_num_rows($r) > 0) { echo json_encode(['ok'=>false,'msg'=>'Este correo ya está registrado']); exit; }

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conexion, "INSERT INTO usuarios (nombre, correo, password, id_rol) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sssi', $nombre, $correo, $hash, $id_rol);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok'=>true, 'id'=>mysqli_insert_id($conexion)]);
} else {
    echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion)]);
}
mysqli_stmt_close($stmt);
mysqli_close($conexion);