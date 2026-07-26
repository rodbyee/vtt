<?php
require_once __DIR__ . '/../includes/auth_admin_json.php';
require_once __DIR__ . '/../config/conexion.php';

$tablasPermitidas = require __DIR__ . '/../config/tablas_papelera.php';
$tabla = $_GET['tabla'] ?? '';

if (!array_key_exists($tabla, $tablasPermitidas)) {
    echo json_encode(['ok' => false, 'msg' => 'Tabla no permitida']);
    exit;
}

$res = mysqli_query($conexion, "SELECT * FROM `$tabla` WHERE activo = 0 ORDER BY deleted_at DESC LIMIT 200");
$items = [];
while ($row = mysqli_fetch_assoc($res)) $items[] = $row;

echo json_encode(['ok' => true, 'items' => $items]);
mysqli_close($conexion);
