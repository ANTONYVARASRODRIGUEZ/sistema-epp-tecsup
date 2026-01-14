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

    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 small">Pendientes</p>
                            <h2 class="fw-bold" style="color: #ffc107;">{{ $pendientes }}</h2>
                        </div>
                        <div style="font-size: 2rem; color: #ffc107; opacity: 0.3;">
                            <i class="bi bi-bell"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #28a745;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 small">Aprobadas</p>
                            <h2 class="fw-bold" style="color: #28a745;">{{ $aprobadas }}</h2>
                        </div>
                        <div style="font-size: 2rem; color: #28a745; opacity: 0.3;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #dc3545;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 small">Rechazadas</p>
                            <h2 class="fw-bold" style="color: #dc3545;">{{ $rechazadas }}</h2>
                        </div>
                        <div style="font-size: 2rem; color: #dc3545; opacity: 0.3;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Filtrar por estado</label>
            <select class="form-select" id="filterEstado">
                <option value="">Todos</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Aprobado">Aprobado</option>
                <option value="Rechazado">Rechazado</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Filtrar por tipo</label>
            <select class="form-select" id="filterTipo">
                <option value="">Todos</option>
                <option value="Nuevo">Nuevo</option>
                <option value="Renovación">Renovación</option>
                <option value="Devolución">Devolución</option>
            </select>
        </div>
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
                        <tr class="solicitud-row" data-estado="{{ $solicitud['estado'] }}" data-tipo="{{ $solicitud['tipo'] }}">
                            <td>
                                @if($solicitud['tipo'] === 'Nuevo')
                                    <span class="badge bg-info">Nuevo</span>
                                @elseif($solicitud['tipo'] === 'Renovación')
                                    <span class="badge bg-warning">Renovación</span>
                                @else
                                    <span class="badge bg-secondary">Devolución</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $solicitud['usuario'] }}</td>
                            <td>{{ $solicitud['epp'] }}</td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $solicitud['cantidad'] }}</span>
                            </td>
                            <td>{{ $solicitud['fecha'] }}</td>
                            <td>
                                @if($solicitud['estado'] === 'Pendiente')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @elseif($solicitud['estado'] === 'Aprobado')
                                    <span class="badge bg-success">Aprobado</span>
                                @else
                                    <span class="badge bg-danger">Rechazado</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($solicitud['estado'] === 'Pendiente')
                                    <form action="{{ route('solicitudes.aprobar', $solicitud['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Aprobar">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('solicitudes.rechazar', $solicitud['id']) }}" method="POST" class="d-inline">
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
