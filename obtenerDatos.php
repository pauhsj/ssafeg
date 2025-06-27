<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión
include_once 'conexion.php';           

$conn = new mysqli($servername, $username, $password, $dbname);

// Datos recibidos
$temperatura = $_GET['temperatura'] ?? null;
$humedad = $_GET['humedad'] ?? null;
$id_sensor = $_GET['id_sensor'] ?? null; // <- Nuevo

if ($temperatura !== null && $humedad !== null && $id_sensor !== null) {
    $stmt = $conexion->prepare("INSERT INTO registros (id_sensor, temperatura, humedad, fecha_registro) VALUES (?, ?, ?, NOW())");
    if ($stmt === false) {
        die("Error en prepare: " . $conexion->error);
    }

    $stmt->bind_param("idd", $id_sensor, $temperatura, $humedad);

    if ($stmt->execute()) {
        echo "✅ Registro guardado correctamente para sensor ID $id_sensor.";
    } else {
        echo "❌ Error al guardar: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "⚠️ Faltan parámetros: temperatura, humedad o id_sensor.";
}

$conexion->close();
?>
