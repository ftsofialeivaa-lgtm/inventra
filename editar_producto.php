<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

include('conexion.php');

$nombre    = htmlspecialchars($_SESSION['usuario_nombre']);
$iniciales = strtoupper(substr($nombre, 0, 2));
$id        = (int) $_GET['id'];

$res      = pg_query($conn, "SELECT * FROM productos WHERE id = $id");
$producto = pg_fetch_assoc($res);

if (!$producto) {
    header('Location: productos.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_p     = trim(pg_escape_string($conn, $_POST['nombre']));
    $categoria    = trim(pg_escape_string($conn, $_POST['categoria']));
    $stock        = (int) $_POST['stock'];
    $stock_minimo = (int) $_POST['stock_minimo'];
    $precio       = (float) $_POST['precio'];
    $proveedor_id = !empty($_POST['proveedor_id']) ? (int) $_POST['proveedor_id'] : 'NULL';

    pg_query($conn, "UPDATE productos SET 
                     nombre = '$nombre_p', 
                     categoria = '$categoria', 
                     stock = $stock, 
                     stock_minimo = $stock_minimo, 
                     precio = $precio, 
                     proveedor_id = $proveedor_id 
                     WHERE id = $id");
    header('Location: productos.php?ok=editado');
    exit;
}

$proveedores = pg_query($conn, "SELECT id, nombre FROM proveedores ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto — INVENTRA</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">

    <div class="sidebar">
        <div class="sb-logo">
            <div class="sb-logo-text">📦 INVENTRA</div>
            <div class="sb-logo-sub">Sistema de gestión</div>
        </div>
        <div class="sb-section">
            <div class="sb-label">General</div>
            <a href="dashboard_admin.php" class="sb-item">
                <div class="sb-dot"></div>
                <span class="sb-text">Inicio</span>
            </a>
            <a href="productos.php" class="sb-item active">
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

    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Editar Producto</div>
                <div class="topbar-sub">Modifica la información del producto</div>
            </div>
            <a href="logout.php" class="btn-logout">Cerrar sesión</a>
        </div>

        <div class="main-content">

            <div class="card">
                <h3>✎ Información del producto</h3>
                <form action="editar_producto.php?id=<?= $id ?>" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Categoría:</label>
                            <input type="text" name="categoria" value="<?= htmlspecialchars($producto['categoria']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Stock actual:</label>
                            <input type="number" name="stock" min="0" value="<?= $producto['stock'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Stock mínimo:</label>
                            <input type="number" name="stock_minimo" min="0" value="<?= $producto['stock_minimo'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Precio:</label>
                            <input type="number" name="precio" step="0.01" min="0" value="<?= $producto['precio'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Proveedor:</label>
                            <select name="proveedor_id">
                                <option value="">Sin proveedor</option>
                                <?php while ($prov = pg_fetch_assoc($proveedores)): ?>
                                    <option value="<?= $prov['id'] ?>" <?= $prov['id'] == $producto['proveedor_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prov['nombre']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        <a href="productos.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>

        </div>

        <div class="footer">
            <div>
                <div class="footer-brand">INVENTRA</div>
                <div class="footer-info">contacto@inventariopro.com — +57 300 123 4567</div>
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