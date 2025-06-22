<?php
$servername = "localhost";
$username   = "u557447082_9x8vh";
$password   = '$afegarden_bm9F8>y';
$dbname     = "u557447082_safegardedb";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre     = $_POST['nombre'] ?? '';
  $email      = $_POST['email'] ?? '';
  $contraseña = $_POST['contraseña'] ?? '';
  $telefono   = $_POST['telefono'] ?? '';
  $ciudad     = $_POST['ciudad'] ?? '';

  $stmt = $conn->prepare("INSERT INTO usuario (nombre, email, contraseña, telefono, ciudad, creado_en) VALUES (?, ?, ?, ?, ?, NOW())");

  if (!$stmt) {
    die("Error en prepare(): " . $conn->error);
  }

  $stmt->bind_param("sssss", $nombre, $email, $contraseña, $telefono, $ciudad);

  if ($stmt->execute()) {
    header("Location: login.php");
    exit;
  } else {
    echo "Error al registrar: " . $stmt->error;
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrarse | SafeGarden</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      background: #f1f8e9;
    }

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
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 900px;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    @media (min-width: 768px) {
      .card {
        flex-direction: row;
      }
    }

    .left {
      background-color: #dcedc8;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 30px;
      text-align: center;
    }

    .left img {
      width: 100%;
      max-width: 300px;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .left h1 {
      font-size: 26px;
      color: #33691e;
    }

    .right {
      flex: 1;
      padding: 40px;
    }

    .form h2 {
      font-size: 24px;
      color: #2e7d32;
      margin-bottom: 10px;
      text-align: center;
    }

    .form p {
      color: #666;
      text-align: center;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .form input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .form button {
      width: 100%;
      padding: 12px;
      background-color: #388e3c;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .form button:hover {
      background-color: #2e7d32;
    }

    .form .login-link {
      text-align: center;
      margin-top: 10px;
      font-size: 14px;
    }

    .form .login-link a {
      color: #2e7d32;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="left">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSyqTieVYbildQV61aIF0sawJOBRSzflKWFSw&s" alt="Herramientas de jardín">
        <h1>Cuida tu jardín como cuidas de ti</h1>
      </div>
      <div class="right">
        <form class="form" method="POST" action="registrouser.php">
          <h2>Crea tu cuenta gratuita</h2>
          <p>Ingresa tus datos para registrarte</p>

          <input type="text" name="nombre" placeholder="Nombre completo" required />
          <input type="email" name="email" placeholder="Correo electrónico" required />
          <input type="password" name="contraseña" placeholder="Contraseña" required />
          <input type="text" name="telefono" placeholder="Teléfono" required />
          <input type="text" name="ciudad" placeholder="Ciudad" required />

          <button type="submit">Registrarme</button>

          <div class="login-link">
            <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
