@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Historial de Asignaciones</h2>
            <p class="text-muted">Control de equipos entregados al personal</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Personal</th>
                            <th>Equipo (EPP)</th>
                            <th>Cant.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignaciones as $asignacion)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($asignacion->fecha_entrega)->format('d/m/Y') }}</td>
                            <td>
                                <div class="fw-bold">{{ $asignacion->personal->nombre_completo }}</div>
                                <small class="text-muted">{{ $asignacion->personal->dni }}</small>
                            </td>
                            <td>{{ $asignacion->epp->nombre }}</td>
                            <td><span class="badge bg-info text-dark">{{ $asignacion->cantidad }}</span></td>
                            <td>
                                <span class="badge rounded-pill {{ $asignacion->estado == 'Posee' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $asignacion->estado }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No hay asignaciones registradas todavía.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection