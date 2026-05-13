<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

include('conexion.php');

$nombre = htmlspecialchars($_SESSION['usuario_nombre']);
$iniciales = strtoupper(substr($nombre, 0, 2));

$total_productos   = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM productos"), 0, 0);
$total_usuarios    = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM usuarios"), 0, 0);
$total_proveedores = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM proveedores"), 0, 0);
$stock_bajo        = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM productos WHERE stock <= stock_minimo"), 0, 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="layout">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sb-logo">
            <div class="sb-logo-text">📦 Inventra</div>
            <div class="sb-logo-sub">Sistema de gestión</div>
        </div>
        <div class="sb-section">
            <div class="sb-label">General</div>
            <a href="dashboard_admin.php" class="sb-item active">
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

    <!-- Contenido -->
    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Panel principal</div>
                <div class="topbar-sub">Bienvenido, <?= $nombre ?></div>
            </div>
            <a href="logout.php" class="btn-logout">Cerrar sesión</a>
        </div>

        <div class="main-content">

            <div class="stats-grid">
                <div class="stat-card success">
                    <p class="stat-label">Total productos</p>
                    <p class="stat-val"><?= $total_productos ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Usuarios</p>
                    <p class="stat-val"><?= $total_usuarios ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Proveedores</p>
                    <p class="stat-val"><?= $total_proveedores ?></p>
                </div>
                <div class="stat-card danger">
                    <p class="stat-label">Stock bajo</p>
                    <p class="stat-val"><?= $stock_bajo ?></p>
                </div>
            </div>

            <div class="card">
                <h3>⚠ Productos con stock bajo</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Stock actual</th>
                                <th>Stock mínimo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $res = pg_query($conn, "SELECT nombre, categoria, stock, stock_minimo FROM productos WHERE stock <= stock_minimo ORDER BY stock ASC");
                        if (pg_num_rows($res) === 0):
                        ?>
                            <tr><td colspan="5" style="text-align:center; color:#a78bfa;">No hay productos con stock bajo ✔</td></tr>
                        <?php else: ?>
                            <?php while ($p = pg_fetch_assoc($res)): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td><?= htmlspecialchars($p['categoria']) ?></td>
                                <td class="stock-bajo"><?= $p['stock'] ?></td>
                                <td><?= $p['stock_minimo'] ?></td>
                                <td><span class="badge badge-bajo">Stock bajo</span></td>
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