<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['id_user']) || ($_SESSION['id_rol'] ?? 2) != 1) {
    echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
    exit;
}
