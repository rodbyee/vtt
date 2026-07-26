<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit; }

$nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
$id_cliente     = intval($_POST['id_cliente'] ?? 0) ?: null;
$notas          = trim($_POST['notas'] ?? '') ?: null;
$items          = json_decode($_POST['items'] ?? '[]', true);

if ($nombre_cliente === '') { echo json_encode(['ok'=>false,'msg'=>'El nombre del cliente es requerido']); exit; }
if (empty($items))          { echo json_encode(['ok'=>false,'msg'=>'Agrega al menos un producto o servicio']); exit; }

// Folio automático
$res   = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM cotizaciones");
$row   = mysqli_fetch_assoc($res);
$folio = 'COT-' . str_pad($row['total'] + 1, 4, '0', STR_PAD_LEFT);

// El precio_unitario YA incluye IVA (viene de inventario o capturado directo).
// El total es simplemente la suma — no se vuelve a sumar IVA.
$total = 0;
foreach ($items as $item) {
    $total += floatval($item['precio_unitario']) * intval($item['cantidad']);
}

// Desglose informativo (no afecta el total)
$subtotal = round($total / 1.16, 2);
$iva      = round($total - $subtotal, 2);
$incluir_iva = 1; // el IVA siempre está incluido en el precio ahora

$stmt = mysqli_prepare($conexion,
    "INSERT INTO cotizaciones (folio, nombre_cliente, id_cliente, incluir_iva, subtotal, iva, total, notas)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, 'ssiiddds', $folio, $nombre_cliente, $id_cliente, $incluir_iva, $subtotal, $iva, $total, $notas);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion)]); exit;
}

$id_cotizacion = mysqli_insert_id($conexion);

// Insertar items (precio_unitario y subtotal tal cual, con IVA incluido)
$stmt2 = mysqli_prepare($conexion,
    "INSERT INTO cotizacion_items (id_cotizacion, tipo, nombre, cantidad, precio_unitario, subtotal)
     VALUES (?, ?, ?, ?, ?, ?)"
);

foreach ($items as $item) {
    $tipo       = $item['tipo'] === 'servicio' ? 'servicio' : 'producto';
    $nombre     = trim($item['nombre']);
    $cantidad   = intval($item['cantidad']);
    $precio     = floatval($item['precio_unitario']);
    $sub_item   = $precio * $cantidad;
    mysqli_stmt_bind_param($stmt2, 'issids', $id_cotizacion, $tipo, $nombre, $cantidad, $precio, $sub_item);
    mysqli_stmt_execute($stmt2);
}

echo json_encode(['ok'=>true, 'id'=>$id_cotizacion, 'folio'=>$folio]);
mysqli_close($conexion);