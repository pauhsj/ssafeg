<?php
session_start();
require("conexion.php");

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener datos del formulario
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];

    // Buscar usuario
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE email = :email");
    $stmt->bindParam(':email', $correo);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($clave, $usuario['password'])) {
        // Autenticación exitosa
        $_SESSION['usuario_id'] = $usuario['id_cliente'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        header("Location: html/dashboard.php");
        exit;
    } else {
        echo "<script>alert('Correo o contraseña incorrectos.'); window.location.href='index.html';</script>";
    }

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> Iniciar Sesión - SafeGarden</title>
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
<form class="login-form" action="login.php" method="POST">      <h2>¡Bienvenido de nuevo a SafeGarden!</h2>
      <p>Inicia sesión en tu cuenta</p>

      <input type="email" id="correo" placeholder="Tu correo electrónico" required>
      <input type="password" id="clave" placeholder="Contraseña" required>

      <div class="options">
        <label><input type="checkbox" id="recordar"> Recuérdame</label>
        <a href="login3.html">¿Olvidaste tu contraseña?</a>
      </div>

      <button type="submit" class="login-btn">Iniciar sesión</button>

      <div class="register">
        <p>¿No tienes cuenta? <a href="login2.html">Regístrate</a></p>
      </div>
    </form>
  </div>
</div>

<script>
  const correoCorrecto = "paulina@gmail.com";
  const claveCorrecta = "1234";

  // Cargar datos recordados
  window.onload = () => {
    const recordado = localStorage.getItem("recordado");
    if (recordado === "true") {
      document.getElementById("correo").value = localStorage.getItem("correo") || "";
      document.getElementById("clave").value = localStorage.getItem("clave") || "";
      document.getElementById("recordar").checked = true;
    }
  };

  function validarLogin(event) {
    event.preventDefault();

    const correo = document.getElementById("correo").value.trim();
    const clave = document.getElementById("clave").value.trim();
    const recordar = document.getElementById("recordar").checked;

    if (correo === correoCorrecto && clave === claveCorrecta) {
      if (recordar) {
        localStorage.setItem("recordado", "true");
        localStorage.setItem("correo", correo);
        localStorage.setItem("clave", clave);
      } else {
        localStorage.removeItem("recordado");
        localStorage.removeItem("correo");
        localStorage.removeItem("clave");
      }

      alert("Inicio de sesión exitoso.");
      window.location.href = "html/dashboard.php"; // Redirige al dashboard
    } else {
      alert("Correo o contraseña incorrectos.");
    }

    return false;
  }
</script>

</body>
</html>
