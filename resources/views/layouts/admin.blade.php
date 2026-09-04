<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema FTP - THEGAME</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/logoTG.png') }}">


    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/iconly.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />


    <style>
        /* Estilos Select2 y Breadcrumb (se mantienen igual) */
        .select2-container--default .select2-selection--multiple { background-color: #1a1a24 !important; border: 1px solid #444 !important; }
        .select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: #333 !important; color: #ffffff !important; border: 1px solid #555 !important; }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: #ff4d4d !important; }
        .select2-dropdown { background-color: #1a1a24 !important; border: 1px solid #444 !important; }
        .select2-results__option { color: #ffffff !important; }
        .select2-results__option--highlighted[aria-selected] { background-color: #435ebe !important; color: #ffffff !important; }
        .select2-container--default .select2-search--inline .select2-search__field { color: #ffffff !important; }

        .breadcrumb-container { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
        .breadcrumb-item-link { font-size: 0.9rem; font-weight: 500; color: #ffffff; text-decoration: none; transition: 0.3s; }
        .breadcrumb-item-link:hover { color: #3498db !important; text-decoration: underline !important; }
        .breadcrumb-separator { color: #ffffff; opacity: 0.5; font-size: 0.8rem; }

        /* --- CORRECCIÓN DE FOOTER FIJO --- */

        /* 1. Asegurar que el footer sea fijo abajo */
        footer {
            position: fixed;
            bottom: 0;
            /* Ajusta este valor (260px) si tu sidebar es más ancho o estrecho */
            left: 300px;
            /* Calculamos el ancho restante restando el ancho del sidebar */
            width: calc(100% - 260px);
            background-color: #1a1a24;
            padding: 10px 20px;
            z-index: 1000;
            border-top: 1px solid #2d2d39;
        }

        /* 2. Padding al final para que el contenido no quede oculto tras el footer */
        .page-content {
            padding-bottom: 80px;
        }

        /* 3. Ajuste para que el sidebar no se solape (opcional) */
        #main {
            min-height: 100vh;
        }
    </style>

    @stack('styles')
</head>

<body>
    @php
        $isAuthPage = request()->routeIs('login');
    @endphp

    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
    <script>
        // Forzar modo oscuro permanente
        localStorage.setItem('theme', 'dark');
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        document.documentElement.classList.add('dark');
    </script>
    @if ($isAuthPage)
        @yield('content')
    @else
        <div id="app">
            <div id="sidebar">
                <div class="sidebar-wrapper active d-flex flex-column h-100">
                    <div class="sidebar-header position-relative">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="logo" >
                                <a href="{{ url('/admin') }}"><img src="{{ asset('assets/images/logo_thegame1.png') }}"
                                        alt="Logo The Game" srcset="" style="width: 100px; height: auto; "></a>
                            </div>

                        </div>

                    </div>
                    <div class="sidebar-menu flex-grow-1">
                        <ul class="menu">
                            <li class="sidebar-title">Menu</li>

                            <li class="sidebar-item {{ request()->routeIs('home') ? 'active' : '' }}">
                                <a href="{{ url('/home') }}" class='sidebar-link'>
                                    <i class="bi bi-grid-fill"></i>
                                    <span>Inicio</span>
                                </a>
                            </li>

                            @can('Ver formulario de ajustes')
                                <li class="sidebar-item {{ request()->routeIs('admin.ajustes.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.ajustes.index') }}" class='sidebar-link'>
                                        <i class="bi bi-sliders"></i>
                                        <span>Ajustes</span>
                                    </a>
                                </li>
                            @endcan



                            @can('Ver listado de roles')
                                <li class="sidebar-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.roles.index') }}" class='sidebar-link'>
                                        <i class="bi bi-shield-lock"></i>
                                        <span>Roles</span>
                                    </a>
                                </li>
                            @endcan


                            @can('Ver listado de usuarios')
                                <li class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.users.index') }}" class='sidebar-link'>
                                        <i class="bi bi-people"></i>
                                        <span>Usuarios</span>
                                    </a>
                                </li>
                            @endcan

                            @can('Ver listado de carpetas')
                                <li class="sidebar-title">Gestión de Archivos</li>
                                <li class="sidebar-item {{ request()->routeIs('admin.carpetas.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.carpetas.index') }}" class='sidebar-link'>
                                        <i class="bi bi-folder-fill"></i>
                                        <span>Mis Carpetas</span>
                                    </a>
                                </li>
                            @endcan

                            @can('Ver historial de actividad')
                                <li class="sidebar-title">Auditoría</li>
                                <li class="sidebar-item {{ request()->routeIs('admin.historial.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.historial.index') }}" class='sidebar-link'>
                                        <i class="bi bi-clock-history"></i>
                                        <span>Historial de Actividad</span>
                                    </a>
                                </li>
                            @endcan
                            

                            {{--  <li class="sidebar-item">
                                <a href="{{ route('logout') }}" class='sidebar-link'
                                    onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Cerrar sesion</span>
                                </a>
                                <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST"
                                    class="d-none">
                                    @csrf
                                </form>
                            </li> --}}

                        </ul>
                    </div>


                    <div class="sidebar-user dropdown px-4 mb-3">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                            id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar avatar-md d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                @if (auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar"
                                        class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="ms-2">
                                <h6 class="mb-0 text-gray-800">{{ auth()->user()->name }}</h6>
                                <small class="text-muted"
                                    style="font-size: 0.7rem;">{{ auth()->user()->getRoleNames()->first() }}</small>
                            </div>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow"
                            aria-labelledby="dropdownUser1">
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                </a>
                            </li>
                        </ul>
                    </div>

                    <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
            <div id="main">
                <header class="mb-3">
                    <a href="#" class="burger-btn d-block d-xl-none">
                        <i class="bi bi-justify fs-3"></i>
                    </a>
                </header>

                <div class="page-content" >
                    @yield('content')
                </div>


            </div>
            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2026 &copy; Erick Fernando Morales Gil</p>
                    </div>
                    <div class="float-end">
                        <p><span class="text-primary"><i class="bi bi-facebook"></i></span>
                                <a href="https://www.facebook.com/erick.fernando.morales.gil">Erick Fernando Morales Gil</a></p>
                    </div>
                </div>
            </footer>
        </div>
        <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
        <script src="{{ asset('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>


        <script src="{{ asset('assets/compiled/js/app.js') }}"></script>



        <!-- Need: Apexcharts -->
        <script src="{{ asset('assets/extensions/apexcharts/apexcharts.min.js') }}"></script>
        <script src="{{ asset('assets/static/js/pages/dashboard.js') }}"></script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function() {
            if (typeof Swal === 'undefined') {
                return;
            }

            const flashMessages = {
                success: @json(session('success')),
                error: @json(session('error')),
                warning: @json(session('warning')),
                info: @json(session('info')),
                status: @json(session('status')),
            };

            const priority = ['success', 'error', 'warning', 'info', 'status'];
            const foundType = priority.find(function(type) {
                return !!flashMessages[type];
            });

            if (!foundType) {
                return;
            }

            const icon = foundType === 'status' ? 'success' : foundType;
            Swal.fire({
                position: 'top-end',
                icon: icon,
                title: flashMessages[foundType],
                showConfirmButton: false,
                timer: 5000,
            });
        })();
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('scripts')

</body>

</html>
