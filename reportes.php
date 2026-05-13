<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

include('conexion.php');

$nombre    = htmlspecialchars($_SESSION['usuario_nombre']);
$iniciales = strtoupper(substr($nombre, 0, 2));

$total_productos  = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM productos"), 0, 0);
$total_usuarios   = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM usuarios"), 0, 0);
$total_entradas   = pg_fetch_result(pg_query($conn, "SELECT COALESCE(SUM(cantidad), 0) FROM movimientos WHERE tipo = 'entrada'"), 0, 0);
$total_salidas    = pg_fetch_result(pg_query($conn, "SELECT COALESCE(SUM(cantidad), 0) FROM movimientos WHERE tipo = 'salida'"), 0, 0);
$valor_inventario = pg_fetch_result(pg_query($conn, "SELECT COALESCE(SUM(stock * precio), 0) FROM productos"), 0, 0);

$mas_movidos = pg_query($conn, "SELECT p.nombre, COUNT(m.id) as movimientos,
                                 SUM(CASE WHEN m.tipo='entrada' THEN m.cantidad ELSE 0 END) as entradas,
                                 SUM(CASE WHEN m.tipo='salida' THEN m.cantidad ELSE 0 END) as salidas
                                 FROM productos p
                                 LEFT JOIN movimientos m ON p.id = m.producto_id
                                 GROUP BY p.id, p.nombre
                                 ORDER BY movimientos DESC LIMIT 10");

$por_usuario = pg_query($conn, "SELECT u.nombre, COUNT(m.id) as movimientos,
                                 SUM(CASE WHEN m.tipo='entrada' THEN 1 ELSE 0 END) as entradas,
                                 SUM(CASE WHEN m.tipo='salida' THEN 1 ELSE 0 END) as salidas
                                 FROM usuarios u
                                 LEFT JOIN movimientos m ON u.id = m.usuario_id
                                 GROUP BY u.id, u.nombre
                                 ORDER BY movimientos DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
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
            <a href="usuarios.php" class="sb-item">
                <div class="sb-dot"></div>
                <span class="sb-text">Usuarios</span>
            </a>
            <a href="reportes.php" class="sb-item active">
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
                <div class="topbar-title">Reportes</div>
                <div class="topbar-sub">Estadísticas y análisis del sistema</div>
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
                    <p class="stat-label">Total usuarios</p>
                    <p class="stat-val"><?= $total_usuarios ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Total entradas</p>
                    <p class="stat-val"><?= $total_entradas ?></p>
                </div>
                <div class="stat-card warning">
                    <p class="stat-label">Total salidas</p>
                    <p class="stat-val"><?= $total_salidas ?></p>
                </div>
                <div class="stat-card success">
                    <p class="stat-label">Valor inventario</p>
                    <p class="stat-val" style="font-size:18px;">$<?= number_format($valor_inventario, 2) ?></p>
                </div>
            </div>

            <div class="card">
                <h3>📦 Productos más movidos</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Total movimientos</th>
                                <th>Entradas</th>
                                <th>Salidas</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (pg_num_rows($mas_movidos) === 0): ?>
                            <tr><td colspan="4" style="text-align:center; color:#a78bfa;">No hay movimientos aún.</td></tr>
                        <?php else: ?>
                            <?php while ($m = pg_fetch_assoc($mas_movidos)): ?>
                            <tr>
                                <td><?= htmlspecialchars($m['nombre']) ?></td>
                                <td><?= $m['movimientos'] ?></td>
                                <td><span class="badge badge-entrada"><?= $m['entradas'] ?></span></td>
                                <td><span class="badge badge-salida"><?= $m['salidas'] ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h3>👥 Actividad por usuario</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Total movimientos</th>
                                <th>Entradas</th>
                                <th>Salidas</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($u = pg_fetch_assoc($por_usuario)): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td><?= $u['movimientos'] ?></td>
                                <td><span class="badge badge-entrada"><?= $u['entradas'] ?></span></td>
                                <td><span class="badge badge-salida"><?= $u['salidas'] ?></span></td>
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