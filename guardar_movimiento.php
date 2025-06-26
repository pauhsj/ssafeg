<?php
$servername = "localhost";
$username = "u557447082_9x8vh";
$password = '$afegarden_bm9F8>y';
$dbname = "u557447082_safegardedb";

$id_sensor = $_GET['id_sensor'] ?? null;
$evento = $_GET['evento'] ?? null;

if ($id_sensor && $evento) {
    $stmt = $conn->prepare("INSERT INTO sensor_movimiento (id_sensor, evento) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_sensor, $evento);
    
    if ($stmt->execute()) {
        echo "Evento registrado correctamente";
    } else {
        echo "Error al guardar";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Faltan parámetros";
}
?>
