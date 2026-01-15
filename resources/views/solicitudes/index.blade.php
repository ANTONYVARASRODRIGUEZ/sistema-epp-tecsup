@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #000;">Centro de Solicitudes</h1>
        <p class="text-muted">Aprueba o rechaza solicitudes de renovación y entregas</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row mb-4 g-3">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Pendientes</p>
                        <h3 class="fw-bold">{{ $pendientes }}</h3>
                    </div>
                    <span class="badge bg-warning-subtle text-warning rounded-pill p-3">
                        <i class="bi bi-bell-fill"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Aprobadas</p>
                        <h3 class="fw-bold">{{ $aprobadas }}</h3>
                    </div>
                    <span class="badge bg-success-subtle text-success rounded-pill p-3">
                        <i class="bi bi-check-lg"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Rechazadas</p>
                        <h3 class="fw-bold">{{ $rechazadas }}</h3>
                    </div>
                    <span class="badge bg-danger-subtle text-danger rounded-pill p-3">
                        <i class="bi bi-x-lg"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
        <form class="row g-3 align-items-end" method="GET">
            <div class="col-md-6">
                <label class="text-muted small mb-1">Filtrar por estado</label>
                <select name="estado" class="form-select">
                    <option value="todos" {{ $estadoFiltro === 'todos' ? 'selected' : '' }}>Todos</option>
                    <option value="pendiente" {{ $estadoFiltro === 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                    <option value="aprobado" {{ $estadoFiltro === 'aprobado' ? 'selected' : '' }}>Aprobadas</option>
                    <option value="rechazado" {{ $estadoFiltro === 'rechazado' ? 'selected' : '' }}>Rechazadas</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="text-muted small mb-1">Filtrar por tipo</label>
                <select name="tipo" class="form-select">
                    <option value="todos" {{ $tipoFiltro === 'todos' ? 'selected' : '' }}>Todos</option>
                    @foreach($tiposDisponibles as $valor => $label)
                        <option value="{{ $valor }}" {{ $tipoFiltro === $valor ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-dark" style="border-radius: 12px;">Aplicar</button>
            </div>
        </form>
    </div>

    <!-- TABLA DE SOLICITUDES -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Usuario</th>
                            <th>EPP</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                        <tr class="solicitud-row" data-estado="{{ $solicitud->estado }}" data-tipo="solicitud_epp">
                            <td>
                                <span class="badge bg-secondary-subtle text-dark">Solicitud de EPP</span>
                            </td>
                            <td class="fw-bold">{{ $solicitud->user->name ?? 'Usuario' }}</td>
                            <td>{{ $solicitud->epp->nombre ?? 'Equipo' }}</td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $solicitud->cantidad }}</span>
                            </td>
                            <td>{{ $solicitud->created_at?->format('Y-m-d') }}</td>
                            <td>
                                @php
                                    $estadoBadge = [
                                        'pendiente' => 'bg-warning text-dark',
                                        'aprobado' => 'bg-success',
                                        'rechazado' => 'bg-danger',
                                    ][$solicitud->estado] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $estadoBadge }}">{{ ucfirst($solicitud->estado) }}</span>
                            </td>
                            <td class="text-center">
                                @if($solicitud->estado === 'pendiente')
                                    <form action="{{ route('solicitudes.aprobar', $solicitud->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Aprobar">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('solicitudes.rechazar', $solicitud->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" title="Rechazar">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                        <i class="bi bi-lock"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                <p class="mt-2">No hay solicitudes que coincidan con los filtros</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .table-custom {
        font-size: 0.95rem;
    }

    .table-custom thead {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .table-custom tbody tr {
        border-bottom: 1px solid #f0f0f0;
    }

    .table-custom tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge-role {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 500;
    }

    .btn-action {
        background-color: transparent;
        color: #003366;
        border: none;
        font-size: 1.1rem;
    }

    .btn-action:hover {
        color: #003366;
        background-color: #f0f4f8;
    }
</style>

<script>
    document.getElementById('filterEstado').addEventListener('change', filterSolicitudes);
    document.getElementById('filterTipo').addEventListener('change', filterSolicitudes);

    function filterSolicitudes() {
        const estado = document.getElementById('filterEstado').value;
        const tipo = document.getElementById('filterTipo').value;
        const rows = document.querySelectorAll('.solicitud-row');

        rows.forEach(row => {
            const rowEstado = row.getAttribute('data-estado');
            const rowTipo = row.getAttribute('data-tipo');

            const estadoMatch = !estado || rowEstado === estado;
            const tipoMatch = !tipo || rowTipo === tipo;

            row.style.display = estadoMatch && tipoMatch ? '' : 'none';
        });
    }
</script>
@endsection
