@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Reporte de Incidencias y Bajas</h2>
            <p class="text-muted mb-0">Listado de EPPs reportados como dañados, perdidos o dados de baja.</p>
        </div>
        <div>
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill me-2">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <button onclick="window.print()" class="btn btn-dark rounded-pill shadow-sm">
                <i class="bi bi-printer me-2"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            @if($incidencias->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill text-success fs-1"></i>
                    <p class="text-muted mt-2">No se han reportado incidencias hasta la fecha.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">EPP</th>
                                <th>Personal Asignado</th>
                                <th>Departamento</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Estado Incidencia</th>
                                <th>Fecha Reporte</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incidencias as $incidencia)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $incidencia->epp->nombre ?? 'N/A' }}</td>
                                <td>{{ $incidencia->personal->nombre_completo ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-light text-secondary border">
                                        {{ $incidencia->personal->departamento->nombre ?? 'Sin Depto.' }}
                                    </span>
                                </td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $incidencia->cantidad }}</span></td>
                                <td class="text-center"><span class="badge bg-{{ $incidencia->estado == 'Dañado' ? 'warning text-dark' : 'danger' }}">{{ $incidencia->estado }}</span></td>
                                <td>{{ $incidencia->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection