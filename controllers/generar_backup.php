<?php
require_once __DIR__ . '/../includes/auth_admin_json.php';
require_once __DIR__ . '/../config/conexion.php';

$tablas = require __DIR__ . '/../config/tablas_papelera.php';
$dirBackups = __DIR__ . '/../backups/';
if (!is_dir($dirBackups)) mkdir($dirBackups, 0755, true);

$nombreArchivo = 'backup_' . date('Y-m-d_His') . '.sql';
$ruta = $dirBackups . $nombreArchivo;

$fh = fopen($ruta, 'w');
if (!$fh) {
    echo json_encode(['ok' => false, 'msg' => 'No se pudo crear el archivo de respaldo']);
    exit;
}

fwrite($fh, "-- Respaldo Villa Tecnia (VTT)\n-- Generado: " . date('Y-m-d H:i:s') . "\n\n");
fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n\n");

$LOTE = 500; // filas por bloque, para no cargar todo en memoria de golpe

foreach ($tablas as $tabla => $label) {

    fwrite($fh, "-- ──────────────────────────────\n-- Tabla: $tabla\n-- ──────────────────────────────\n");

    // Estructura de la tabla
    $crear = mysqli_query($conexion, "SHOW CREATE TABLE `$tabla`");
    if (!$crear) { fwrite($fh, "-- (no se pudo leer la estructura: " . mysqli_error($conexion) . ")\n\n"); continue; }
    $filaCrear = mysqli_fetch_assoc($crear);
    fwrite($fh, "DROP TABLE IF EXISTS `$tabla`;\n");
    fwrite($fh, $filaCrear['Create Table'] . ";\n\n");

    // Datos, en bloques
    $totalRes = mysqli_query($conexion, "SELECT COUNT(*) AS n FROM `$tabla`");
    $total = mysqli_fetch_assoc($totalRes)['n'];

    for ($offset = 0; $offset < $total; $offset += $LOTE) {
        $res = mysqli_query($conexion, "SELECT * FROM `$tabla` LIMIT $LOTE OFFSET $offset");
        while ($fila = mysqli_fetch_assoc($res)) {
            $cols = array_map(function($c) { return "`$c`"; }, array_keys($fila));
            $vals = array_map(function($v) use ($conexion) {
                if ($v === null) return 'NULL';
                return "'" . mysqli_real_escape_string($conexion, $v) . "'";
            }, array_values($fila));
            fwrite($fh, "INSERT INTO `$tabla` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ");\n");
        }
    }
    fwrite($fh, "\n");
}

fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($fh);

// Conservar solo los últimos 10 respaldos para no saturar el disco del hosting
$archivos = glob($dirBackups . 'backup_*.sql');
usort($archivos, function($a, $b) { return filemtime($b) - filemtime($a); });
foreach (array_slice($archivos, 10) as $viejo) unlink($viejo);

echo json_encode(['ok' => true, 'archivo' => $nombreArchivo]);
mysqli_close($conexion);
