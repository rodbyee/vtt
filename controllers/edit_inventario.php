<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit; }

$id              = intval($_POST['id']          ?? 0);
$nombre          = trim($_POST['nombre']        ?? '');
$sku             = trim($_POST['sku']           ?? '')?: null;
$descripcion     = trim($_POST['descripcion']   ?? '') ?: null;
$precio_final    = floatval($_POST['precio_final'] ?? 0);
$id_departamento = intval($_POST['id_departamento'] ?? 0) ?: null;

if (!$id || $nombre === '') { echo json_encode(['ok'=>false,'msg'=>'Datos incompletos']); exit; }

// Obtener el precio actual para saber si cambió
$stmtActual = mysqli_prepare($conexion, "SELECT precio_final FROM inventario WHERE id=?");
mysqli_stmt_bind_param($stmtActual, 'i', $id);
mysqli_stmt_execute($stmtActual);
$resActual = mysqli_stmt_get_result($stmtActual);
$actual = mysqli_fetch_assoc($resActual);
mysqli_stmt_close($stmtActual);

if (!$actual) { echo json_encode(['ok'=>false,'msg'=>'Producto no encontrado']); exit; }

$precioAnteriorActual = floatval($actual['precio_final']);
$cambioPrecio = abs($precioAnteriorActual - $precio_final) > 0.001;

if ($cambioPrecio) {
    $stmt = mysqli_prepare($conexion,
        "UPDATE inventario SET nombre=?, sku=?, descripcion=?, precio_final=?, precio_anterior=?, id_departamento=? WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, 'sssddii', $nombre, $sku, $descripcion, $precio_final, $precioAnteriorActual, $id_departamento, $id);
} else {
    $stmt = mysqli_prepare($conexion,
        "UPDATE inventario SET nombre=?, sku=?, descripcion=?, precio_final=?, id_departamento=? WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, 'sssdii', $nombre, $sku, $descripcion, $precio_final, $id_departamento, $id);
}

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok'=>true]);
} else {
    echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion)]);
}
mysqli_stmt_close($stmt);
mysqli_close($conexion);