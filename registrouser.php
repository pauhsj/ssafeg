<?php
$servername = "localhost";
$username = "u557447082_9x8vh";
$password = '$afegarden_bm9F8>y';
$dbname = "u557447082_safegardedb";


// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
// Obtener ID del usuario actual desde sesión
$id_usuario = $_SESSION['id_usuario'] ?? 0;


// Recibir datos
$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$direccion = $_POST['direccion'] ?? '';

// Insertar
$sql = "INSERT INTO usuarios (nombre, email, telefono, direccion, creado_en)
        VALUES (?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nombre, $email, $telefono, $direccion);

if ($stmt->execute()) {
    
 header("Location: login.php");   
} else {
    echo "Error al registrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>






<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - SafeGarden</title>
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
            margin-bottom: 20px;
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

        .register-form {
            width: 100%;
            max-width: 350px;
        }

        .register-form h2 {
            margin-bottom: 10px;
            font-size: 28px;
            text-align: center;
        }

        .register-form p {
            margin-bottom: 20px;
            color: #777;
            text-align: center;
            font-size: 14px;
        }

        .register-form input[type="text"],
        .register-form input[type="email"],
        .register-form input[type="password"],
        .register-form select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .register-form .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .register-form .checkbox-group input[type="checkbox"] {
            margin-right: 8px;
        }

        .register-form .checkbox-group a {
            color: #467e48;
            text-decoration: none;
        }

        .register-form button.register-btn {
            width: 100%;
            padding: 12px;
            background-color: #366738;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .login-link {
            text-align: center;
            font-size: 14px;
        }

        .login-link a {
            color: #467e48;
            text-decoration: none;
        }
    </style>
</head>
<body>
    
<div class="container">
    <div class="left-side">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSyqTieVYbildQV61aIF0sawJOBRSzflKWFSw&s" alt="Herramientas de jardín">
        <h1>Cuida tu jardín como cuidas de ti</h1>
        <p>Protege con solo unos clics.</p>
    </div>

    <div class="right-side">
        <form class="register-form" method="POST" action="registro.php">
    <h2>Crea tu cuenta gratuita</h2>
    <p>Ingresa tus datos para registrarte</p>

    <input type="text" name="nombre" placeholder="Nombre completo" required>
    <input type="email" name="email" placeholder="Tu correo electrónico" required>
    <input type="text" name="telefono" placeholder="Teléfono" required>
    <input type="text" name="direccion" placeholder="Dirección" required>
    
    <button type="submit" class="register-btn">Comenzar</button>

    <div class="login-link">
        <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
    </div>
</form>

    </div>
</div>

</body>
</html>
