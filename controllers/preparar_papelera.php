<?php
require_once __DIR__ . '/../includes/auth_admin_json.php';
require_once __DIR__ . '/../config/conexion.php';

$tablas = require __DIR__ . '/../config/tablas_papelera.php';
$reporte = [];

foreach ($tablas as $tabla => $label) {

    // ¿Existe la columna 'activo'?
    $col = mysqli_query($conexion, "
        SELECT COUNT(*) AS n FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . mysqli_real_escape_string($conexion, $tabla) . "'
        AND COLUMN_NAME = 'activo'
    ");
    $tieneActivo = mysqli_fetch_assoc($col)['n'] > 0;

    if (!$tieneActivo) {
        mysqli_query($conexion, "ALTER TABLE `$tabla` ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1");
        $reporte[] = "$label: se agregó columna 'activo'";
    }

    // ¿Existe la columna 'deleted_at'?
    $col2 = mysqli_query($conexion, "
        SELECT COUNT(*) AS n FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . mysqli_real_escape_string($conexion, $tabla) . "'
        AND COLUMN_NAME = 'deleted_at'
    ");
    $tieneDeletedAt = mysqli_fetch_assoc($col2)['n'] > 0;

    if (!$tieneDeletedAt) {
        mysqli_query($conexion, "ALTER TABLE `$tabla` ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL");
        $reporte[] = "$label: se agregó columna 'deleted_at'";
    }

    if ($tieneActivo && $tieneDeletedAt) {
        $reporte[] = "$label: ya estaba lista";
    }
}

echo json_encode(['ok' => true, 'reporte' => $reporte]);
mysqli_close($conexion);
