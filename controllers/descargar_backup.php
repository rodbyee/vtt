<?php
require_once __DIR__ . '/../includes/auth_admin.php'; // aquí sí redirige, no es JSON

$nombre = basename($_GET['file'] ?? '');

// Solo permitir el patrón exacto que nosotros generamos
if (!preg_match('/^backup_[0-9\-_]+\.sql$/', $nombre)) {
    header('Location: ../respaldos.php');
    exit;
}

$ruta = __DIR__ . '/../backups/' . $nombre;
if (!file_exists($ruta)) {
    header('Location: ../respaldos.php');
    exit;
}

header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $nombre . '"');
header('Content-Length: ' . filesize($ruta));
readfile($ruta);
exit;
