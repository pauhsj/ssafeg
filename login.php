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
  <title> Iniciar Sesión | SafeGarden</title>

  <meta name="description" content="" />

  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/logoSG.png" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Arial', sans-serif;
    }

    body, html {
      height: 100%;
    }

    .container {
      display: flex;
      height: 100vh;
    }

    .left-side {
      flex: 1;
      background-color: #f7f7f7;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
      text-align: center;
    }

    .left-side img {
      width: 100%;
      max-width: 400px;
      border-radius: 10px;
      margin-top: 20px;
    }

    .left-side h1 {
      font-size: 32px;
      margin-bottom: 10px;
    }

    .left-side p {
      font-size: 16px;
      color: #666;
      margin-bottom: 20px;
    }

    .right-side {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #fff;
    }

    .login-form {
      width: 100%;
      max-width: 350px;
    }

    .login-form h2 {
      margin-bottom: 10px;
      font-size: 28px;
      text-align: center;
    }

    .login-form p {
      margin-bottom: 20px;
      color: #777;
      text-align: center;
      font-size: 14px;
    }

    .login-form input[type="email"],
    .login-form input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .login-form .options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .login-form .options label {
      display: flex;
      align-items: center;
      font-size: 14px;
    }

    .login-form .options a {
      font-size: 14px;
      color: #467e48;
      text-decoration: none;
    }

    .login-form button.login-btn {
      width: 100%;
      padding: 12px;
      background-color: #366738;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      margin-bottom: 15px;
    }

    .register {
      text-align: center;
      margin-top: 10px;
      font-size: 14px;
    }

    .register a {
      color: #3c7b3e;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="left-side">
    <h1>Cuida tu jardín como cuidas de ti</h1>
    <img src="https://media.istockphoto.com/id/146766798/es/foto/grass-field.jpg?s=612x612&w=0&k=20&c=LN9-h7W1eQpfsD_HCY-dMM2nvekSeFZUk54CqIQoLB0=" alt="Imagen de jardín">
  </div>
<form class="login-form" action="html/dashboard.php" method="POST"> <h2>¡Bienvenido de nuevo a SafeGarden!</h2>
      <p>Inicia sesión en tu cuenta</p>

      <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

      <input type="email" name="correo" required><br><br>

        <label for="clave">Contraseña:</label><br>
        <input type="text" name="clave" required><br><br>

      <div class="options">
        <label><input type="checkbox" id="recordar"> Recuérdame</label>
        <a href="login3.html">¿Olvidaste tu contraseña?</a>
      </div>

      <button type="submit" class="login-btn">Iniciar sesión</button>

      <div class="register">
        <p>¿No tienes cuenta? <a href="registrouser.php">Regístrate</a></p>
      </div>
    </form>

  </body>
</html>
