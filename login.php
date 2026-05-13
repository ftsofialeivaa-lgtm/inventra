<?php
session_start();
include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$email    = trim(pg_escape_string($conn, $_POST['email']));
$password = $_POST['password'];

$sql       = "SELECT id, nombre, password, rol FROM usuarios WHERE email = '$email' LIMIT 1";
$resultado = pg_query($conn, $sql);

if ($resultado && pg_num_rows($resultado) === 1) {
    $usuario = pg_fetch_assoc($resultado);

    if (password_verify($password, $usuario['password'])) {
        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol']    = $usuario['rol'];
        header('Location: dashboard.php');
        exit;
    }
}

header('Location: index.php?error=1');
exit;
?>