<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

include('conexion.php');

$rol        = $_SESSION['usuario_rol'];
$nombre     = htmlspecialchars($_SESSION['usuario_nombre']);
$iniciales  = strtoupper(substr($nombre, 0, 2));
$usuario_id = (int) $_SESSION['usuario_id'];
$dashboard  = $rol === 'admin' ? 'dashboard_admin.php' : ($rol === 'almacenista' ? 'dashboard_almacen.php' : 'dashboard_vendedor.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = (int) $_POST['producto_id'];
    $tipo        = pg_escape_string($conn, $_POST['tipo']);
    $cantidad    = (int) $_POST['cantidad'];
    $descripcion = trim(pg_escape_string($conn, $_POST['descripcion'] ?? ''));

    if ($tipo === 'salida') {
        $stock_actual = pg_fetch_result(pg_query($conn, "SELECT stock FROM productos WHERE id = $producto_id"), 0, 0);
        if ($cantidad > $stock_actual) {
            header('Location: movimientos.php?error=stock');
            exit;
        }
    }

    pg_query($conn, "INSERT INTO movimientos (producto_id, usuario_id, tipo, cantidad, descripcion) 
                     VALUES ($producto_id, $usuario_id, '$tipo', $cantidad, '$descripcion')");

    if ($tipo === 'entrada') {
        pg_query($conn, "UPDATE productos SET stock = stock + $cantidad WHERE id = $producto_id");
    } else {
        pg_query($conn, "UPDATE productos SET stock = stock - $cantidad WHERE id = $producto_id");
    }

    header('Location: movimientos.php?ok=1');
    exit;
}

$productos   = pg_query($conn, "SELECT id, nombre, stock FROM productos ORDER BY nombre ASC");
$movimientos = pg_query($conn, "SELECT m.*, p.nombre as producto, u.nombre as usuario 
                                 FROM movimientos m 
                                 JOIN productos p ON m.producto_id = p.id 
                                 JOIN usuarios u ON m.usuario_id = u.id 
                                 ORDER BY m.created_at DESC LIMIT 50");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos</title>
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
            <a href="<?= $dashboard ?>" class="sb-item">
                <div class="sb-dot"></div>
                <span class="sb-text">Inicio</span>
            </a>
            <a href="productos.php" class="sb-item">
                <div class="sb-dot"></div>
                <span class="sb-text">Productos</span>
            </a>
            <a href="movimientos.php" class="sb-item active">
                <div class="sb-dot"></div>
                <span class="sb-text">Movimientos</span>
            </a>
        </div>
        <?php if ($rol === 'admin'): ?>
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
        <?php endif; ?>
        <div class="sb-footer">
            <div class="sb-avatar"><?= $iniciales ?></div>
            <div>
                <div class="sb-user-name"><?= $nombre ?></div>
                <div class="sb-user-role"><?= ucfirst($rol) ?></div>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Movimientos</div>
                <div class="topbar-sub">Entradas y salidas de inventario</div>
            </div>
            <a href="logout.php" class="btn-logout">Cerrar sesión</a>
        </div>

        <div class="main-content">

            <?php if (isset($_GET['ok'])): ?>
                <div class="alert alert-success">Movimiento registrado correctamente ✔</div>
            <?php endif; ?>
            <?php if (isset($_GET['error']) && $_GET['error'] === 'stock'): ?>
                <div class="alert alert-error">No hay suficiente stock para esta salida.</div>
            <?php endif; ?>

            <div class="card">
                <h3>Registrar movimiento</h3>
                <form action="movimientos.php" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Producto:</label>
                            <select name="producto_id" required>
                                <option value="">Seleccionar...</option>
                                <?php while ($p = pg_fetch_assoc($productos)): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= htmlspecialchars($p['nombre']) ?> (Stock: <?= $p['stock'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tipo:</label>
                            <select name="tipo" required>
                                <?php if ($rol !== 'vendedor'): ?>
                                    <option value="entrada">Entrada</option>
                                <?php endif; ?>
                                <option value="salida">Salida</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Cantidad:</label>
                            <input type="number" name="cantidad" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Descripción:</label>
                            <input type="text" name="descripcion" placeholder="Opcional">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Registrar movimiento</button>
                    </div>
                </form>
            </div>

            <div class="card">
                <h3>Últimos 50 movimientos</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Usuario</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (pg_num_rows($movimientos) === 0): ?>
                            <tr><td colspan="6" style="text-align:center; color:#a78bfa;">No hay movimientos registrados aún.</td></tr>
                        <?php else: ?>
                            <?php while ($m = pg_fetch_assoc($movimientos)): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                                <td><?= htmlspecialchars($m['producto']) ?></td>
                                <td><span class="badge badge-<?= $m['tipo'] ?>"><?= ucfirst($m['tipo']) ?></span></td>
                                <td><?= $m['cantidad'] ?></td>
                                <td><?= htmlspecialchars($m['usuario']) ?></td>
                                <td><?= htmlspecialchars($m['descripcion']) ?></td>
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