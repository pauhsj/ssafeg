<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de conexión
$servername = "localhost";
$username = "u557447082_9x8vh";
$password = '$afegarden_bm9F8>y';
$dbname = "u557447082_safegardedb";

// Obtener los parámetros del sensor
$id_sensor = $_GET['id_sensor'] ?? null;
$temperatura = $_GET['temperatura'] ?? null;
$humedad = $_GET['humedad'] ?? null;

if ($id_sensor !== null && $temperatura !== null && $humedad !== null) {
    // Conectar a la base de datos
    $conexion = new mysqli($servername, $username, $password, $dbname);
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Insertar los datos con id_sensor
    $stmt = $conexion->prepare("INSERT INTO registros (id_sensor, temperatura, humedad, fecha) VALUES (?, ?, ?, NOW())");
    if ($stmt === false) {
        die("Error en prepare: " . $conexion->error);
    }

    $stmt->bind_param("idd", $id_sensor, $temperatura, $humedad);

    if ($stmt->execute()) {
        echo "Datos insertados correctamente.";
    } else {
        echo "Error al insertar: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
} else {
    echo "Faltan parámetros: id_sensor, temperatura o humedad.";
}
?>
