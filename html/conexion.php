<?php 
$servername = "localhost";
$username = "u557447082_9x8vh";
$password ='$afegarden_bm9F8>y';
$dbname = "u557447082_safegardedb";
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