<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok'=>false]); exit; }

$id            = intval($_POST['id'] ?? 0);
$id_cotizacion = intval($_POST['id_cotizacion'] ?? 0);
$folio_doc     = trim($_POST['folio_doc'] ?? '');

$stmt = mysqli_prepare($conexion, "UPDATE pagos SET id_cotizacion=?, folio_doc=? WHERE id=?");
mysqli_stmt_bind_param($stmt, 'isi', $id_cotizacion, $folio_doc, $id);
mysqli_stmt_execute($stmt) ? json_encode(['ok'=>true]) : json_encode(['ok'=>false]);
echo json_encode(['ok'=>true]);
mysqli_close($conexion);