<?php
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

$hoy  = date('Y-m-d');
$mes  = date('Y-m');

// Ingresos del mes (pagos completos + abonos del mes)
$r = mysqli_query($conexion, "SELECT COALESCE(SUM(monto_cobrado),0) AS total FROM pagos WHERE activo=1 AND DATE_FORMAT(created_at,'%Y-%m')='$mes'");
$ingresos_mes = floatval(mysqli_fetch_assoc($r)['total']);

// Cotizaciones
$r = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM cotizaciones WHERE activo=1");
$total_cots = intval(mysqli_fetch_assoc($r)['total']);

$r = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM cotizaciones WHERE activo=1 AND DATE_FORMAT(created_at,'%Y-%m')='$mes'");
$cots_mes = intval(mysqli_fetch_assoc($r)['total']);

// Facturas
$r = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM facturas WHERE activo=1");
$total_facs = intval(mysqli_fetch_assoc($r)['total']);

$r = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM facturas WHERE activo=1 AND DATE_FORMAT(created_at,'%Y-%m')='$mes'");
$facs_mes = intval(mysqli_fetch_assoc($r)['total']);

// Contactos
$r = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM clientes WHERE activo=1");
$total_contactos = intval(mysqli_fetch_assoc($r)['total']);

// Productos
$r = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM inventario WHERE activo=1");
$total_productos = intval(mysqli_fetch_assoc($r)['total']);

// Ingresos por mes (últimos 12 meses)
$ingresos_chart = [];
for ($i = 11; $i >= 0; $i--) {
    $m = date('Y-m', strtotime("-$i months"));
    $r = mysqli_query($conexion, "SELECT COALESCE(SUM(monto_cobrado),0) AS total FROM pagos WHERE activo=1 AND DATE_FORMAT(created_at,'%Y-%m')='$m'");
    $ingresos_chart[] = floatval(mysqli_fetch_assoc($r)['total']);
}

// Estado cotizaciones (pagado, pendiente, sin pago)
$r = mysqli_query($conexion, "SELECT estatus_pago, COUNT(*) AS c FROM cotizaciones WHERE activo=1 GROUP BY estatus_pago");
$est_cots = ['pagado'=>0, 'parcial'=>0, 'pendiente'=>0];
while ($row = mysqli_fetch_assoc($r)) {
    $est_cots[$row['estatus_pago'] ?? 'pendiente'] = intval($row['c']);
}

// Actividad reciente (últimas 8 entre cotizaciones, facturas y pagos)
$actividad = [];

$r = mysqli_query($conexion, "SELECT 'cotizacion' AS tipo, folio, nombre_cliente, total, created_at FROM cotizaciones WHERE activo=1 ORDER BY created_at DESC LIMIT 4");
while ($row = mysqli_fetch_assoc($r)) $actividad[] = $row;

$r = mysqli_query($conexion, "SELECT 'factura' AS tipo, folio, nombre_cliente, total, created_at FROM facturas WHERE activo=1 ORDER BY created_at DESC LIMIT 4");
while ($row = mysqli_fetch_assoc($r)) $actividad[] = $row;

usort($actividad, function($a, $b) { return strcmp($b['created_at'], $a['created_at']); });
$actividad = array_slice($actividad, 0, 8);

echo json_encode([
    'ok'              => true,
    'ingresos_mes'    => $ingresos_mes,
    'total_cots'      => $total_cots,
    'cots_mes'        => $cots_mes,
    'total_facs'      => $total_facs,
    'facs_mes'        => $facs_mes,
    'total_contactos' => $total_contactos,
    'total_productos' => $total_productos,
    'ingresos_chart'  => $ingresos_chart,
    'est_cots'        => $est_cots,
    'actividad'       => $actividad
]);

mysqli_close($conexion);