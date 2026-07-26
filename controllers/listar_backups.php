<?php
require_once __DIR__ . '/../includes/auth_admin_json.php';

$dirBackups = __DIR__ . '/../backups/';
$archivos = glob($dirBackups . 'backup_*.sql');
usort($archivos, function($a, $b) { return filemtime($b) - filemtime($a); });

$items = array_map(function($ruta) {
    return [
        'nombre' => basename($ruta),
        'tamano' => round(filesize($ruta) / 1024, 1) . ' KB',
        'fecha'  => date('d/m/Y H:i', filemtime($ruta)),
    ];
}, $archivos);

echo json_encode(['ok' => true, 'items' => $items]);
