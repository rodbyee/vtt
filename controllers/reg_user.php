<?php
session_start();
mysqli_report(MYSQLI_REPORT_OFF);
include __DIR__ . '/../config/conexion.php';

if (!isset($_SESSION['id_user']) || $_SESSION['id_rol'] != 1) {
    header('Location: ../dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

$nombre          = trim($_POST['nombre']           ?? '');
$correo          = trim($_POST['correo']            ?? '');
$password        = $_POST['password']               ?? '';
$confirmPassword = $_POST['confirm_password']       ?? '';
$id_rol          = intval($_POST['id_rol']          ?? 2);

if (!in_array($id_rol, [1, 2])) $id_rol = 2;

function redirigirConError(string $msg): void {
    $_SESSION['reg_error']  = $msg;
    $_SESSION['reg_nombre'] = trim($_POST['nombre'] ?? '');
    $_SESSION['reg_correo'] = trim($_POST['correo'] ?? '');
    header('Location: ../register.php');
    exit;
}

if ($nombre === '' || $correo === '' || $password === '' || $confirmPassword === '') {
    redirigirConError('Completa todos los campos para continuar.');
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    redirigirConError('Ingresa un correo válido.');
}
if (strlen($password) < 8) {
    redirigirConError('La contraseña debe tener al menos 8 caracteres.');
}
if ($password !== $confirmPassword) {
    redirigirConError('Las contraseñas no coinciden.');
}

$check = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ? LIMIT 1");
$check->bind_param("s", $correo);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    redirigirConError('Este correo ya está registrado.');
}
$check->close();

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conexion->prepare(
    "INSERT INTO usuarios (nombre, correo, password, id_rol) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("sssi", $nombre, $correo, $hash, $id_rol);

if ($stmt->execute()) {
    $_SESSION['reg_success'] = true;
    header('Location: ../register.php');
    exit;
} else {
    redirigirConError('Error al crear la cuenta. Intenta de nuevo.');
}