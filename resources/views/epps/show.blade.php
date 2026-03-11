@extends('layouts.app')

@section('content')
@php
    // Calcular estado de vencimiento dinámicamente
    $vencimientoReal = null;
    $estadoVencimiento = null;
    if ($epp->created_at && $epp->vida_util_meses) {
        $vencimientoReal = \Carbon\Carbon::parse($epp->created_at)->addMonths($epp->vida_util_meses);
        $hoy = \Carbon\Carbon::today();
        if ($vencimientoReal->lt($hoy)) {
            $estadoVencimiento = 'vencido';
        } elseif ($hoy->diffInDays($vencimientoReal) <= 30) {
            $estadoVencimiento = 'proximo';
        } else {
            $estadoVencimiento = 'vigente';
        }
    }

    $estadoConfig = [
        'vencido'  => ['bg' => '#f8d7da', 'text' => '#721c24', 'label' => 'Vencido',             'icon' => 'bi-x-circle-fill'],
        'proximo'  => ['bg' => '#fff3cd', 'text' => '#856404', 'label' => 'Próximo a vencer',     'icon' => 'bi-exclamation-triangle-fill'],
        'vigente'  => ['bg' => '#d4edda', 'text' => '#155724', 'label' => 'Vigente',              'icon' => 'bi-check-circle-fill'],
    ];
    $stCfg = $estadoVencimiento ? $estadoConfig[$estadoVencimiento] : ['bg' => '#e2e3e5', 'text' => '#383d41', 'label' => 'Sin fecha', 'icon' => 'bi-dash-circle'];
@endphp

