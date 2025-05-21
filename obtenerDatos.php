
<?php
session_start();
require("conexion.php");

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

// Obtener el último registro
$sql = "SELECT temperatura, humedad, fecha FROM registros ORDER BY fecha DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(["error" => "No hay datos disponibles"]);
}

$conn->close();
?>

