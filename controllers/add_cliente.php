<?php
require_once __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

$nombre         = trim($_POST['nombre']         ?? '');
if ($nombre === '') {
    echo json_encode(['ok' => false, 'msg' => 'El nombre es requerido']);
    exit;
}

$telefono       = trim($_POST['telefono']       ?? '') ?: null;
$correo         = trim($_POST['correo']         ?? '') ?: null;
$direccion      = trim($_POST['direccion']      ?? '') ?: null;
$notas          = trim($_POST['notas']          ?? '') ?: null;
$alias          = trim($_POST['alias']          ?? '') ?: null;
$razon_social   = trim($_POST['razon_social']   ?? '') ?: null;
$rfc            = trim($_POST['rfc']            ?? '') ?: null;
$codigo_postal  = trim($_POST['codigo_postal']  ?? '') ?: null;
$regimen_fiscal = trim($_POST['regimen_fiscal'] ?? '') ?: null;

$stmt = mysqli_prepare($conexion,
    "INSERT INTO clientes (nombre, telefono, correo, direccion, notas, alias, razon_social, rfc, codigo_postal, regimen_fiscal, activo)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)"
);

mysqli_stmt_bind_param($stmt, 'ssssssssss',
    $nombre, $telefono, $correo, $direccion, $notas,
    $alias, $razon_social, $rfc, $codigo_postal, $regimen_fiscal
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok' => true, 'id' => mysqli_insert_id($conexion)]);
} else {
    echo json_encode(['ok' => false, 'msg' => mysqli_error($conexion)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);