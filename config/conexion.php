<?php

$conexion=mysqli_connect("localhost","root","","vtt");
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>