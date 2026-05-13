<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$rol = $_SESSION['usuario_rol'];

if ($rol === 'admin') {
    header('Location: dashboard_admin.php');
} elseif ($rol === 'almacenista') {
    header('Location: dashboard_almacen.php');
} elseif ($rol === 'vendedor') {
    header('Location: dashboard_vendedor.php');
} else {
    header('Location: index.php');
}
exit;
?>