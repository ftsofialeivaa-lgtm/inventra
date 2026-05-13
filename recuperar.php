<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña — INVENTRA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: #4c1d95;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 40px 36px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            text-align: center;
        }

        .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        h2 {
            font-size: 20px;
            color: #1e1b2e;
            margin-bottom: 8px;
        }

        .sub {
            font-size: 13px;
            color: #a78bfa;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .info-box {
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .info-box p {
            font-size: 13px;
            color: #4c1d95;
            line-height: 1.7;
        }

        .info-box strong {
            color: #6d28d9;
        }

        .btn-back {
            display: block;
            width: 100%;
            padding: 13px;
            background: #4c1d95;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.08em;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-back:hover { background: #6d28d9; }
    </style>
</head>
<body>

    <div class="card">
        <div class="icon">🔐</div>
        <h2>Recuperar contraseña</h2>
        <p class="sub">¿Olvidaste tu contraseña de acceso al sistema?</p>

        <div class="info-box">
            <p>
                Para recuperar tu contraseña debes contactar al <strong>administrador del sistema</strong>, 
                quien podrá restablecer tus credenciales de acceso desde el panel de gestión de usuarios.
            </p>
            <br>
            <p>
                📧 <strong>contacto@inventra.top</strong><br>
                📞 <strong>+57 300 123 4567</strong>
            </p>
        </div>

        <a href="index.php" class="btn-back">← Volver al inicio de sesión</a>
    </div>

</body>
</html>
