<?php
session_start();

$error = '';
$success = '';
$servername = "localhost";
$username = "u557447082_9x8vh";
$password ="safegarden_bm9F8>y";
$dbname = "u557447082_safegardendb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre     = trim($_POST['nombre'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $contraseña = trim($_POST['contraseña'] ?? '');
    $telefono   = trim($_POST['telefono'] ?? '');
    $ciudad     = trim($_POST['ciudad'] ?? '');

    // Validación básica + avanzada
    if (!$nombre || !$email || !$contraseña || !$telefono || !$ciudad) {
        $error = "Por favor completa todos los campos.";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
        $error = "El nombre solo puede contener letras y espacios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo electrónico no es válido.";
    } elseif (!preg_match("/^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[\W_]).{8,}$/", $contraseña)) {
        $error = "La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos.";
    } elseif (!preg_match("/^\d{10}$/", $telefono)) {
        $error = "El teléfono debe contener exactamente 10 números.";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $ciudad)) {
        $error = "La ciudad solo puede contener letras y espacios.";
    } else {
        // Verificar si email ya existe
        $checkSql = "SELECT id_cliente FROM usuario WHERE email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $error = "Este correo ya está registrado.";
        } else {
            // Hashear contraseña
            $passHasheada = password_hash($contraseña, PASSWORD_DEFAULT);

            $insertSql = "INSERT INTO usuario (nombre, email, contraseña, telefono, ciudad, creado_en) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($insertSql);
            if (!$stmt) {
                $error = "Error en la preparación de consulta: " . $conn->error;
            } else {
                $stmt->bind_param("sssss", $nombre, $email, $passHasheada, $telefono, $ciudad);
                if ($stmt->execute()) {
                    $success = "Registro exitoso, redirigiendo a inicio de sesión...";
                } else {
                    $error = "Error al registrar: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        $checkStmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear Cuenta - SafeGarden</title>
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
      max-width: 900px;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    @media (min-width: 768px) {
      .card { flex-direction: row; }
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
    .login-link {
      text-align: center;
      font-size: 14px;
      margin-top: 10px;
    }
    .login-link a {
      color: #2e7d32;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="card">
    <div class="left">
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSyqTieVYbildQV61aIF0sawJOBRSzflKWFSw&s" alt="Herramientas de jardín" />
      <h1>Cuida tu jardín como cuidas de ti</h1>
      <p>Protege con solo unos clics.</p>
    </div>

    <div class="right">
      <form class="form" method="POST" action="" id="registerForm" novalidate>
        <h2>Crea tu cuenta gratuita</h2>
        <p>Ingresa tus datos para registrarte</p>

        <input type="text" name="nombre" placeholder="Nombre completo" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" />
        <input type="email" name="email" placeholder="Correo electrónico" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        <input type="password" name="contraseña" placeholder="Contraseña" required />
        <input type="text" name="telefono" placeholder="Teléfono" required value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>" />
        <input type="text" name="ciudad" placeholder="Ciudad" required value="<?= htmlspecialchars($_POST['ciudad'] ?? '') ?>" />

        <button type="submit">Registrarme</button>

        <div class="login-link">
          <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.getElementById('registerForm').addEventListener('submit', function(e) {
    const form = this;
    const nombre = form.nombre.value.trim();
    const email = form.email.value.trim();
    const contraseña = form.contraseña.value.trim();
    const telefono = form.telefono.value.trim();
    const ciudad = form.ciudad.value.trim();

    if (!nombre || !email || !contraseña || !telefono || !ciudad) {
      e.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: 'Campos incompletos',
        text: 'Por favor llena todos los campos.',
        confirmButtonColor: '#388e3c'
      });
      return;
    }

    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre)) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Nombre inválido',
        text: 'El nombre solo puede contener letras y espacios.',
        confirmButtonColor: '#388e3c'
      });
      return;
    }

    if (!/\S+@\S+\.\S+/.test(email)) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Correo inválido',
        text: 'Por favor ingresa un correo electrónico válido.',
        confirmButtonColor: '#388e3c'
      });
      return;
    }

    if (!/^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[\W_]).{8,}$/.test(contraseña)) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Contraseña débil',
        html: 'La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos.',
        confirmButtonColor: '#388e3c'
      });
      return;
    }

    if (!/^\d{10}$/.test(telefono)) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Teléfono inválido',
        text: 'El teléfono debe contener exactamente 10 números.',
        confirmButtonColor: '#388e3c'
      });
      return;
    }

    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(ciudad)) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Ciudad inválida',
        text: 'La ciudad solo puede contener letras y espacios.',
        confirmButtonColor: '#388e3c'
      });
      return;
    }
  });

  <?php if ($error): ?>
    Swal.fire({
      icon: 'error',
      title: '¡Oops!',
      text: <?= json_encode($error) ?>,
      confirmButtonColor: '#388e3c'
    });
  <?php elseif ($success): ?>
    Swal.fire({
      icon: 'success',
      title: '¡Bien hecho!',
      text: <?= json_encode($success) ?>,
      timer: 3000,
      timerProgressBar: true,
      showConfirmButton: false,
      didClose: () => { window.location.href = 'login.php'; }
    });
  <?php endif; ?>
</script>

</body>
</html>