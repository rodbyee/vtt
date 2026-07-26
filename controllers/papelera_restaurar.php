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

$stmt = mysqli_prepare($conexion, "UPDATE `$tabla` SET activo = 1, deleted_at = NULL WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'msg' => mysqli_error($conexion)]);
}
mysqli_stmt_close($stmt);
mysqli_close($conexion);
