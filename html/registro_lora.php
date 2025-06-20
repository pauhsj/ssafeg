<?php
$servername = "localhost";
$username = "u557447082_9x8vh";
$password = '$afegarden_bm9F8>y';
$dbname = "u557447082_safegardedb";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_dispositivo = $_POST['nombre'];
    $descripcion_dispositivo = $_POST['descripcion'];
    $ubicacion = $_POST['ubicacion'] ?? "No especificada";
    $codigo_lora = $_POST['codigo_lora'] ?? uniqid('LoRa_'); // Generar uno si no lo mandas
    $id_usuario = $_POST['id_usuario'];

    // Insertar en Dispositivos_LoRa
    $stmt = $conn->prepare("INSERT INTO Dispositivos_LoRa (nombre_dispositivo, descripcion, ubicacion, codigo_lora, id_usuario) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombre_dispositivo, $descripcion_dispositivo, $ubicacion, $codigo_lora, $id_usuario);

    if ($stmt->execute()) {
        $id_lora = $conn->insert_id; // ID del nuevo dispositivo

        // Insertar sensor por defecto relacionado
        $tipo_sensor = "DHT11";
        $descripcion_sensor = "Sensor de temperatura y humedad por defecto";

        $stmt_sensor = $conn->prepare("INSERT INTO Sensores (tipo_sensor, descripcion, id_lora) VALUES (?, ?, ?)");
        $stmt_sensor->bind_param("ssi", $tipo_sensor, $descripcion_sensor, $id_lora);

        if ($stmt_sensor->execute()) {
            echo json_encode(["success" => true, "message" => "LoRa y sensor registrados"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al insertar sensor"]);
        }

        $stmt_sensor->close();
    } else {
        echo json_encode(["success" => false, "message" => "Error al insertar dispositivo LoRa"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
}
?>
