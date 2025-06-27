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
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }
    body, html {
      height: 100%;
      background: #e8f5e9;
    }
    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
      min-height: 100vh;
    }
    .card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 600px;
      padding: 40px 30px;
      position: relative;
    }
    .profile-img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #66bb6a;
      display: block;
      margin: 0 auto 20px;
    }
    h1 {
      font-size: 28px;
      color: #2e7d32;
      text-align: center;
      margin-bottom: 25px;
    }
    .info {
      margin-bottom: 16px;
      font-size: 17px;
      color: #333;
    }
    .info strong {
      color: #388e3c;
    }
    .back-link {
      text-align: center;
      margin-top: 20px;
    }
    .back-link a {
      text-decoration: none;
      color: #2e7d32;
      font-weight: bold;
      transition: 0.3s;
    }
    .back-link a:hover {
      color: #1b5e20;
    }
    .upload-section {
      text-align: center;
      margin-bottom: 20px;
    }
    .upload-section input[type="file"] {
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Mi Perfil</h1>
      <img src="uploads/default.png" alt="Foto de perfil" class="profile-img" id="fotoPerfil">

      <div class="upload-section">
        <form method="POST" action="subir_foto.php" enctype="multipart/form-data">
          <input type="file" name="foto" accept="image/*" required>
          <br>
          <button type="submit">Subir Foto</button>
        </form>
      </div>

      <div class="info"><strong>Nombre:</strong> <?= htmlspecialchars($usuario["nombre"]) ?></div>
      <div class="info"><strong>Correo:</strong> <?= htmlspecialchars($usuario["email"]) ?></div>
      <div class="info"><strong>Teléfono:</strong> <?= htmlspecialchars($usuario["telefono"]) ?></div>
      <div class="info"><strong>Ciudad:</strong> <?= htmlspecialchars($usuario["ciudad"]) ?></div>
      <div class="info"><strong>Fecha de Registro:</strong> <?= htmlspecialchars($usuario["creado_en"]) ?></div>

      <div class="back-link">
        <p><a href="dashboard.php"><i class="fas fa-arrow-left"></i> Volver al panel</a></p>
      </div>
    </div>
  </div>
</body>
</html>
