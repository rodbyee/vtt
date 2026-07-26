<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit;
}

$nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
$id_cliente     = intval($_POST['id_cliente'] ?? 0) ?: null;
$uso_cfdi       = trim($_POST['uso_cfdi'] ?? 'G03');
$notas          = trim($_POST['notas'] ?? '') ?: null;
$items          = json_decode($_POST['items'] ?? '[]', true);

if ($nombre_cliente === '') { echo json_encode(['ok'=>false,'msg'=>'El nombre del cliente es requerido']); exit; }
if (empty($items))          { echo json_encode(['ok'=>false,'msg'=>'Agrega al menos un producto o servicio']); exit; }

// Datos fiscales del cliente
$rfc = ''; $razon_social = ''; $regimen_fiscal = ''; $codigo_postal = '';
if ($id_cliente) {
    $rc = mysqli_query($conexion, "SELECT rfc, razon_social, regimen_fiscal, codigo_postal FROM clientes WHERE id = $id_cliente");
    $cl = mysqli_fetch_assoc($rc);
    if ($cl) {
        $rfc            = $cl['rfc']            ?? '';
        $razon_social   = $cl['razon_social']   ?? '';
        $regimen_fiscal = $cl['regimen_fiscal']  ?? '';
        $codigo_postal  = $cl['codigo_postal']  ?? '';
    }
}

// Folio automático
$res   = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM facturas");
$row   = mysqli_fetch_assoc($res);
$folio = 'FAC-' . str_pad($row['total'] + 1, 4, '0', STR_PAD_LEFT);

// Los precios de cada item YA incluyen IVA (igual que en cotizaciones).
// El total final es la suma directa; subtotal/iva son solo el desglose informativo (total / 1.16).
$total = 0;
foreach ($items as $item) {
    $total += floatval($item['precio_unitario']) * intval($item['cantidad']);
}
$subtotal    = round($total / 1.16, 2);
$iva         = round($total - $subtotal, 2);
$incluir_iva = 1; // El IVA siempre va implícito en el precio

// Insertar factura
$stmt = mysqli_prepare($conexion,
    "INSERT INTO facturas (folio, id_cliente, nombre_cliente, rfc, razon_social, regimen_fiscal, codigo_postal, uso_cfdi, incluir_iva, subtotal, iva, total, notas)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, 'sissssssiddds',
    $folio, $id_cliente, $nombre_cliente, $rfc, $razon_social, $regimen_fiscal, $codigo_postal,
    $uso_cfdi, $incluir_iva, $subtotal, $iva, $total, $notas
);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['ok'=>false,'msg'=>mysqli_error($conexion)]); exit;
}

$id_factura = mysqli_insert_id($conexion);

// Insertar items
$stmt2 = mysqli_prepare($conexion,
    "INSERT INTO factura_items (id_factura, tipo, nombre, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)"
);
foreach ($items as $item) {
    $tipo     = $item['tipo'] === 'servicio' ? 'servicio' : 'producto';
    $nombre   = trim($item['nombre']);
    $cantidad = intval($item['cantidad']);
    $precio   = floatval($item['precio_unitario']);
    $sub_item = $precio * $cantidad;
    mysqli_stmt_bind_param($stmt2, 'issids', $id_factura, $tipo, $nombre, $cantidad, $precio, $sub_item);
    mysqli_stmt_execute($stmt2);
}

echo json_encode(['ok'=>true, 'id'=>$id_factura, 'folio'=>$folio]);
mysqli_close($conexion);