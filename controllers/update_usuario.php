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

$id     = intval($_POST['id']     ?? 0);
$nombre = trim($_POST['nombre']   ?? '');
$correo = trim($_POST['correo']   ?? '');
$id_rol = intval($_POST['id_rol'] ?? 2);
$pass   = trim($_POST['password'] ?? '');

if (!$id)     { echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit; }
if (!$nombre) { echo json_encode(['ok'=>false,'msg'=>'El nombre es requerido']); exit; }
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) { echo json_encode(['ok'=>false,'msg'=>'Correo inválido']); exit; }

// Protección: no cambiar rol de admins
$r  = mysqli_query($conexion, "SELECT id_rol FROM usuarios WHERE id = $id");
$u  = mysqli_fetch_assoc($r);
if (!$u) { echo json_encode(['ok'=>false,'msg'=>'Usuario no encontrado']); exit; }
if ($u['id_rol'] == 1) $id_rol = 1; // Mantener admin si ya lo es

// Verificar correo único (excluyendo el mismo usuario)
$ce = mysqli_real_escape_string($conexion, $correo);
$r2 = mysqli_query($conexion, "SELECT id FROM usuarios WHERE correo = '$ce' AND id != $id");
if (mysqli_num_rows($r2) > 0) { echo json_encode(['ok'=>false,'msg'=>'Este correo ya está en uso']); exit; }

if ($pass) {
    if (strlen($pass) < 8) { echo json_encode(['ok'=>false,'msg'=>'La contraseña debe tener al menos 8 caracteres']); exit; }
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conexion, "UPDATE usuarios SET nombre=?, correo=?, password=?, id_rol=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssii', $nombre, $correo, $hash, $id_rol, $id);
} else {
    $stmt = mysqli_prepare($conexion, "UPDATE usuarios SET nombre=?, correo=?, id_rol=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'ssii', $nombre, $correo, $id_rol, $id);
}

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok'=>true]);
} else {
    echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion)]);
}
mysqli_stmt_close($stmt);
mysqli_close($conexion);