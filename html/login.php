<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$servername = "localhost";
$username = "u557447082_9x8vh";
$password = "safegarden_bm9F8>y";
$dbname = "u557447082_safegardendb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$error = '';
$loginSuccess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $contrasena = trim($_POST["contraseña"] ?? '');

    if (empty($email) || empty($contrasena)) {
        $error = "Por favor completa todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo electrónico inválido.";
    } else {
        $stmt = $conn->prepare("SELECT id_cliente, contraseña FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            $hash = $usuario['contraseña'];

            if (password_verify($contrasena, $hash)) {
                $_SESSION["id_cliente"] = $usuario["id_cliente"];
                $loginSuccess = true;
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Correo no registrado.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Iniciar Sesión | SafeGarden</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    * { box-sizing: border-box; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; }
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
    @media (min-width: 768px) {
      .card { flex-direction: row; }
    }
    .left {
      flex: 1;
      background-color: #dcedc8;
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
      color: #2e7d32;
    }
    .right {
      flex: 1;
      padding: 40px;
    }
    .right h2 {
      font-size: 24px;
      background: linear-gradient(to right, #2e7d32, #66bb6a);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      color: transparent;
      margin-bottom: 10px;
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
    .register, .back-link {
      text-align: center;
      font-size: 14px;
      margin-top: 10px;
    }
    .register a, .back-link a {
      color: #388e3c;
      text-decoration: none;
      font-weight: bold;
    }
    .back-link a:hover {
      color: #1b5e20;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="card">
    <div class="left">
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSyqTieVYbildQV61aIF0sawJOBRSzflKWFSw&s" alt="Herramientas de jardín" />
      <h1>Cuida tu jardín como cuidas de ti</h1>
    </div>

    <div class="right">
      <form class="form" method="POST" action="" id="loginForm" novalidate>
        <h2>¡Bienvenido de nuevo a SafeGarden!</h2>
        <input type="email" name="email" placeholder="Correo electrónico" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        <input type="password" name="contraseña" placeholder="Contraseña" required />
        <button type="submit">Iniciar sesión</button>

        <div class="register">
          ¿No tienes cuenta? <a href="registrouser.php">Regístrate</a>
        </div>
        <div class="back-link">
          <a href="index.html"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.getElementById('loginForm').addEventListener('submit', function(e) {
    const email = this.email.value.trim();
    const password = this.contraseña.value.trim();

    if (!email || !password) {
      e.preventDefault();
      Swal.fire({ icon: 'warning', title: 'Campos incompletos', text: 'Por favor llena todos los campos.', confirmButtonColor: '#388e3c' });
      return;
    }

    if (!/\S+@\S+\.\S+/.test(email)) {
      e.preventDefault();
      Swal.fire({ icon: 'error', title: 'Correo inválido', text: 'Correo no válido.', confirmButtonColor: '#388e3c' });
      return;
    }
  });

  <?php if (!empty($error)): ?>
    Swal.fire({
      icon: 'error',
      title: 'Error de inicio de sesión',
      text: <?= json_encode($error) ?>,
      confirmButtonColor: '#388e3c'
    });
  <?php elseif ($loginSuccess): ?>
    Swal.fire({
      icon: 'success',
      title: '¡Bienvenido!',
      text: 'Inicio de sesión exitoso.',
      timer: 2000,
      timerProgressBar: true,
      showConfirmButton: false,
      didClose: () => { window.location.href = 'html/dashboard.php'; }
    });
  <?php endif; ?>
</script>

</body>
</html>
