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
        <div class="col-lg-8">
            <h6 class="text-primary fw-bold text-uppercase mb-2" style="letter-spacing: 2px;">Gestión de Activos</h6>
            <h1 class="display-5 fw-black text-dark mb-3">Master Plan de Vida Útil</h1>
            <p class="text-muted w-75">Proyección estratégica anual para el reemplazo y mantenimiento de Equipos de Protección Personal (EPP).</p>
        </div>
        <div class="col-lg-4 text-lg-end d-flex align-items-end justify-content-lg-end">
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

    @if($proyeccionPorAnio->isEmpty())
        <div class="card p-5 text-center border-0 shadow-sm rounded-4">
            <div class="py-4">
                <i class="bi bi-calendar-x display-1 text-light"></i>
                <h3 class="mt-4 fw-bold">Sin datos para proyectar</h3>
                <p class="text-muted">No se detectaron fechas de vencimiento en la base de datos.</p>
            </div>
        </div>
    @else
        @foreach($proyeccionPorAnio as $anio => $epps)
            <div class="year-marker mb-5">
                <div class="d-flex align-items-baseline mb-4">
                    <span class="display-year">{{ $anio }}</span>
                    <span class="ms-3 text-muted fw-medium small">Ciclo de Renovación Anual</span>
                </div>

                <div class="card timeline-card shadow-sm overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small text-uppercase fw-bold">
                                    <th class="ps-4 py-3">Calendario</th>
                                    <th>Detalle Técnico</th>
                                    <th>Especificación</th>
                                    <th class="text-center">Estado de Vida Útil</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($epps as $epp)
                                    @php
                                        $fecha = \Carbon\Carbon::parse($epp->fecha_vencimiento);
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
                                            <div class="fw-bold text-dark fs-5 mb-0">{{ $epp->nombre }}</div>
                                            <div class="d-flex align-items-center mt-1">
                                                <span class="badge bg-soft-primary text-primary border-0 rounded-1 me-2" style="font-size: 0.65rem; background: #e0e7ff;">{{ $epp->codigo_logistica ?? '---' }}</span>
                                                <span class="text-muted small">{{ $epp->marca_modelo ?? '---' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted small mb-1">Frecuencia</div>
                                            <div class="fw-bold text-secondary small">{{ $epp->frecuencia_entrega ?? 'Cada uso' }}</div>
                                        </td>
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