<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "u557447082_9x8vh";
$password ='$afegarden_bm9F8>y';
$dbname = "u557447082_safegardedb";
$conexion = new mysqli($servername, $username, $password, $dbname);

$movimiento = $_GET['movimiento'] ?? null;  // Se espera un valor 1 (detectado) o 0 (no)

if ($movimiento !== null) {
    $conexion = new mysqli($servername, $username, $password, $dbname);

    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    $stmt = $conexion->prepare("INSERT INTO sensor_movimiento (movimiento, fecha) VALUES (?, NOW())");
    $stmt->bind_param("i", $movimiento);

    if ($stmt->execute()) {
        echo "Movimiento registrado.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
} else {
    echo "Falta el parámetro 'movimiento'.";
}
?>
