<?php
session_start();
$servername = "localhost";
$username = "u557447082_9x8vh";
$password ="safegarden_bm9F8>y";
$dbname = "u557447082_safegardendb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$error = '';

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $contrasena = trim($_POST["contraseña"] ?? '');

    if (empty($email) || empty($contrasena)) {
        $error = "Por favor completa todos los campos.";
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
                // Redirige directamente al dashboard (sin depender de JS)
                header("Location: dashboard.php");
                exit;
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

