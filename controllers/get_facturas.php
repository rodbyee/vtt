<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

$res = mysqli_query($conexion,
    "SELECT id, folio, id_cliente, nombre_cliente, rfc, razon_social, uso_cfdi,
            incluir_iva, subtotal, iva, total, estatus, created_at
     FROM facturas WHERE activo = 1 ORDER BY created_at DESC"
);

$data = [];
while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
echo json_encode($data);
mysqli_close($conexion);