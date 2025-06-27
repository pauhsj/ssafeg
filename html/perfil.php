<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$servername = "localhost";
$username = "u557447082_9x8vh";
$password = "safegarden_bm9F8>y";
$dbname = "u557447082_safegardendb";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id = $_SESSION["id_cliente"];
$sql = "SELECT nombre, email, telefono, ciudad, creado_en FROM usuario WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();
} else {
    echo "Usuario no encontrado.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mi Perfil | SafeGarden</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    * { box-sizing: border-box; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; }
    body, html { height: 100%; background: #f1f8e9; }
    .container {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      padding: 20px;
      justify-content: center;
      align-items: center;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 600px;
      overflow: hidden;
      padding: 30px;
    }
    h1 {
      font-size: 26px;
      background: linear-gradient(to right, #2e7d32, #66bb6a);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      color: transparent;
      text-align: center;
      margin-bottom: 25px;
    }
    .info {
      margin-bottom: 15px;
      font-size: 16px;
    }
    .info strong {
      color: #2e7d32;
    }
    .back-link {
      text-align: center;
      margin-top: 15px;
    }
    .back-link a {
      text-decoration: none;
      color: #2e7d32;
      font-weight: bold;
      transition: color 0.3s;
    }
    .back-link a:hover {
      color: #1b5e20;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Mi Perfil</h1>
      <div class="info"><strong>Nombre:</strong> <?= htmlspecialchars($usuario["nombre"]) ?></div>
      <div class="info"><strong>Correo:</strong> <?= htmlspecialchars($usuario["email"]) ?></div>
      <div class="info"><strong>Teléfono:</strong> <?= htmlspecialchars($usuario["telefono"]) ?></div>
      <div class="info"><strong>Ciudad:</strong> <?= htmlspecialchars($usuario["ciudad"]) ?></div>
      <div class="info"><strong>Fecha de Registro:</strong> <?= htmlspecialchars($usuario["creado_en"]) ?></div>

      <div class="back-link">
        <p><a href="html/dashboard.php"><i class="fas fa-arrow-left"></i> Volver al panel</a></p>
      </div>
    </div>
  </div>
</body>
</html>