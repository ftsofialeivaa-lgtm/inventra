<?php
if (isset($_COOKIE[session_name()])) {
    session_start();
    if (isset($_SESSION['usuario_id'])) {
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVENTRA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* ── Panel izquierdo ── */
        .left {
            flex: 1;
            background: #6d28d9;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .blob1 { width: 400px; height: 400px; bottom: -100px; right: -100px; }
        .blob2 { width: 220px; height: 220px; top: -50px; left: -50px; }

        .brand { font-size: 14px; font-weight: 600; color: rgba(255,255,255,0.9); letter-spacing: 0.2em; margin-bottom: 4px; z-index: 1; position: relative; }
        .brand-sub { font-size: 9px; color: rgba(255,255,255,0.35); letter-spacing: 0.15em; z-index: 1; position: relative; }

        .left-mid { z-index: 1; position: relative; }

        .big-title {
            font-size: 38px;
            font-weight: 700;
            color: white;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .big-title span { color: #c4b5fd; }

        .desc {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            line-height: 1.7;
            max-width: 320px;
            margin-bottom: 36px;
        }

        .feat {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .feat-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .feat-text { font-size: 13px; color: rgba(255,255,255,0.75); }

        .left-bot { z-index: 1; position: relative; }

        .pill {
            display: inline-block;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 10px;
            color: rgba(255,255,255,0.5);
        }

        /* ── Panel derecho ── */
        .right {
            width: 460px;
            background: #faf5ff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
            min-height: 100vh;
        }

        .card {
            width: 100%;
            background: white;
            border-radius: 18px;
            padding: 36px;
            border: 0.5px solid #e9d5ff;
            box-shadow: 0 4px 24px rgba(109,40,217,0.08);
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e1b2e;
            margin-bottom: 6px;
        }

        .card-sub {
            font-size: 12px;
            color: #a78bfa;
            margin-bottom: 28px;
        }

        .inp-label {
            font-size: 10px;
            font-weight: 700;
            color: #6d28d9;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
            display: block;
        }

        .inp-wrap { margin-bottom: 16px; }

        .inp-wrap input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #e9d5ff;
            border-radius: 10px;
            font-size: 13px;
            font-family: 'Segoe UI', sans-serif;
            background: #faf5ff;
            color: #1e1b2e;
            outline: none;
            transition: all 0.2s;
        }

        .inp-wrap input:focus {
            border-color: #7c3aed;
            background: white;
            box-shadow: 0 0 0 3px rgba(124,58,237,0.08);
        }

        .inp-wrap input::placeholder { color: #c4b5fd; }

        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 0.5px solid #fecaca;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 16px;
            text-align: center;
        }

        .btn-in {
            width: 100%;
            padding: 13px;
            background:  #3e1f6d;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.08em;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s;
        }

        .btn-in:hover { background: #6d28d9; }

        .or {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }

        .or-line { flex: 1; height: 0.5px; background: #e9d5ff; }
        .or-text { font-size: 11px; color: #a78bfa; }

        .links { text-align: center; }

        .links a {
            font-size: 12px;
            color: #7c3aed;
            text-decoration: none;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
        }

        .links a:hover { text-decoration: underline; }

        .links .reg {
            font-size: 11px;
            color: #a78bfa;
            display: block;
        }

        .links .reg a {
            display: inline;
            font-size: 11px;
        }
        .logo-circle {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    border: 2px solid rgba(255,255,255,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    overflow: hidden;
    backdrop-filter: blur(4px);
}

.logo-circle img {
    width: 180px;
    height: 180px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #fff;
}

.logo-circle img:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 18px rgba(0,0,0,0.35);
}
    </style>
</head>
<body>

<!-- Panel izquierdo -->
<div class="left">
    <div class="blob blob1"></div>
    <div class="blob blob2"></div>

   <div>
    <center>
        <div class="logo-circle">
            <img src="img/logo.jpeg" alt="INVENTRA">
        </div>
    </center>

    <center> <div class="brand-sub">SISTEMA DE CONTROL DE INVENTARIO EMPRESARIAL</div> </center>
</div>
    <div class="left-mid">
        <center> <div class="big-title">Gestiona tu inventario<br><span>con inteligencia</span></div> </center>
        <center> <div class="desc">Una plataforma completa para el control empresarial de productos, proveedores y equipos de trabajo.</div> </center>
        <div class="feat">
        <div class="feat-icon">📦</div> 
        <span class="feat-text">Control de stock en tiempo real</span>
        </div>
        <div class="feat">
            <div class="feat-icon">👥</div> 
            <span class="feat-text">Múltiples roles y permisos</span>
        </div>
        <div class="feat">
            <div class="feat-icon">📊</div>
            <span class="feat-text">Reportes y estadísticas</span>
        </div>
    </div>

    <div class="left-bot">
        <center> <span class="pill">© 2026 Inventra — Todos los derechos reservados</span> </center>
    </div>
</div>

<!-- Panel derecho -->
<div class="right">
    <div class="card">
        <center> <div class="card-title">Bienvenidos</div> </center>
        <center> <div class="card-sub">Ingresa tus credenciales para continuar</div> </center>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert-error">Correo o contraseña incorrectos.</div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="inp-wrap">
                <span class="inp-label">CORREO ELECTRÓNICO</span>
                <input type="email" name="email" placeholder="tu@empresa.com" required>
            </div>
            <div class="inp-wrap">
                <span class="inp-label">CONTRASEÑA</span>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-in">INICIAR SESIÓN</button>
        </form>

        <div class="or">
            <div class="or-line"></div>
            <span class="or-text">¿Nuevo aquí?</span>
            <div class="or-line"></div>
        </div>

        <div class="links">
            <a href="register.php">Regístrese en el sistema</a>
            <span class="reg">¿Olvidó su contraseña? <a href="#">Recupérela aquí</a></span>
        </div>
    </div>
</div>

</body>
</html>