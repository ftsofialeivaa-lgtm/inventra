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
    if ($id !== (int) $_SESSION['usuario_id']) {
        pg_query($conn, "DELETE FROM usuarios WHERE id = $id");
    }
    header('Location: usuarios.php?ok=eliminado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_u = trim(pg_escape_string($conn, $_POST['nombre']));
    $email    = trim(pg_escape_string($conn, $_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $rol      = pg_escape_string($conn, $_POST['rol']);

    pg_query($conn, "INSERT INTO usuarios (nombre, email, password, rol) 
                     VALUES ('$nombre_u', '$email', '$password', '$rol')");
    header('Location: usuarios.php?ok=creado');
    exit;
}

$usuarios = pg_query($conn, "SELECT id, nombre, email, rol, created_at FROM usuarios ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
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
            <a href="proveedores.php" class="sb-item">
                <div class="sb-dot"></div>
                <span class="sb-text">Proveedores</span>
            </a>
            <a href="usuarios.php" class="sb-item active">
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
                <div class="topbar-title">Usuarios</div>
                <div class="topbar-sub">Gestión de usuarios del sistema</div>
            </div>
            <a href="logout.php" class="btn-logout">Cerrar sesión</a>
        </div>

        <div class="main-content">

            <?php if (isset($_GET['ok'])): ?>
                <div class="alert alert-success">Operación realizada correctamente ✔</div>
            <?php endif; ?>

            <div class="card">
                <h3>Agregar usuario</h3>
                <form action="usuarios.php" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Contraseña:</label>
                            <input type="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label>Rol:</label>
                            <select name="rol">
                                <option value="vendedor">Vendedor</option>
                                <option value="almacenista">Almacenista</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Agregar usuario</button>
                    </div>
                </form>
            </div>

            <div class="card">
                <h3>Lista de usuarios</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Registrado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($u = pg_fetch_assoc($usuarios)): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class="badge badge-<?= $u['rol'] ?>"><?= ucfirst($u['rol']) ?></span></td>
                                <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                                        <a href="usuarios.php?eliminar=<?= $u['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                                    <?php else: ?>
                                        <span style="color:#a78bfa; font-size:12px;">Tu cuenta</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
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