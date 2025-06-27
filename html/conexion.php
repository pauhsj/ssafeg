<?php 
$servername = "localhost";
$username = "u557447082_9x8vh";
$password ="safegarden_bm9F8>y";
$dbname = "u557447082_safegardendb";
$conn = new mysqli($servername, $username, $password, $dbname);



// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
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