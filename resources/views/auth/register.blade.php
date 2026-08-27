<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Deshabilitado - THE GAME</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html { margin: 0; padding: 0; height: 100%; width: 100%; overflow-x: hidden; }

        .login-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            background-image: url('{{ asset('assets/images/UM_THEGAME.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 40px 60px;
        }

        .floating-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 520px;
            background: rgba(11, 15, 25, 0.92);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 50px 45px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.7);
            text-align: center;
        }

        @media (max-width: 768px) {
            .login-container { justify-content: center; padding: 20px; }
            .floating-card { max-width: 100%; padding: 35px 25px; }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="floating-card">
        <div class="mb-4">
            <img src="{{ asset('assets/images/logo_thegame.png') }}" alt="Logo" style="width: 320px; height: auto; margin-bottom: 20px; filter: drop-shadow(0 4px 12px rgba(59, 130, 246, 0.3));">
        </div>

        <h3 style="color: white; font-weight: 600; font-size: 1.6rem; margin-bottom: 12px;">Registro no disponible</h3>
        <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.5; margin-bottom: 30px;">
            El registro de nuevos usuarios está deshabilitado en este sistema. Si necesitas acceso, por favor comunícate con el administrador.
        </p>

        <a href="{{ route('login') }}" class="btn btn-primary w-100 py-3 mb-4" style="background-color: #1d4ed8; border: none; font-weight: 600; font-size: 1rem; border-radius: 10px; text-decoration: none; display: inline-block;">
            Ir al inicio de sesión
        </a>

        <div style="color: #64748b; font-size: 0.8rem; margin-top: 15px;">
            © THE GAME. Todos los Derechos Reservados
        </div>
    </div>
</div>

</body>
</html>