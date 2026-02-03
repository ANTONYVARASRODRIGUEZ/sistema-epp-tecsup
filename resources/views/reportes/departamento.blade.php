@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Reporte de Asignaciones por Área</h2>
            <p class="text-muted mb-0">Consulte los equipos entregados al personal de cada departamento.</p>
        </div>
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <!-- Filtro -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form action="{{ route('reportes.departamento') }}" method="GET" class="row align-items-end">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Seleccione Departamento</label>
                    <select name="departamento_id" class="form-select form-select-lg bg-light border-0" onchange="this.form.submit()">
                        <option value="">-- Seleccionar --</option>
                        @foreach($departamentos as $depto)
                            <option value="{{ $depto->id }}" {{ (isset($departamentoSeleccionado) && $departamentoSeleccionado->id == $depto->id) ? 'selected' : '' }}>
                                {{ $depto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    @if(isset($departamentoSeleccionado))
                        <button type="button" onclick="window.print()" class="btn btn-dark rounded-pill px-4">
                            <i class="bi bi-printer me-2"></i> Imprimir Reporte
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(isset($departamentoSeleccionado))
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-building me-2"></i> {{ $departamentoSeleccionado->nombre }}
                </h5>
            </div>
            <div class="card-body p-0">
                @if($personal->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted fs-1"></i>
                        <p class="text-muted mt-2">No hay personal registrado en este departamento.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Docente / Personal</th>
                                    <th>DNI</th>
                                    <th>Equipos Asignados (EPP)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($personal as $persona)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $persona->nombre_completo }}</div>
                                        <small class="text-muted">{{ $persona->carrera }}</small>
                                    </td>
                                    <td>{{ $persona->dni }}</td>
                                    <td>
                                        @if($persona->asignaciones->where('estado', 'Entregado')->isEmpty())
                                            <span class="badge bg-light text-muted border">Sin asignaciones activas</span>
                                        @else
                                            <ul class="list-unstyled mb-0">
                                                @foreach($persona->asignaciones->where('estado', 'Entregado') as $asignacion)
                                                    <li class="mb-1">
                                                        <i class="bi bi-check2-circle text-success me-1"></i>
                                                        {{ $asignacion->epp->nombre }} 
                                                        <span class="fw-bold">x{{ $asignacion->cantidad }}</span>
                                                        <small class="text-muted ms-1">({{ $asignacion->fecha_entrega ? \Carbon\Carbon::parse($asignacion->fecha_entrega)->format('d/m/Y') : 'S/F' }})</small>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection