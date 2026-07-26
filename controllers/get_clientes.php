<?php
require_once __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json');

$resultado = mysqli_query($conexion,
    "SELECT id, nombre, alias, razon_social, telefono, correo, direccion, rfc, codigo_postal, regimen_fiscal, notas, activo, created_at
     FROM clientes
     WHERE activo = 1
     ORDER BY created_at DESC"
);

$clientes = [];
while ($row = mysqli_fetch_assoc($resultado)) {
    $clientes[] = $row;
}

echo json_encode($clientes);
mysqli_close($conexion);