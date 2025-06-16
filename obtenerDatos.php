<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de conexión
$servername = "localhost";
$username = "u557447082_9x8vh";
$password = '$afegarden_bm9F8>y';
$dbname = "u557447082_safegardedb";

// Obtener los parámetros
$temperatura = $_GET['temperatura'] ?? null;
$humedad = $_GET['humedad'] ?? null;

if ($temperatura !== null && $humedad !== null) {
    // Conectar a la base de datos
    $conexion = new mysqli($servername, $username, $password, $dbname);

    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Insertar los datos de forma segura
    $stmt = $conexion->prepare("INSERT INTO registros (temperatura, humedad) VALUES (?, ?)");
    if ($stmt === false) {
        die("Error en prepare: " . $conexion->error);
    }

    $stmt->bind_param("dd", $temperatura, $humedad);

    if ($stmt->execute()) {
        echo "Datos insertados correctamente.";
    } else {
        echo "Error al insertar: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
} else {
    echo "Faltan parámetros de temperatura o humedad.";
}
?>
