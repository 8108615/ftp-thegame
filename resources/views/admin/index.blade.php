@extends('layouts.admin')

@push('styles')
    <style>
        .dashboard-stats {
            gap: 1rem;
        }

        .dashboard-card {
            border-radius: 1.25rem;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            border: none;
        }

        .dashboard-card .card-body {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .dashboard-card-icon {
            width: 64px;
            height: 64px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
            line-height: 1;
        }

        .dashboard-card-title {
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
            color: #6b7280;
        }

        .dashboard-card-value {
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.35rem;
        }

        .dashboard-card-desc {
            margin: 0;
            color: #6b7280;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Dashboard</h3>
                <p class="text-muted">Resumen ejecutivo de los módulos activos en el sistema.</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row dashboard-stats">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="dashboard-card-icon bg-primary">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <div>
                            <p class="dashboard-card-title">Usuarios</p>
                            <p class="dashboard-card-value">{{ $stats['usuarios'] }}</p>
                            <p class="dashboard-card-desc">Total de usuarios registrados</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="dashboard-card-icon bg-info">
                            <i class="bi bi-shield-lock-fill fs-4"></i>
                        </div>
                        <div>
                            <p class="dashboard-card-title">Roles</p>
                            <p class="dashboard-card-value">{{ $stats['roles'] }}</p>
                            <p class="dashboard-card-desc">Niveles de acceso disponibles</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mt-4">
            <div class="col-12 col-xl-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Estado del sistema</h5>
                        <div class="mb-3">
                            <strong>Configuración:</strong>
                            <span class="badge bg-{{ $stats['configuracion'] ? 'success' : 'danger' }}">
                                {{ $stats['configuracion'] ? 'Cargada' : 'Faltante' }}
                            </span>
                        </div>
                        @if ($config)
                            <div class="mb-2"><strong>Nombre:</strong> {{ $config->nombre }}</div>
                            <div class="mb-2"><strong>Email:</strong> {{ $config->email }}</div>
                            <div class="mb-2"><strong>Divisa:</strong> {{ $config->divisa }}</div>
                        @else
                            <div class="alert alert-warning mb-0">Aún no hay configuración general.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
