<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

$res = mysqli_query($conexion,
    "SELECT i.id, i.nombre, i.sku, i.descripcion, i.precio_final, i.precio_anterior, i.id_departamento,
            d.nombre AS dep_nombre, d.color AS dep_color
     FROM inventario i
     LEFT JOIN departamentos d ON d.id = i.id_departamento AND d.activo = 1
     WHERE i.activo = 1
     ORDER BY i.created_at DESC"
);

$items = [];
while ($row = mysqli_fetch_assoc($res)) $items[] = $row;
echo json_encode($items);
mysqli_close($conexion);