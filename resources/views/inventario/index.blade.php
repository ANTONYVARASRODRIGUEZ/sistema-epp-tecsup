@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Header con KPIs Rápidos --}}
    <div class="row mb-4">
        <div class="col-12 col-md-8">
            <h3 class="fw-bold text-dark mb-1">Gestión de Existencias</h3>
            <p class="text-muted">Control centralizado de stock para equipos de protección.</p>
        </div>
        <div class="col-12 col-md-4 text-md-end">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0"
                       placeholder="Buscar por código o nombre...">
            </div>
        </div>
    </div>

    {{-- Tarjetas de Resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-white" style="background-color:#003366;">
                <div class="card-body">
                    <div class="d-flex justify-content-between px-2">
                        <div>
                            <p class="mb-0 opacity-75 small fw-bold">TOTAL EPPs</p>
                            <h3 class="mb-0 fw-bold">{{ $epps->count() }}</h3>
                        </div>
                        <i class="bi bi-box-seam fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between px-2">
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Stock Crítico</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ $epps->where('stock', '<=', 5)->count() }}</h3>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between px-2">
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Sin Stock</p>
                            <h3 class="mb-0 fw-bold text-danger">{{ $epps->where('stock', '<=', 0)->count() }}</h3>
                        </div>
                        <i class="bi bi-x-octagon fs-1 text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                <thead style="background:#1a1a1a; color:#fff; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase;">
                    <tr>
                        <th class="ps-4 py-3">Código Logístico</th>
                        <th>Descripción del EPP</th>
                        <th class="text-center">Stock Actual</th>
                        <th class="text-center">Nivel</th>
                        <th class="text-end pe-4">Gestión</th>
                    </tr>
                </thead>
                <tbody class="bg-white" id="inventarioBody">
                    @forelse($epps as $epp)
                    <tr class="border-bottom inventario-row"
                        data-nombre="{{ strtolower($epp->nombre) }}"
                        data-codigo="{{ strtolower($epp->codigo_logistica ?? '') }}">
                        <td class="ps-4">
                            <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                {{ $epp->codigo_logistica ?? '---' }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark" style="font-size:0.95rem;">{{ $epp->nombre }}</div>
                            <small class="text-muted" style="font-size:0.8rem;">{{ $epp->marca_modelo ?? 'Estándar' }}</small>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold fs-5 {{ ($epp->stock ?? 0) <= 5 ? 'text-danger' : 'text-dark' }}">
                                {{ $epp->stock ?? 0 }}
                            </span>
                            <small class="text-muted"> uds</small>
                        </td>
                        <td class="text-center">
                            @if(($epp->stock ?? 0) <= 0)
                                <span class="badge px-3 py-2 rounded-pill small" style="background:#f8d7da; color:#842029;">Agotado</span>
                            @elseif(($epp->stock ?? 0) <= 5)
                                <span class="badge px-3 py-2 rounded-pill small" style="background:#fff3cd; color:#664d03;">Crítico</span>
                            @else
                                <span class="badge px-3 py-2 rounded-pill small" style="background:#d1e7dd; color:#0a3622;">Óptimo</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-dark rounded-3 px-3 shadow-sm border-2 fw-bold"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editStock{{ $epp->id }}">
                                <i class="bi bi-plus-slash-minus me-1"></i>Ajustar
                            </button>
                        </td>
                    </tr>

                    {{-- Modal de ajuste --}}
                    <div class="modal fade" id="editStock{{ $epp->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
                            <div class="modal-content border-0 shadow-lg" style="border-radius:14px; overflow:hidden;">
                                <div class="modal-header border-0 text-white" style="background:#003366;">
                                    <div>
                                        <h6 class="modal-title fw-bold mb-0">
                                            <i class="bi bi-boxes me-2"></i>Ajuste de Stock
                                        </h6>
                                        <small style="opacity:.7; font-size:.75rem;">{{ $epp->nombre }}</small>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('inventario.update', $epp->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body py-4 px-4">

                                        {{-- Stock actual --}}
                                        <div class="text-center mb-4">
                                            <p class="text-muted small mb-1">Stock actual</p>
                                            <span class="fw-bold" style="font-size:2.2rem; color:#003366; line-height:1;">
                                                {{ $epp->stock ?? 0 }}
                                            </span>
                                            <span class="text-muted ms-1">unidades</span>
                                        </div>

                                        {{-- Tipo de ajuste --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Tipo de ajuste</label>
                                            <div class="d-flex gap-2">
                                                <input type="radio" class="btn-check" name="tipo_ajuste_{{ $epp->id }}"
                                                       id="sumar_{{ $epp->id }}" value="sumar" checked>
                                                <label class="btn btn-outline-success flex-fill fw-bold"
                                                       for="sumar_{{ $epp->id }}">
                                                    <i class="bi bi-plus-circle me-1"></i>Sumar
                                                </label>

                                                <input type="radio" class="btn-check" name="tipo_ajuste_{{ $epp->id }}"
                                                       id="fijar_{{ $epp->id }}" value="fijar">
                                                <label class="btn btn-outline-secondary flex-fill fw-bold"
                                                       for="fijar_{{ $epp->id }}">
                                                    <i class="bi bi-pin-angle me-1"></i>Fijar
                                                </label>
                                            </div>
                                            <small class="text-muted d-block mt-1" style="font-size:.72rem;">
                                                <b>Sumar</b>: agrega unidades al stock actual · <b>Fijar</b>: establece un valor exacto (corrección manual)
                                            </small>
                                        </div>

                                        {{-- Cantidad --}}
                                        <div>
                                            <label class="form-label fw-bold small">Cantidad</label>
                                            <input type="number" name="cantidad"
                                                   class="form-control form-control-lg text-center fw-bold border-2"
                                                   value="0" min="0"
                                                   style="font-size:1.6rem; color:#003366; border-radius:10px;">
                                        </div>

                                    </div>
                                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                        <button type="button" class="btn btn-light border flex-fill" data-bs-dismiss="modal">
                                            Cancelar
                                        </button>
                                        <button type="submit" class="btn fw-bold flex-fill text-white"
                                                style="background-color:#003366;">
                                            <i class="bi bi-check-circle me-1"></i>Confirmar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted fst-italic">
                            No se encontraron registros en el inventario.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Sin resultados de búsqueda --}}
        <div id="emptySearch" class="text-center py-5" style="display:none;">
            <i class="bi bi-search" style="font-size:3rem; color:#ccc;"></i>
            <p class="mt-3 fw-semibold text-muted mb-1">Sin resultados</p>
            <p class="text-muted small">No hay EPPs que coincidan con tu búsqueda.</p>
        </div>
    </div>

</div>

<style>
    .table-hover tbody tr:hover { background-color: #f8f9ff !important; transition: 0.15s; }
    .btn-check:checked + .btn-outline-success  { background-color:#198754; color:#fff; }
    .btn-check:checked + .btn-outline-warning  { background-color:#ffc107; color:#000; }
    .btn-check:checked + .btn-outline-secondary{ background-color:#6c757d; color:#fff; }

    /* ── MEJORAS RESPONSIVE AÑADIDAS ── */

    /* Prevenir overflow horizontal global */
    body { overflow-x: hidden; }

    /* ── Móvil pequeño (<576px) ── */
    @media (max-width: 575.98px) {

        /* Padding del contenedor más compacto */
        .container-fluid {
            padding-left: 12px;
            padding-right: 12px;
        }

        /* Header: buscador debajo del título sin margen extraño */
        .row.mb-4 > .col-12.col-md-4 {
            margin-top: 0.5rem;
        }

        /* KPI cards: fila en scroll horizontal si hace falta,
           pero mejor apiladas (Bootstrap ya lo hace con col-md-4) */
        .row.g-3.mb-4 .card-body {
            padding: 0.875rem 1rem;
        }

        .row.g-3.mb-4 h3.fw-bold {
            font-size: 1.6rem;
        }

        .row.g-3.mb-4 .bi.fs-1 {
            font-size: 1.75rem !important;
        }

        /* Tabla: scroll horizontal suave con indicador */
        .table-responsive {
            -webkit-overflow-scrolling: touch;
            border-radius: 0;
        }

        /* Reducir padding de celdas en móvil */
        #inventarioBody td,
        #inventarioBody th {
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        /* Código logístico: badge más compacto */
        #inventarioBody .badge.bg-light {
            font-size: 0.7rem;
            padding: 3px 6px;
        }

        /* Nombre del EPP: no desbordarse */
        #inventarioBody .fw-bold.text-dark {
            font-size: 0.85rem !important;
            word-break: break-word;
        }

        /* Stock: número más pequeño */
        #inventarioBody .fs-5 {
            font-size: 1rem !important;
        }

        /* Botón Ajustar: más compacto */
        #inventarioBody .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* Modal: ancho completo en móvil */
        .modal-dialog {
            margin: 0.5rem auto;
            max-width: calc(100% - 1rem) !important;
        }

        .modal-body.py-4.px-4 {
            padding: 1rem !important;
        }

        .modal-footer.px-4.pb-4 {
            padding: 0.75rem 1rem 1rem !important;
        }

        /* Stock actual en modal: tamaño reducido */
        .modal-body span[style*="2.2rem"] {
            font-size: 1.75rem !important;
        }

        /* Cantidad input en modal */
        .modal-body input[style*="1.6rem"] {
            font-size: 1.2rem !important;
        }
    }

    /* ── Tablet (576px - 767px) ── */
    @media (min-width: 576px) and (max-width: 767.98px) {

        /* KPI cards en fila de 3 columnas pequeñas */
        .row.g-3.mb-4 > div {
            flex: 0 0 auto;
            width: 33.333%;
        }

        .row.g-3.mb-4 .card-body {
            padding: 0.75rem;
        }

        .row.g-3.mb-4 h3.fw-bold {
            font-size: 1.4rem;
        }

        .row.g-3.mb-4 .bi.fs-1 {
            font-size: 1.5rem !important;
        }

        .row.g-3.mb-4 p.small {
            font-size: 0.65rem;
        }

        /* Tabla compacta */
        #inventarioBody td,
        thead th {
            padding-top: 0.55rem;
            padding-bottom: 0.55rem;
            font-size: 0.82rem;
        }

        /* Modal ajustado */
        .modal-dialog {
            max-width: 360px !important;
        }
    }

    /* ── Desktop pequeño (768px - 991px) ── */
    @media (min-width: 768px) and (max-width: 991.98px) {

        /* Tabla un poco más compacta */
        .table {
            font-size: 0.875rem;
        }

        thead th {
            font-size: 0.7rem !important;
            letter-spacing: 0.5px !important;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        /* Columna descripción: no demasiado ancha */
        #inventarioBody td:nth-child(2) {
            max-width: 200px;
        }

        #inventarioBody .fw-bold.text-dark {
            font-size: 0.875rem !important;
        }
    }

    /* ── Desktop grande (992px+) ── */
    @media (min-width: 992px) {
        /* Hover más suave */
        .table-hover tbody tr {
            transition: background-color 0.12s ease;
        }

        /* Buscador alineado a la derecha, con buen tamaño */
        .col-12.col-md-4 .input-group {
            max-width: 340px;
            margin-left: auto;
        }
    }

    /* ── Scroll horizontal con degradado indicador ── */
    @media (max-width: 991.98px) {
        .card.border-0.shadow-sm.overflow-hidden {
            position: relative;
        }

        /* Indicador sutil de scroll horizontal */
        .table-responsive {
            scrollbar-width: thin;
            scrollbar-color: #dee2e6 transparent;
        }

        .table-responsive::-webkit-scrollbar {
            height: 4px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: transparent;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #dee2e6;
            border-radius: 4px;
        }
    }

    /* ── Pantallas XL (1400px+) ── */
    @media (min-width: 1400px) {
        .container-fluid {
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const rows        = document.querySelectorAll('.inventario-row');
    const emptySearch = document.getElementById('emptySearch');

    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        let visible = 0;

        rows.forEach(row => {
            const match = row.dataset.nombre.includes(q) || row.dataset.codigo.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        emptySearch.style.display = visible === 0 ? 'block' : 'none';
    });
});
</script>
@endsection