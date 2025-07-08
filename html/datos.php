<?php
// simulacion_datos_frecuentes.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "u557447082_9x8vh";
$password = "safegarden_bm9F8>y";
$dbname = "u557447082_safegardendb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// ID cliente a simular
$id_cliente = 23;

// Obtener sensores DHT11 (LoRa) para este cliente
$sensores_dht = [];
$sql = "
    SELECT s.id_sensor 
    FROM sensores s
    INNER JOIN dispositivos_lora d ON s.id_dispositivo = d.id_lora
    WHERE d.id_cliente = ? AND s.tipo_sensor = 'DHT11'
    LIMIT 4";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $sensores_dht[] = $row['id_sensor'];
}

// Obtener sensores movimiento (ESP32)
$sensores_mov = [];
$sql = "
    SELECT s.id_sensor 
    FROM sensores s
    INNER JOIN dispositivos_esp32 d ON s.id_dispositivo = d.id_esp32
    WHERE d.id_cliente = ? AND s.tipo_sensor = 'Movimiento'
    LIMIT 4";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $sensores_mov[] = $row['id_sensor'];
}

if (empty($sensores_dht) && empty($sensores_mov)) {
    die("No hay sensores para el cliente $id_cliente.");
}

// Hora actual para insertar
$fecha_actual = date('Y-m-d H:i:s');

// Insertar lecturas DHT11
foreach ($sensores_dht as $id_sensor) {
    // Simulación temperatura y humedad con ruido aleatorio
    $temp = round(20 + rand(-3, 5) + (rand(0, 10) * 0.1), 1);
    $hum = round(50 + rand(-10, 10) + (rand(0, 10) * 0.1), 1);

    $sql = "INSERT INTO registros (id_sensor, temperatura, humedad, fecha) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idds", $id_sensor, $temp, $hum, $fecha_actual);
    $stmt->execute();
}

// Insertar eventos movimiento (de 0 a 2 eventos aleatorios en el momento actual)
foreach ($sensores_mov as $id_sensor) {
    $eventos = rand(0, 2);
    for ($i = 0; $i < $eventos; $i++) {
        $offset_segundos = rand(0, 300); // Evento hasta 5 minutos atrás
        $fecha_evento = date('Y-m-d H:i:s', strtotime($fecha_actual) - $offset_segundos);

        $sql = "INSERT INTO sensor_movimiento (id_sensor, fecha) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id_sensor, $fecha_evento);
        $stmt->execute();
    }
}

echo "Datos simulados insertados a las $fecha_actual para cliente $id_cliente.";

$conn->close();
?>
