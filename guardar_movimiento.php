<?php
require_once 'html/conexion.php';


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$id_sensor = $_GET['id_sensor'] ?? null;
$evento = $_GET['evento'] ?? null;

if ($id_sensor && $evento) {
    $stmt = $conn->prepare("INSERT INTO sensor_movimiento (id_sensor, evento) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_sensor, $evento);

    if ($stmt->execute()) {
        echo "Evento registrado correctamente.";
    } else {
        echo "Error al insertar.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Faltan parámetros.";
}
?>
