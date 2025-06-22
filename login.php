<?php
session_start();

// Conexión a la base de datos
$servername = "localhost";
$username   = "u557447082_9x8vh";
$password   = '$afegarden_bm9F8>y';
$dbname     = "u557447082_safegardedb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';

    // Buscar el usuario por correo
    $sql = "SELECT * FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            // Comparar contraseñas (sin hash)
            if ($usuario['contraseña'] === $clave) {
                $_SESSION['id_usuario'] = $usuario['id_cliente'];
                $_SESSION['nombre'] = $usuario['nombre'];
                header("Location: html/dashboard.php");
                exit;
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Correo no registrado.";
        }
    } else {
        $error = "Error en la consulta.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión | SafeGarden</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }

    body, html { height: 100%; background: #e8f5e9; }

    .container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      width: 100%;
      max-width: 900px;
      overflow: hidden;
    }

    .card-content {
      display: flex;
      flex-direction: column;
    }

    @media (min-width: 768px) {
      .card-content { flex-direction: row; }
    }

    .left {
      flex: 1;
      background-color: #dcedc8;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 30px;
    }

    .left img {
      width: 100%;
      max-width: 300px;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .left h1 {
      font-size: 26px;
      color: #2e7d32;
    }

    .right {
      flex: 1;
      padding: 40px;
    }

    .right h2 {
      font-size: 24px;
      color: #2e7d32;
      margin-bottom: 10px;
    }

    .right p {
      color: #666;
      font-size: 14px;
      margin-bottom: 20px;
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
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .form button:hover {
      background-color: #2e7d32;
    }

    .options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
      margin-bottom: 10px;
    }

    .options a {
      color: #388e3c;
      text-decoration: none;
    }

    .register {
      text-align: center;
      font-size: 14px;
      margin-top: 10px;
    }

    .register a {
      color: #388e3c;
      text-decoration: none;
    }

    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="card">
    <div class="card-content">

      <div class="right">
        <form class="form" method="POST" action="login.php">
          <h2>¡Bienvenido de nuevo a SafeGarden!</h2>
          <p>Inicia sesión en tu cuenta</p>

          <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

          <input type="email" name="correo" placeholder="Tu correo electrónico" required>
          <input type="password" name="clave" placeholder="Contraseña" required>

          <div class="options">
            <label><input type="checkbox"> Recuérdame</label>
            <a href="#">¿Olvidaste tu contraseña?</a>
          </div>

          <button type="submit">Iniciar sesión</button>

          <div class="register">
            <p>¿No tienes cuenta? <a href="registrouser.php">Regístrate</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
