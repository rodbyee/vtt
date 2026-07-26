<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

$res = mysqli_query($conexion,
    "SELECT id, tipo, modalidad, nombre_cliente, metodo, monto, monto_cobrado,
            mensualidades, monto_mensualidad, total_diferido,
            referencia, notas, recordatorio, telefono_recordatorio,
            id_factura, id_cotizacion, folio_doc, created_at
     FROM pagos WHERE activo = 1 ORDER BY created_at DESC"
);

$data = [];
while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
echo json_encode($data);
mysqli_close($conexion);