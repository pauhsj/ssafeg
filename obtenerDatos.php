<?php
$servername = "localhost";
$username = "root";
$password = "120994knj";
$dbname = "safegardendb_local";

// Obtener los datos desde la URL
$temperatura = $_GET['temperatura'] ?? null;
$humedad = $_GET['humedad'] ?? null;

if ($temperatura !== null && $humedad !== null) {
    // Crear conexión
    $conexion = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Insertar datos
    $sql = "INSERT INTO registros (temperatura, humedad, fecha) VALUES ('$temperatura', '$humedad', NOW())";

    if ($conexion->query($sql) === TRUE) {
        echo "Datos insertados correctamente.";
    } else {
        echo "Error: " . $conexion->error;
    }

    $conexion->close();
} else {
    echo "Faltan parámetros de temperatura o humedad.";
}
?>
