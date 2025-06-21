<?php

$servername = "localhost";
$username = "u557447082_9x8vh";
$password = '$afegarden_bm9F8>y';
$dbname = "u557447082_safegardedb";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Traer todos los usuarios
    $usuarios = $conexion->query("SELECT id_cliente, contraseña FROM usuario")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($usuarios as $usuario) {
        $id = $usuario['id_cliente'];
        $clave = $usuario['contraseña'];

        // Verificamos si la contraseña ya está encriptada
        if (!contraseña_get_info($clave)['algo']) {
            // Encriptar
            $hash = contraseña_hash($clave, CONTRASEÑA_DEFAULT);
            
            // Actualizar en la base de datos
            $stmt = $conexion->prepare("UPDATE usuario SET contraseña = :hash WHERE id_cliente = :id");
            $stmt->execute([
                ':hash' => $hash,
                ':id' => $id
            ]);

            
        } else {
        }
    }

    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
}


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
    <p>Protege con solo unos clics.</p>
    <img src="https://media.istockphoto.com/id/146766798/es/foto/grass-field.jpg?s=612x612&w=0&k=20&c=LN9-h7W1eQpfsD_HCY-dMM2nvekSeFZUk54CqIQoLB0=" alt="Imagen de jardín">
  </div>

  <div class="right-side">
<form class="login-form" action="html/dashboard.php" method="POST"> <h2>¡Bienvenido de nuevo a SafeGarden!</h2>
      <p>Inicia sesión en tu cuenta</p>

      <input type="email" id="correo" name="correo" placeholder="Tu correo electrónico" required>
      <input type="contraseña" id="clave" name="clave" placeholder="Contraseña" required>

      <div class="options">
        <label><input type="checkbox" id="recordar"> Recuérdame</label>
        <a href="login3.html">¿Olvidaste tu contraseña?</a>
      </div>

      <button type="submit" class="login-btn">Iniciar sesión</button>

      <div class="register">
        <p>¿No tienes cuenta? <a href="registrouser.php">Regístrate</a></p>
      </div>
    </form>
  </div>
</div>




  </body>
</html>
