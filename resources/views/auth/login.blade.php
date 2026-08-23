@extends('layouts.admin')

@push('styles')
    <style>
        /* Eliminamos márgenes y aseguramos pantalla completa */
        body, html { margin: 0; padding: 0; height: 100%; width: 100%; overflow-x: hidden; }

        /* Contenedor principal tipo Flexbox */
        .full-wrapper { display: flex; height: 100vh; width: 100vw; }

        /* Lado Izquierdo: Formulario */
        .left-side {
            width: 100%;
            max-width: 450px;
            background-color: #1e293b;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            z-index: 10;
        }

        /* Lado Derecho: Imagen */
        .right-side {
            flex-grow: 1;
            /* Usamos ruta absoluta para evitar errores de compilación */
            background-image: url('{{ asset('assets/images/UM_THEGAME.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Ocultar lado derecho en móviles */
        @media (max-width: 991px) {
            .right-side { display: none; }
            .left-side { max-width: 100%; }
        }
    </style>
@endpush

@section('content')
<div class="full-wrapper">
    <div class="left-side">
        <div class="mb-5">
            <img src="{{ asset('assets/images/logo_thegame.png') }}" alt="Logo" style="width: 450px; height: auto;">
        </div>

        <h1 style="color: white; text-align: center; font-size: 3.5rem; margin-bottom: 0.5rem;">FTP <br> THE GAME</h1>
        <p style="color: #94a3b8; margin-bottom: 2rem;">Ingresa tus credenciales para continuar.</p>

        <form method="POST" action="{{ route('login') }}" style="width: 100%; max-width: 450px;">
            @csrf
            <div class="mb-4">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" placeholder="Correo Electrónico" required>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-4">
                <label for="password">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
        </form>
    </div>

    <div class="right-side"></div>
</div>
@endsection
