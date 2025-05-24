<?php 
$servername = "localhost";
$username = "root";
$password ="120994knj";
$dbname = "safegardendb_local";
$conexion = new mysqli($servername, $username, $password, $dbname);



// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}


/*

USAR ESTE SOLO PARA PRUEBAS LOCALES

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "safegardenbd_local";
$conexion = new mysqli($servername, $username, $password, $dbname);
if ($conexion->connect_error) {
    die("Conexion fallida" . $conexion->connect_error);
}

*/