
@extends('layouts.app')

@section('content')

<style>
    /* CONFIGURACIÓN GENERAL */
    body {
        margin: 0;
    }

    .ftp-recovery,
    .ftp-recovery * {
        box-sizing: border-box;
    }

    /* FONDO PERSONALIZADO */
    .ftp-recovery {
        position: relative;
        min-height: 100vh;
        min-height: 100svh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 16px;

        background:
            linear-gradient(
                rgba(0, 8, 24, 0.20),
                rgba(0, 8, 24, 0.50)
            ),
            url("{{ asset('assets/images/FONO CONTRASEÑA.png') }}")
            center center / cover no-repeat;

        background-color: #020b18;
        font-family: Arial, sans-serif;
    }

    /* PANEL CENTRAL */
    .ftp-recovery-card {
        width: 100%;
        max-width: 460px;
        padding: 35px 32px;

        border: 1px solid rgba(0, 153, 255, 0.65);
        border-radius: 18px;

        background: rgba(2, 13, 32, 0.94);

        box-shadow:
            0 0 35px rgba(0, 153, 255, 0.25),
            0 20px 60px rgba(0, 0, 0, 0.45);

        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);

        text-align: center;
        color: #ffffff;
    }

    /* LOGO THE GAME */
    .ftp-recovery-logo {
        display: block;
        width: 100px;
        height: 100px;
        object-fit: contain;
        margin: 0 auto 16px;
    }

    /* ICONO DE SEGURIDAD */
    .ftp-recovery-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        width: 55px;
        height: 55px;
        margin-bottom: 18px;

        border: 1px solid rgba(59, 130, 246, 0.30);
        border-radius: 50%;

        background: rgba(37, 99, 235, 0.18);
        color: #60a5fa;
    }

    .ftp-recovery-icon svg {
        width: 28px;
        height: 28px;
    }

    /* TÍTULO */
    .ftp-recovery-card h1 {
        color: #ffffff;
        font-size: 24px;
        font-weight: 700;
        line-height: 1.3;
        margin: 0 0 10px;
    }

    /* SUBTÍTULO */
    .ftp-recovery-subtitle {
        color: #38bdf8;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        line-height: 1.6;
    }

    /* SEPARADOR */
    .ftp-recovery-divider {
        width: 100%;
        height: 1px;
        margin: 22px 0 0;

        background: linear-gradient(
            to right,
            transparent,
            rgba(56, 189, 248, 0.60),
            transparent
        );
    }

    /* CUADRO INFORMATIVO */
    .ftp-recovery-message {
        margin-top: 24px;
        padding: 20px;

        border: 1px solid rgba(59, 130, 246, 0.40);
        border-radius: 12px;

        background: rgba(30, 58, 138, 0.25);
        text-align: left;
    }

    .ftp-recovery-message h2 {
        display: flex;
        align-items: center;
        gap: 9px;

        color: #93c5fd;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.4;
        margin: 0 0 12px;
    }

    .ftp-recovery-message h2 svg {
        width: 19px;
        height: 19px;
        flex-shrink: 0;
    }

    .ftp-recovery-message p {
        color: #e4e4e7;
        font-size: 14px;
        line-height: 1.7;
        margin: 0 0 12px;
    }

    .ftp-recovery-message p:last-child {
        margin-bottom: 0;
        color: #ffffff;
    }

    /* BOTÓN VOLVER */
    .ftp-recovery-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;

        width: 100%;
        margin-top: 24px;
        padding: 13px 18px;

        border: 1px solid rgba(96, 165, 250, 0.35);
        border-radius: 9px;

        background: linear-gradient(
            135deg,
            #2563eb,
            #0066ff
        );

        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);

        color: #ffffff !important;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none !important;

        transition:
            background 0.2s ease,
            box-shadow 0.2s ease,
            transform 0.2s ease;
    }

    .ftp-recovery-button:hover {
        background: linear-gradient(
            135deg,
            #1d4ed8,
            #0052cc
        );

        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.40);
        transform: translateY(-1px);
    }

    .ftp-recovery-button svg {
        width: 17px;
        height: 17px;
    }

    /* PIE DE PÁGINA */
    .ftp-recovery-footer {
        margin-top: 22px;
        color: #a1a1aa;
        font-size: 11px;
        line-height: 1.6;
    }

    /* DISEÑO RESPONSIVO */
    @media (max-width: 480px) {
        .ftp-recovery {
            padding: 24px 12px;
        }

        .ftp-recovery-card {
            padding: 28px 20px;
        }

        .ftp-recovery-card h1 {
            font-size: 21px;
        }

        .ftp-recovery-logo {
            width: 85px;
            height: 85px;
        }

        .ftp-recovery-message {
            padding: 16px;
        }

        .ftp-recovery-message p {
            font-size: 13px;
        }

        .ftp-recovery-subtitle {
            font-size: 10px;
            letter-spacing: 1px;
        }
    }
</style>

<!-- PANTALLA DE RECUPERACIÓN -->
<div class="ftp-recovery">

    <!-- PANEL CENTRAL -->
    <div class="ftp-recovery-card">

        <!-- LOGO DE THE GAME SRL -->
        <img
            src="{{ asset('assets/images/LOGO THE GAME.png') }}"
            alt="Logo THE GAME SRL"
            class="ftp-recovery-logo"
        >

        <!-- ICONO DE SEGURIDAD -->
        <div>
            <span class="ftp-recovery-icon">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12l2 2 4-4"
                    />
                </svg>
            </span>
        </div>

        <!-- TÍTULO -->
        <h1>Recuperación de contraseña</h1>

        <!-- SUBTÍTULO -->
        <div class="ftp-recovery-subtitle">
            Sistema FTP - THE GAME SRL
        </div>

        <!-- SEPARADOR -->
        <div class="ftp-recovery-divider"></div>

        <!-- MENSAJE INFORMATIVO -->
        <div class="ftp-recovery-message">

            <h2>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M2.25 12a9.75 9.75 0 1119.5 0 9.75 9.75 0 01-19.5 0zm9.75-4.5a1.125 1.125 0 100 2.25 1.125 1.125 0 000-2.25zm-1.125 4.5a.75.75 0 000 1.5h.375v3a.75.75 0 001.5 0v-3.75a.75.75 0 00-.75-.75h-1.125z"
                        clip-rule="evenodd"
                    />
                </svg>

                Restablecimiento de acceso
            </h2>

            <p>
                Por motivos de seguridad, el restablecimiento
                de contraseñas es gestionado únicamente por
                el administrador del sistema.
            </p>

            <p>
                Comuníquese con el administrador para solicitar
                el restablecimiento de su contraseña.
            </p>

        </div>

        <!-- BOTÓN REGRESAR AL LOGIN -->
        <a
            href="{{ route('login') }}"
            class="ftp-recovery-button"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M19 12H5m0 0l7-7m-7 7l7 7"
                />
            </svg>

            Volver al inicio de sesión
        </a>

        <!-- DERECHOS RESERVADOS -->
        <div class="ftp-recovery-footer">
            © {{ date('Y') }} THE GAME SRL.
            Todos los derechos reservados.
        </div>

    </div>

</div>

@endsection
