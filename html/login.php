<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$servername = "localhost";
$username = "u557447082_9x8vh";
$password ="safegarden_bm9F8>y";
$dbname = "u557447082_safegardendb";;

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$error = '';
$loginSuccess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitización básica
    $email_raw = $_POST["email"] ?? '';
    $contrasena_raw = $_POST["contraseña"] ?? '';

    // Sanitizar inputs
    $email = filter_var(trim($email_raw), FILTER_SANITIZE_EMAIL);
    $contrasena = trim($contrasena_raw);

    // Validaciones servidor
    if (empty($email) || empty($contrasena)) {
        $error = "Por favor completa todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo electrónico inválido.";
    } elseif (!preg_match("/^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[\W_]).{8,}$/", $contrasena)) {
        $error = "La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos.";
    } else {
        // Consulta al usuario usando sentencia preparada para evitar inyección
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
    .register {
      text-align: center;
      font-size: 14px;
      margin-top: 10px;
    }
    .register a {
      color: #388e3c;
      text-decoration: none;
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
    <div class="left">
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSyqTieVYbildQV61aIF0sawJOBRSzflKWFSw&s" alt="Herramientas de jardín" />
      <h1>Cuida tu jardín como cuidas de ti</h1>
      <p>Protege tu jardín.</p>
    </div>

    <div class="right">
      <form class="form" method="POST" action="" id="loginForm" novalidate>
        <h2>¡Bienvenido de nuevo a SafeGarden!</h2>
        <p>Inicia sesión en tu cuenta</p>

        <input type="email" name="email" placeholder="Tu correo electrónico" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        <input type="password" name="contraseña" placeholder="Contraseña" required />

        <button type="submit">Iniciar sesión</button>

        <div class="register">
          <p>¿No tienes cuenta? <a href="registrouser.php">Regístrate</a></p>
        </div>

        <div class="back-link">
          <p><a href="index.html"><i class="fas fa-arrow-left"></i> Volver al inicio</a></p>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Validación simple en cliente antes de enviar
  document.getElementById('loginForm').addEventListener('submit', function(e) {
    const form = this;
    const email = form.email.value.trim();
    const password = form.contraseña.value.trim();

    if (!email || !password) {
      e.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: 'Campos incompletos',
        text: 'Por favor llena todos los campos.',
        confirmButtonColor: '#388e3c'
      });
    } else if (!/\S+@\S+\.\S+/.test(email)) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Correo inválido',
        text: 'Por favor ingresa un correo electrónico válido.',
        confirmButtonColor: '#388e3c'
      });
    } else if (!/(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[\W_]).{8,}/.test(password)) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Contraseña inválida',
        text: 'La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos.',
        confirmButtonColor: '#388e3c'
      });
    }
  });

  <?php if (!empty($error)): ?>
    Swal.fire({
      icon: 'error',
      title: '¡Error!',
      text: <?= json_encode($error) ?>,
      confirmButtonColor: '#388e3c'
    });
  <?php elseif ($loginSuccess): ?>
    Swal.fire({
      icon: 'success',
      title: '¡Bienvenido!',
      text: 'Has iniciado sesión correctamente.',
      timer: 2000,
      timerProgressBar: true,
      showConfirmButton: false,
      didClose: () => { window.location.href = 'html/dashboard.php'; }
    });
  <?php endif; ?>
</script>

</body>
</html>