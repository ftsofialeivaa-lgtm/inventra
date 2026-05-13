<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

include('conexion.php');

$nombre    = htmlspecialchars($_SESSION['usuario_nombre']);
$iniciales = strtoupper(substr($nombre, 0, 2));

if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    pg_query($conn, "DELETE FROM proveedores WHERE id = $id");
    header('Location: proveedores.php?ok=eliminado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_p = trim(pg_escape_string($conn, $_POST['nombre']));
    $contacto = trim(pg_escape_string($conn, $_POST['contacto']));
    $email    = trim(pg_escape_string($conn, $_POST['email']));
    $telefono = trim(pg_escape_string($conn, $_POST['telefono']));

    pg_query($conn, "INSERT INTO proveedores (nombre, contacto, email, telefono) 
                     VALUES ('$nombre_p', '$contacto', '$email', '$telefono')");
    header('Location: proveedores.php?ok=creado');
    exit;
}

$proveedores = pg_query($conn, "SELECT * FROM proveedores ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">

    <div class="sidebar">
        <div class="sb-logo">
            <div class="sb-logo-text">📦 Inventra</div>
            <div class="sb-logo-sub">Sistema de gestión</div>
        </div>
        <div class="sb-section">
            <div class="sb-label">General</div>
            <a href="dashboard_admin.php" class="sb-item">
                <div class="sb-dot"></div>
                <span class="sb-text">Inicio</span>
            </a>
            <a href="productos.php" class="sb-item">
                <div class="sb-dot"></div>
                <span class="sb-text">Productos</span>
            </a>
            <a href="movimientos.php" class="sb-item">
                <div class="sb-dot"></div>
                <span class="sb-text">Movimientos</span>
            </a>
        </div>
        <div class="sb-section" style="margin-top:14px;">
            <div class="sb-label">Administración</div>
            <a href="proveedores.php" class="sb-item active">
                <div class="sb-dot"></div>
                <span class="sb-text">Proveedores</span>
            </a>
            <a href="usuarios.php" class="sb-item">
                <div class="sb-dot"></div>
                <span class="sb-text">Usuarios</span>
            </a>
            <a href="reportes.php" class="sb-item">
                <div class="sb-dot"></div>
                <span class="sb-text">Reportes</span>
            </a>
        </div>
        <div class="sb-footer">
            <div class="sb-avatar"><?= $iniciales ?></div>
            <div>
                <div class="sb-user-name"><?= $nombre ?></div>
                <div class="sb-user-role">Administrador</div>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Proveedores</div>
                <div class="topbar-sub">Gestión de proveedores</div>
            </div>
            <a href="logout.php" class="btn-logout">Cerrar sesión</a>
        </div>

        <div class="main-content">

            <?php if (isset($_GET['ok'])): ?>
                <div class="alert alert-success">Operación realizada correctamente ✔</div>
            <?php endif; ?>

            <div class="card">
                <h3>Agregar proveedor</h3>
                <form action="proveedores.php" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Contacto:</label>
                            <input type="text" name="contacto">
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email">
                        </div>
                        <div class="form-group">
                            <label>Teléfono:</label>
                            <input type="text" name="telefono">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Agregar proveedor</button>
                    </div>
                </form>
            </div>

            <div class="card">
                <h3>Lista de proveedores</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Contacto</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (pg_num_rows($proveedores) === 0): ?>
                            <tr><td colspan="5" style="text-align:center; color:#a78bfa;">No hay proveedores registrados.</td></tr>
                        <?php else: ?>
                            <?php while ($p = pg_fetch_assoc($proveedores)): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td><?= htmlspecialchars($p['contacto']) ?></td>
                                <td><?= htmlspecialchars($p['email']) ?></td>
                                <td><?= htmlspecialchars($p['telefono']) ?></td>
                                <td>
                                    <a href="proveedores.php?eliminar=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="footer">
            <div>
                <div class="footer-brand">Inventra</div>
                <div class="footer-info">contacto@Inventra.com — +57 300 123 4567</div>
            </div>
            <div class="footer-links">
                <a href="#" class="footer-link">Soporte</a>
                <a href="#" class="footer-link">Privacidad</a>
                <span class="footer-link">© 2026</span>
            </div>
        </div>
    </div>

</div>
</body>
</html>