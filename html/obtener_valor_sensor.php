<?php
$servername = "localhost";
$username = "u557447082_9x8vh";
$password = "safegarden_bm9F8>y";
$dbname = "u557447082_safegardendb";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$id = intval($_GET['id_sensor'] ?? 0);
$tipo = $_GET['tipo'] ?? '';

header('Content-Type: application/json');

if ($tipo === 'temp' || $tipo === 'hum') {
    $sql = "SELECT temperatura, humedad FROM registros WHERE id_sensor = $id ORDER BY fecha DESC LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        echo json_encode($res->fetch_assoc());
    } else {
        echo json_encode(['temperatura' => null, 'humedad' => null]);
    }
} elseif ($tipo === 'mov') {
    $sql = "SELECT COUNT(*) AS total_eventos FROM sensor_movimiento WHERE id_sensor = $id AND DATE(fecha) = CURDATE()";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    echo json_encode(['total_eventos' => $row['total_eventos'] ?? 0]);
} else {
    echo json_encode(['error' => 'Tipo inválido']);
}
?>
