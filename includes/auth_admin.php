<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user'])) { header('Location: index.php'); exit; }
if (($_SESSION['id_rol'] ?? 2) != 1) { header('Location: dashboard.php'); exit; }
