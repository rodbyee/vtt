<?php
session_start();
mysqli_report(MYSQLI_REPORT_OFF);
include __DIR__ . '/../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$correo   = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';

function redirigirConError(string $msg): void {
    $_SESSION['log_error']  = $msg;
    $_SESSION['log_correo'] = trim($_POST['correo'] ?? '');
    header('Location: ../index.php');
    exit;
}

if ($correo === '' || $password === '') {
    redirigirConError('Completa correo y contraseña para continuar.');
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    redirigirConError('Ingresa un correo válido.');
}


$stmt = $conexion->prepare("SELECT id, nombre, password, id_rol, activo FROM usuarios WHERE correo = ? LIMIT 1");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if (!$usuario || !password_verify($password, $usuario['password'])) {
    redirigirConError('Correo o contraseña incorrectos.');
}

if ((int)$usuario['activo'] === 0) {
    redirigirConError('Tu cuenta está desactivada. Contacta al administrador.');
}

$_SESSION['id_user']  = $usuario['id'];
$_SESSION['nombre']   = $usuario['nombre'];
$_SESSION['id_rol']   = $usuario['id_rol'];

header('Location: ../dashboard.php');
exit;