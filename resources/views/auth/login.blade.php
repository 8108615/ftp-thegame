@extends('layouts.admin')

@push('styles')
    <style>
        body, html { margin: 0; padding: 0; height: 100%; width: 100%; overflow-x: hidden; }

        /* Contenedor que ocupa toda la pantalla con la imagen de fondo */
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

        /* Panel flotante de la derecha más grande, visible y espacioso */
        .floating-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 560px; /* Panel más ancho */
            background: rgba(11, 15, 25, 0.90); /* Fondo oscuro elegante y sólido */
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 55px 50px; /* Más espacio interno */
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.7);
        }

        .form-control {
            background-color: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            background-color: rgba(30, 41, 59, 0.95);
            border-color: #3b82f6;
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }

        /* Adaptabilidad para móviles */
        @media (max-width: 768px) {
            .login-container {
                justify-content: center;
                padding: 20px;
            }
            .floating-card {
                max-width: 100%;
                padding: 35px 25px;
            }
        }
    </style>
@endpush

@section('content')
<div class="login-container">
    <!-- Panel flotante grande de inicio de sesión -->
    <div class="floating-card">
        <div class="text-center mb-4">
            <!-- Logo más grande y llamativo -->
            <img src="{{ asset('assets/images/logo_thegame.png') }}" alt="Logo" style="width: 450px; height: auto; margin-bottom: 20px; filter: drop-shadow(0 4px 12px rgba(59, 130, 246, 0.3));">
            <h3 style="color: white; font-weight: 600; font-size: 1.8rem; margin-bottom: 8px;">Iniciar sesión</h3>
            <p style="color: #94a3b8; font-size: 0.95rem;">Accede a tu cuenta para continuar</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" style="color: #cbd5e1; font-size: 0.9rem; margin-bottom: 8px; display: block;">Correo electrónico</label>
                <input type="email" name="email" class="form-control" placeholder="usuario@thegame.bo" value="{{ old('email') }}" required autofocus>
                @error('email') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
            </div>

            <div class="mb-4">
                <label for="password" style="color: #cbd5e1; font-size: 0.9rem; margin-bottom: 8px; display: block;">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="Ingresa tu contraseña" required>
                @error('password') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4" style="font-size: 0.9rem;">
                <div>
                    <input type="checkbox" name="remember" id="remember" class="form-check-input" style="width: 1.1em; height: 1.1em;">
                    <label for="remember" style="color: #94a3b8; margin-left: 6px;">Recordarme</label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="color: #3b82f6; text-decoration: none;">¿Olvidaste tu contraseña?</a>
                @endif
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3" style="background-color: #1d4ed8; border: none; font-weight: 600; font-size: 1rem; border-radius: 10px;">Iniciar sesión</button>
        </form>

        <div style="color: #64748b; font-size: 0.8rem; margin-top: 35px; text-align: center;">
            © THE GAME. Todo los Derechos Reservados
        </div>
    </div>
</div>
@endsection