<style>
    .epp-image-container {
        height: clamp(200px, 35vw, 400px);
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        display: flex; align-items: center; justify-content: center;
    }

    /* Badges flexibles */
    .badge { word-break: break-word; white-space: normal; text-align: left; }

    /* ── RESPONSIVE ── */

    /* En xs: imagen más compacta, columnas apiladas */
    @media (max-width: 575.98px) {
        .epp-image-container { height: 220px; }

        /* Botones de acción full width en xs */
        .action-buttons .btn { width: 100%; justify-content: center; }

        /* Ajuste de fuentes en detalle */
        .detail-title { font-size: 1.1rem !important; }
        .detail-label { font-size: 0.72rem !important; }
        .detail-value { font-size: 0.9rem !important; }

        /* Modal footer en xs */
        .modal-footer-responsive { flex-direction: column; }
        .modal-footer-responsive .btn { width: 100%; }
        .modal-footer-responsive form { width: 100%; }
    }

    @media (min-width: 576px) {
        .w-sm-auto { width: auto !important; }
    }

    /* Imagen en md: ocupa toda la columna */
    @media (min-width: 768px) {
        .epp-image-container { height: clamp(260px, 35vw, 400px); }
    }

    /* Fechas box */
    .fechas-box {
        background: #f0f7ff;
        border-radius: 12px;
        padding: 1rem;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-3 py-md-4">

    {{-- ── HEADER ── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="font-size: clamp(1.2rem, 4vw, 1.6rem);">Detalles del EPP</h2>
            <p class="text-muted mb-0 small">Información completa de {{ $epp->nombre }}</p>
        </div>
        <a href="{{ route('epps.index') }}" class="btn btn-outline-secondary shadow-sm flex-shrink-0">
            <i class="bi bi-arrow-left me-1"></i> Volver al catálogo
        </a>
    </div>

    <div class="row g-4">

        {{-- ── COLUMNA IMAGEN ── --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="epp-image-container">
                    @if($epp->imagen)
                        <img src="{{ asset('storage/' . $epp->imagen) }}"
                             class="img-fluid"
                             style="max-height: 100%; max-width: 100%; object-fit: contain; padding: 20px;"
                             onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(135deg,#f5f7fa,#e8ecf0);color:#aab0b8;\'><i class=\'bi bi-shield-slash\' style=\'font-size:3rem;opacity:0.4;\'></i><p style=\'font-size:0.7rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;opacity:0.5;margin-top:8px;\'>Sin Imagen</p></div>'">
                    @else
                        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(135deg,#f5f7fa,#e8ecf0);color:#aab0b8;">
                            <i class="bi bi-shield-slash" style="font-size:3rem;opacity:0.4;"></i>
                            <p style="font-size:0.7rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;opacity:0.5;margin-top:8px;">Sin Imagen</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Ficha Técnica --}}
            @if($epp->ficha_tecnica)
            <div class="mt-3">
                <a href="{{ asset('storage/' . $epp->ficha_tecnica) }}" target="_blank"
                   class="btn btn-primary w-100"
                   style="background-color: #003366; border: none;">
                    <i class="bi bi-file-earmark-pdf me-2"></i> Descargar Ficha Técnica
                </a>
            </div>
            @endif
        </div>

        {{-- ── COLUMNA DETALLES ── --}}
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3 p-sm-4">

                    {{-- Encabezado --}}
                    <div class="mb-4 pb-3" style="border-bottom: 2px solid #003366;">
                        <h3 class="fw-bold mb-2 detail-title" style="color: #333; font-size: clamp(1rem, 3vw, 1.4rem);">
                            {{ Str::ucfirst($epp->nombre) }}
                        </h3>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="badge" style="background-color: #003366;">{{ $epp->tipo }}</span>
                            @if($epp->departamentos && $epp->departamentos->count())
                                @foreach($epp->departamentos as $depto)
                                    <span class="badge bg-info">{{ $depto->nombre }}</span>
                                @endforeach
                            @elseif($epp->departamento_id)
                                <span class="badge bg-info">{{ $epp->departamento->nombre ?? 'Departamento desconocido' }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Descripción --}}
                    @if($epp->descripcion)
                    <div class="mb-4">
                        <h6 class="fw-bold text-uppercase text-muted mb-2 detail-label" style="font-size: 0.8rem;">Descripción</h6>
                        <p class="mb-0">{{ $epp->descripcion }}</p>
                    </div>
                    @endif

                    {{-- Código y Marca --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <h6 class="fw-bold text-uppercase text-muted mb-1 detail-label" style="font-size: 0.8rem;">Código Logística</h6>
                            <p class="fw-bold mb-0 detail-value" style="color: #003366; font-size: clamp(0.9rem, 2vw, 1.1rem);">
                                {{ $epp->codigo_logistica ?? '—' }}
                            </p>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-bold text-uppercase text-muted mb-1 detail-label" style="font-size: 0.8rem;">Marca/Modelo</h6>
                            <p class="fw-bold mb-0 detail-value">{{ $epp->marca_modelo ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Vida útil y Frecuencia --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <h6 class="fw-bold text-uppercase text-muted mb-1 detail-label" style="font-size: 0.8rem;">Vida Útil</h6>
                            <p class="fw-bold mb-0 detail-value">
                                {{ $epp->vida_util_meses >= 12 ? ($epp->vida_util_meses / 12).' Años' : $epp->vida_util_meses.' Meses' }}
                            </p>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-bold text-uppercase text-muted mb-1 detail-label" style="font-size: 0.8rem;">Frecuencia de Entrega</h6>
                            <p class="fw-bold mb-0 detail-value">{{ $epp->frecuencia_entrega ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Estado de Vencimiento --}}
                    <div class="mb-4">
                        <h6 class="fw-bold text-uppercase text-muted mb-2 detail-label" style="font-size: 0.8rem;">Estado</h6>
                        <span class="badge d-inline-flex align-items-center gap-1"
                              style="background-color: {{ $stCfg['bg'] }}; color: {{ $stCfg['text'] }}; font-size: 0.875rem; padding: 7px 12px;">
                            <i class="bi {{ $stCfg['icon'] }}"></i>
                            {{ $stCfg['label'] }}
                        </span>
                    </div>

                    {{-- Fechas --}}
                    <div class="fechas-box mb-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <h6 class="fw-bold text-uppercase text-muted mb-1 detail-label" style="font-size: 0.8rem;">Fecha de Registro</h6>
                                <p class="fw-bold mb-0 detail-value">
                                    {{ $epp->created_at ? \Carbon\Carbon::parse($epp->created_at)->format('d/m/Y') : '—' }}
                                </p>
                            </div>
                            <div class="col-6">
                                <h6 class="fw-bold text-uppercase text-muted mb-1 detail-label" style="font-size: 0.8rem;">Fecha de Vencimiento</h6>
                                <p class="fw-bold mb-0 detail-value"
                                   style="color: {{ $estadoVencimiento === 'vencido' ? '#dc3545' : ($estadoVencimiento === 'proximo' ? '#fd7e14' : 'inherit') }};">
                                    {{ $vencimientoReal ? $vencimientoReal->format('d/m/Y') : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- Botones de Acción --}}
                    <div class="action-buttons d-flex flex-column flex-sm-row gap-2 justify-content-end">
                        <a href="{{ route('epps.edit', $epp->id) }}"
                           class="btn btn-primary d-flex align-items-center justify-content-center"
                           style="background-color: #003366; border: none;">
                            <i class="bi bi-pencil me-2"></i> Editar
                        </a>
                        <button type="button"
                                class="btn btn-danger d-flex align-items-center justify-content-center"
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-2"></i> Eliminar
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL ELIMINACIÓN ── --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
        <div class="modal-content border-0 shadow" style="border-radius: 14px;">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0">
                    ¿Estás seguro de que deseas eliminar el EPP <strong>"{{ $epp->nombre }}"</strong>?
                </p>
                <p class="text-muted small mt-2 mb-0">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer border-0 modal-footer-responsive d-flex flex-column flex-sm-row gap-2">
                <button type="button" class="btn btn-light w-100 w-sm-auto" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
                <form action="{{ route('epps.destroy', $epp->id) }}" method="POST" class="w-100 w-sm-auto">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash me-1"></i> Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection