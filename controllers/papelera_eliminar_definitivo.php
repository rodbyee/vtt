<?php
require_once __DIR__ . '/../includes/auth_admin_json.php';
require_once __DIR__ . '/../config/conexion.php';

$tablasPermitidas = require __DIR__ . '/../config/tablas_papelera.php';
$tabla = $_POST['tabla'] ?? '';
$id    = intval($_POST['id'] ?? 0);

if (!array_key_exists($tabla, $tablasPermitidas) || !$id) {
    echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
    exit;
}

// Solo borra de verdad si el registro ya estaba en la papelera (activo = 0),
// para evitar borrar algo por accidente que sigue en uso.
$stmt = mysqli_prepare($conexion, "DELETE FROM `$tabla` WHERE id = ? AND activo = 0");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'msg' => mysqli_error($conexion)]);
}
mysqli_stmt_close($stmt);
mysqli_close($conexion);
