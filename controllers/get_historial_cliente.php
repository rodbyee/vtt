<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

$nombre = trim($_GET['nombre'] ?? '');
if ($nombre === '') { echo json_encode(['ok'=>false,'msg'=>'Nombre requerido']); exit; }

$nombre_esc = mysqli_real_escape_string($conexion, $nombre);

// Cotizaciones
$res = mysqli_query($conexion,
    "SELECT 'cotizacion' AS tipo, id, folio, nombre_cliente, total, created_at
     FROM cotizaciones
     WHERE activo = 1 AND nombre_cliente LIKE '%$nombre_esc%'
     ORDER BY created_at DESC"
);
$items = [];
while ($row = mysqli_fetch_assoc($res)) $items[] = $row;

// Remisiones (pagos tipo remision)
$res2 = mysqli_query($conexion,
    "SELECT 'remision' AS tipo, id, folio_doc AS folio, nombre_cliente,
            monto AS total, modalidad, monto_cobrado, created_at
     FROM pagos
     WHERE activo = 1 AND tipo = 'remision' AND nombre_cliente LIKE '%$nombre_esc%'
     ORDER BY created_at DESC"
);
while ($row = mysqli_fetch_assoc($res2)) $items[] = $row;

// Ordenar por fecha desc
usort($items, function($a, $b) {
    return strcmp($b['created_at'], $a['created_at']);
});

echo json_encode(['ok'=>true, 'data'=>$items]);
mysqli_close($conexion);