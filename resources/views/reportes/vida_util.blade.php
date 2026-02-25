@extends('layouts.app')

@section('content')
<style>
    :root {
        --dark-primary: #0f172a;
        --accent-blue: #2563eb;
        --glass-bg: rgba(255, 255, 255, 0.95);
    }
    body { background-color: #f8fafc; font-family: 'Inter', system-ui, sans-serif; }
    
    /* Efecto de elevación en tarjetas */
    .timeline-card {
        border: none;
        border-radius: 16px;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), shadow 0.3s ease;
        background: var(--glass-bg);
    }
    .timeline-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Línea lateral decorativa */
    .year-marker {
        position: relative;
        padding-left: 2.5rem;
    }
    .year-marker::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(to bottom, var(--accent-blue), transparent);
        border-radius: 4px;
    }

    /* Tipografía de lujo */
    .display-year {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--dark-primary);
        letter-spacing: -2px;
    }
    .month-badge {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        letter-spacing: 0.5px;
    }
</style>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-lg-6">
            <h6 class="text-primary fw-bold text-uppercase mb-2" style="letter-spacing: 2px;">Gestión de Activos</h6>
            <h1 class="display-5 fw-black text-dark mb-3">Master Plan de Vida Útil</h1>
            <p class="text-muted w-75">Proyección de renovaciones basada en las asignaciones actuales al personal docente y administrativo.</p>
        </div>
        <div class="col-lg-6">
            <div class="d-flex flex-column align-items-end gap-3">
                <!-- Buscador -->
                <form action="{{ route('reportes.vida_util') }}" method="GET" class="w-100" style="max-width: 400px;">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Buscar docente o DNI..." value="{{ $search ?? '' }}">
                        @if(request('search'))
                            <a href="{{ route('reportes.vida_util') }}" class="btn btn-light border-start-0" title="Limpiar filtro"><i class="bi bi-x-lg"></i></a>
                        @endif
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
                
                <div class="btn-group shadow-sm">
                    <a href="{{ route('reportes.index') }}" class="btn btn-white border px-4 fw-bold rounded-start">
                        <i class="bi bi-grid-3x3-gap me-2"></i>Menú
                    </a>
                    <button onclick="window.print()" class="btn btn-dark px-4 fw-bold rounded-end">
                        <i class="bi bi-printer me-2"></i>Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($proyeccionPorAnio->isEmpty())
        <div class="card p-5 text-center border-0 shadow-sm rounded-4">
            <div class="py-4">
                <i class="bi bi-calendar-x display-1 text-light"></i>
                <h3 class="mt-4 fw-bold">Sin datos para proyectar</h3>
                @if(request('search'))
                    <p class="text-muted">No se encontraron asignaciones para "<strong>{{ request('search') }}</strong>".</p>
                @else
                    <p class="text-muted">No hay asignaciones activas ('Entregado') para calcular renovaciones.</p>
                @endif
            </div>
        </div>
    @else
        @foreach($proyeccionPorAnio as $anio => $asignaciones)
            <div class="year-marker mb-5">
                <div class="d-flex align-items-baseline mb-4">
                    <span class="display-year">{{ $anio }}</span>
                    <span class="ms-3 text-muted fw-medium small">Renovaciones Programadas</span>
                </div>

                <div class="card timeline-card shadow-sm overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small text-uppercase fw-bold">
                                    <th class="ps-4 py-3">Mes Vencimiento</th>
                                    <th>Personal / Área</th>
                                    <th>EPP a Renovar</th>
                                    <th>Fecha Entrega</th>
                                    <th>Vida Útil</th>
                                    <th class="text-center">Estado Actual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($asignaciones as $item)
                                    @php
                                        $fecha = \Carbon\Carbon::parse($item->fecha_vencimiento);
                                        $hoy = now();
                                        $dias = (int) $hoy->diffInDays($fecha, false);
                                        $meses = (int) $hoy->diffInMonths($fecha, false);
                                    @endphp
                                    <tr class="border-bottom border-light">
                                        <td class="ps-4 py-4" style="width: 150px;">
                                            <div class="month-badge mb-1 text-center">{{ $fecha->translatedFormat('F') }}</div>
                                            <div class="text-center small text-muted fw-semibold">{{ $fecha->format('d/m/Y') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->personal->nombre_completo ?? 'N/A' }}</div>
                                            <span class="badge bg-light text-secondary border mt-1">{{ $item->personal->departamento->nombre ?? 'Sin Área' }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-primary">{{ $item->epp->nombre ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $item->epp->codigo_logistica ?? '' }}</small>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->fecha_entrega)->format('d/m/Y') }}</td>
                                        <td>{{ $item->epp->vida_util_meses ?? 12 }} meses</td>
                                        <td class="text-center">
                                            @if($fecha->isPast())
                                                <div class="px-3 py-2 bg-danger bg-opacity-10 text-danger rounded-3 d-inline-block">
                                                    <span class="fw-black small"><i class="bi bi-shield-exclamation me-1"></i> EXPIRO</span>
                                                </div>
                                            @elseif($dias < 30)
                                                <div class="px-3 py-2 bg-warning bg-opacity-10 text-dark rounded-3 d-inline-block">
                                                    <span class="fw-black h6 mb-0">{{ $dias }} Días</span>
                                                    <div class="small fw-bold opacity-75">RENOVAR</div>
                                                </div>
                                            @else
                                                <div class="px-3 py-2 bg-success bg-opacity-10 text-success rounded-3 d-inline-block">
                                                    <span class="fw-black h6 mb-0">{{ $meses }} Meses</span>
                                                    <div class="small fw-bold opacity-75">VIGENTE</div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<style>
    .fw-black { font-weight: 900; }
    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca; }
    .table tbody tr:last-child { border-bottom: none !important; }
</style>
@endsection