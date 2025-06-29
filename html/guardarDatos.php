<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "safegardenbd_local";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $id_sensor = intval($_GET['id_sensor'] ?? 0);
    $temperatura = floatval($_GET['temperatura'] ?? 0);
    $humedad = floatval($_GET['humedad'] ?? 0);

    if ($id_sensor > 0) {
        $stmt = $conn->prepare("INSERT INTO registros (id_sensor, temperatura, humedad, fecha) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("idd", $id_sensor, $temperatura, $humedad);
        if ($stmt->execute()) {
            echo "OK";
        } else {
            echo "ERROR DB: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Falta id_sensor";
    }
}
?>
