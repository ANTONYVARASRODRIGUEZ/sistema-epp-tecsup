@extends('layouts.app')

@section('content')
<style>
    .page-title { font-size: clamp(1.25rem, 4vw, 1.75rem); }

    .filter-bar .input-group,
    .filter-bar .form-select { min-width: 0; }

    .table-hover tbody tr:hover { background-color: #f8fafc; }
    .badge { font-weight: 600; }

    .badge.bg-light       { color: #333 !important; }
    .badge-depto          { background-color: #e0f2fe !important; color: #0369a1 !important; font-weight: 600; }

    .card-asignacion {
        border: none;
        border-radius: 14px;
        transition: box-shadow 0.2s;
    }
    .card-asignacion:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.08) !important; }

    .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f1f3f5;
        border-radius: 8px;
        padding: 2px 8px;
        font-size: 0.75rem;
        color: #495057;
    }

    .btn-filter-toggle { font-size: 0.85rem; }

    /* Filas clickeables */
    #asignacionesTable tbody tr.row-clickable {
        cursor: pointer;
        transition: background 0.15s;
    }
    #asignacionesTable tbody tr.row-clickable:hover {
        background-color: #eff6ff !important;
    }
    .card-mobile-item.row-clickable {
        cursor: pointer;
    }
    .card-mobile-item.row-clickable:hover {
        box-shadow: 0 6px 20px rgba(37,99,235,0.12) !important;
    }

    /* Tooltip hint */
    .click-hint {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* ── MEJORAS RESPONSIVE AÑADIDAS ── */

    /* Contenedor general: mejor padding en pantallas pequeñas */
    @media (max-width: 575.98px) {
        .container {
            padding-left: 12px;
            padding-right: 12px;
        }

        /* Header más compacto en móvil */
        .mb-4 {
            margin-bottom: 1rem !important;
        }

        /* Filter card sin padding extra */
        .card-body.p-3 {
            padding: 0.75rem !important;
        }

        /* Búsqueda mobile: que el input no se desborde */
        #searchInput {
            min-width: 0;
            font-size: 0.875rem;
        }

        /* Botón de filtros alineado */
        .btn-filter-toggle {
            white-space: nowrap;
            padding: 0.375rem 0.6rem;
        }

        /* Cards mobile: padding más ajustado */
        .card-asignacion .card-body {
            padding: 0.75rem !important;
        }

        /* Nombre del docente en mobile no se corta */
        .card-asignacion .fw-bold {
            font-size: 0.9rem;
            line-height: 1.3;
            word-break: break-word;
        }

        /* EPP chip en mobile */
        .card-asignacion .bg-light.rounded-3 {
            font-size: 0.8rem;
        }

        /* Info chips en mobile */
        .info-chip {
            font-size: 0.7rem;
            padding: 2px 6px;
        }

        /* Badges en mobile */
        .badge.rounded-pill {
            font-size: 0.7rem;
        }

        /* Fecha + badge: que no se rompan */
        .card-asignacion .d-flex.justify-content-between {
            flex-wrap: nowrap;
            gap: 8px;
        }
    }

    /* Tablet (576px - 767px): vista intermedia tipo card compacto */
    @media (min-width: 576px) and (max-width: 767.98px) {
        .card-asignacion .card-body {
            padding: 0.875rem !important;
        }

        #listaMobileCards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        /* Anular el mb-3 individual cuando hay grid */
        #listaMobileCards .card-asignacion {
            margin-bottom: 0 !important;
        }

        /* El "no hay registros" ocupa todo el ancho */
        #listaMobileCards > div:only-child {
            grid-column: 1 / -1;
        }
    }

    /* Desktop pequeño (768px - 991px): tabla más compacta */
    @media (min-width: 768px) and (max-width: 991.98px) {
        #asignacionesTable {
            font-size: 0.82rem;
        }

        #asignacionesTable th,
        #asignacionesTable td {
            padding: 0.5rem 0.6rem;
        }

        /* Columna Personal: evitar que sea muy ancha */
        #asignacionesTable th:nth-child(3),
        #asignacionesTable td:nth-child(3) {
            min-width: 150px !important;
        }

        /* Columna EPP */
        #asignacionesTable th:nth-child(4),
        #asignacionesTable td:nth-child(4) {
            min-width: 160px !important;
        }

        /* Click hint más pequeño */
        .click-hint {
            font-size: 0.65rem;
        }

        /* Filtros desktop: selects más pequeños */
        .filter-bar .form-select-sm,
        .filter-bar .form-control {
            font-size: 0.8rem;
        }
    }

    /* Desktop grande (992px+) */
    @media (min-width: 992px) {
        #asignacionesTable {
            font-size: 0.875rem;
        }

        /* Hover más suave en desktop */
        #asignacionesTable tbody tr.row-clickable {
            transition: background 0.12s, box-shadow 0.12s;
        }
    }

    /* Pantallas muy grandes (1400px+): limitar el ancho del contenedor */
    @media (min-width: 1400px) {
        .container {
            max-width: 1320px;
        }
    }

    /* Tabla: scroll horizontal suave en tablet */
    @media (min-width: 768px) and (max-width: 1100px) {
        .table-responsive {
            -webkit-overflow-scrolling: touch;
        }

        /* Indicador visual de scroll horizontal */
        .table-responsive::after {
            content: '';
            display: block;
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 24px;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.8));
            pointer-events: none;
            border-radius: 0 12px 12px 0;
        }

        .card-body {
            position: relative;
        }
    }

    /* Filtros: colapso mobile más limpio */
    @media (max-width: 767.98px) {
        #filtrosExtras .row.g-2 > .col-6 {
            padding-left: 4px;
            padding-right: 4px;
        }

        #filtrosExtras .form-select-sm,
        #filtrosExtras .form-control {
            font-size: 0.85rem;
        }
    }

    /* Accesibilidad: foco visible en filas clickeables */
    .row-clickable:focus {
        outline: 2px solid #2563eb;
        outline-offset: -2px;
    }

    /* Prevenir overflow horizontal global */
    body {
        overflow-x: hidden;
    }

    /* Badge de departamento: no cortar texto largo */
    .badge-depto {
        white-space: normal;
        text-align: left;
        line-height: 1.3;
        max-width: 140px;
        display: inline-block;
    }
</style>

<div class="container py-3 py-md-4">

    {{-- ── HEADER ── --}}
    <div class="mb-4">
        <h2 class="page-title fw-bold text-dark mb-0">Historial de Entregas de EPP</h2>
        <p class="text-muted small mb-0">Consulta por Departamento · <span class="text-primary"><i class="bi bi-hand-index-thumb me-1"></i>Haz clic en una fila para ver la vida útil del docente</span></p>
    </div>

    {{-- ── FILTER BAR ── --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">

            {{-- Mobile --}}
            <div class="d-md-none mb-2 d-flex gap-2">
                <div class="input-group flex-grow-1">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                    <input id="searchInput" type="text"
                           class="form-control border-0 bg-light"
                           placeholder="Buscar docente o EPP...">
                </div>
                <button class="btn btn-light border btn-filter-toggle"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#filtrosExtras"
                        aria-expanded="false">
                    <i class="bi bi-sliders me-1"></i>Filtros
                </button>
            </div>

            {{-- Desktop --}}
            <div class="d-none d-md-block">
                <div class="row g-2 align-items-center filter-bar">
                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input id="searchInputDesktop" type="text"
                                   class="form-control border-0 bg-light"
                                   placeholder="Buscar por docente o EPP...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="input-group input-group-sm border rounded">
                            <span class="input-group-text bg-white border-0 text-muted small">Desde:</span>
                            <input type="date" id="dateFromDesktop" class="form-control border-0 ps-0">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="input-group input-group-sm border rounded">
                            <span class="input-group-text bg-white border-0 text-muted small">Hasta:</span>
                            <input type="date" id="dateToDesktop" class="form-control border-0 ps-0">
                        </div>
                    </div>
                    <div class="col-lg-4 d-flex gap-1">
                        @php
                            $deps = collect($asignaciones)->map(fn($a) => optional(optional($a->personal)->departamento)->nombre)->filter()->unique()->sort()->values();
                        @endphp
                        <select id="depFilterDesktop" class="form-select form-select-sm">
                            <option value="">Departamentos</option>
                            @foreach($deps as $d) <option value="{{ $d }}">{{ $d }}</option> @endforeach
                        </select>
                        <select id="estadoFilterDesktop" class="form-select form-select-sm">
                            <option value="">Estado</option>
                            <option value="entregado">Entregado</option>
                            <option value="devuelto">Devuelto</option>
                            <option value="dañado">Dañado</option>
                            <option value="perdido">Perdido</option>
                        </select>
                        <button type="button" id="btnResetDesktop"
                                class="btn btn-sm btn-outline-secondary flex-shrink-0" title="Limpiar Filtros">
                            <i class="bi bi-eraser"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Mobile: collapsible --}}
            <div class="collapse d-md-none" id="filtrosExtras">
                <div class="pt-2 d-flex flex-column gap-2">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="input-group input-group-sm border rounded">
                                <span class="input-group-text bg-white border-0 text-muted" style="font-size:.75rem;">Desde:</span>
                                <input type="date" id="dateFrom" class="form-control border-0 ps-0">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group input-group-sm border rounded">
                                <span class="input-group-text bg-white border-0 text-muted" style="font-size:.75rem;">Hasta:</span>
                                <input type="date" id="dateTo" class="form-control border-0 ps-0">
                            </div>
                        </div>
                    </div>
                    <select id="depFilter" class="form-select form-select-sm">
                        <option value="">Todos los departamentos</option>
                        @foreach($deps as $d) <option value="{{ $d }}">{{ $d }}</option> @endforeach
                    </select>
                    <select id="estadoFilter" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="entregado">Entregado</option>
                        <option value="devuelto">Devuelto</option>
                        <option value="dañado">Dañado</option>
                        <option value="perdido">Perdido</option>
                    </select>
                    <button type="button" id="btnReset"
                            class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-eraser me-1"></i>Limpiar filtros
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- ── TABLE (desktop) ── --}}
    <div class="card border-0 shadow-sm d-none d-md-block" style="border-radius: 12px;">
        <div class="card-body p-3 p-lg-4">
            <div class="table-responsive">
                <table id="asignacionesTable" class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:110px;">Fecha</th>
                            <th style="min-width:150px;">Departamento</th>
                            <th style="min-width:200px;">Personal</th>
                            <th style="min-width:220px;">Equipo (EPP)</th>
                            <th class="text-center">Cant.</th>
                            <th style="min-width:110px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignaciones as $asignacion)
                        @php
                            $personal  = $asignacion->personal;
                            $dep       = optional(optional($personal)->departamento)->nombre;
                            $buscarText = strtolower(trim(
                                ($personal->nombre_completo ?? '') . ' ' .
                                ($personal->dni ?? '') . ' ' .
                                ($asignacion->epp->nombre ?? '') . ' ' .
                                ($dep ?? '')
                            ));
                            $estado = (string)$asignacion->estado;
                            $cls = in_array($estado, ['Posee','Entregado']) ? 'bg-success'
                                 : (in_array($estado, ['Dañado','Perdido']) ? 'bg-danger' : 'bg-secondary');
                            $urlVidaUtil = route('reportes.vida_util', ['search' => $personal->nombre_completo ?? '']);
                        @endphp
                        <tr class="border-bottom border-light row-clickable"
                            data-search="{{ $buscarText }}"
                            data-dep="{{ strtolower($dep ?? '') }}"
                            data-estado="{{ strtolower($estado) }}"
                            data-fecha="{{ \Carbon\Carbon::parse($asignacion->fecha_entrega)->format('Y-m-d') }}"
                            data-url="{{ $urlVidaUtil }}"
                            title="Ver vida útil de {{ $personal->nombre_completo ?? '' }}">
                            <td>{{ \Carbon\Carbon::parse($asignacion->fecha_entrega)->format('d/m/Y') }}</td>
                            <td><span class="badge badge-depto">{{ $dep ?? '—' }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $personal->nombre_completo ?? 'No asignado' }}</div>
                                <small class="text-muted">{{ $personal->dni ?? '' }}</small>
                                <div class="click-hint"><i class="bi bi-box-arrow-up-right me-1"></i>Ver vida útil</div>
                            </td>
                            <td>{{ $asignacion->epp->nombre }}</td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $asignacion->cantidad }}</span></td>
                            <td><span class="badge rounded-pill {{ $cls }}">{{ $estado }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">No hay registros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── CARDS (mobile) ── --}}
    <div class="d-md-none" id="listaMobileCards">
        @forelse($asignaciones as $asignacion)
        @php
            $personal  = $asignacion->personal;
            $dep       = optional(optional($personal)->departamento)->nombre;
            $buscarText = strtolower(trim(
                ($personal->nombre_completo ?? '') . ' ' .
                ($personal->dni ?? '') . ' ' .
                ($asignacion->epp->nombre ?? '') . ' ' .
                ($dep ?? '')
            ));
            $estado = (string)$asignacion->estado;
            $cls = in_array($estado, ['Posee','Entregado']) ? 'bg-success'
                 : (in_array($estado, ['Dañado','Perdido']) ? 'bg-danger' : 'bg-secondary');
            $fechaYmd = \Carbon\Carbon::parse($asignacion->fecha_entrega)->format('Y-m-d');
            $urlVidaUtil = route('reportes.vida_util', ['search' => $personal->nombre_completo ?? '']);
        @endphp
        <div class="card card-asignacion shadow-sm mb-3 card-mobile-item row-clickable"
             data-search="{{ $buscarText }}"
             data-dep="{{ strtolower($dep ?? '') }}"
             data-estado="{{ strtolower($estado) }}"
             data-fecha="{{ $fechaYmd }}"
             data-url="{{ $urlVidaUtil }}">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ \Carbon\Carbon::parse($asignacion->fecha_entrega)->format('d/m/Y') }}
                    </span>
                    <span class="badge rounded-pill {{ $cls }}">{{ $estado }}</span>
                </div>
                <div class="fw-bold mb-1">{{ $personal->nombre_completo ?? 'No asignado' }}</div>
                @if($personal->dni ?? false)
                    <div class="text-muted small mb-2">DNI: {{ $personal->dni }}</div>
                @endif
                <div class="d-flex align-items-center gap-2 mb-3 p-2 bg-light rounded-3">
                    <i class="bi bi-shield-check text-primary"></i>
                    <div class="flex-grow-1 small fw-semibold">{{ $asignacion->epp->nombre }}</div>
                    <span class="badge bg-secondary">x{{ $asignacion->cantidad }}</span>
                </div>
                <div class="d-flex flex-wrap gap-1 align-items-center">
                    @if($dep) <span class="info-chip"><i class="bi bi-building"></i>{{ $dep }}</span> @endif
                    <span class="info-chip text-primary ms-auto"><i class="bi bi-box-arrow-up-right me-1"></i>Ver vida útil</span>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">No hay registros.</div>
        @endforelse
    </div>

</div>

<script>
(function () {

    // ── Navegación al hacer clic en fila/card ──
    document.querySelectorAll('.row-clickable').forEach(el => {
        el.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            if (url) window.location.href = url;
        });
    });

    function getVal(id) {
        const el = document.getElementById(id);
        return el ? el.value.trim().toLowerCase() : '';
    }

    function filterRows(rows, q, dep, estado, from, to) {
        rows.forEach(row => {
            if (row.cells && row.cells.length === 1) return;
            const matchText   = !q      || (row.getAttribute('data-search') ?? '').includes(q);
            const matchDep    = !dep    || (row.getAttribute('data-dep')    ?? '') === dep;
            const matchEstado = !estado || (row.getAttribute('data-estado') ?? '') === estado;
            const rfecha      = row.getAttribute('data-fecha') ?? '';
            const matchFecha  = (!from || rfecha >= from) && (!to || rfecha <= to);
            row.style.display = (matchText && matchDep && matchEstado && matchFecha) ? '' : 'none';
        });
    }

    // Desktop
    const desktopInputs = ['searchInputDesktop','depFilterDesktop','estadoFilterDesktop','dateFromDesktop','dateToDesktop'];
    const tableRows = Array.from(document.querySelectorAll('#asignacionesTable tbody tr'));

    function applyDesktop() {
        filterRows(tableRows,
            getVal('searchInputDesktop'),
            getVal('depFilterDesktop'),
            getVal('estadoFilterDesktop'),
            document.getElementById('dateFromDesktop')?.value ?? '',
            document.getElementById('dateToDesktop')?.value ?? '');
    }

    desktopInputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.addEventListener('input', applyDesktop); el.addEventListener('change', applyDesktop); }
    });

    document.getElementById('btnResetDesktop')?.addEventListener('click', () => {
        desktopInputs.forEach(id => { if(document.getElementById(id)) document.getElementById(id).value = ''; });
        applyDesktop();
    });

    // Mobile
    const mobileInputs = ['searchInput','depFilter','estadoFilter','dateFrom','dateTo'];
    const mobileCards  = Array.from(document.querySelectorAll('.card-mobile-item'));

    function applyMobile() {
        filterRows(mobileCards,
            getVal('searchInput'),
            getVal('depFilter'),
            getVal('estadoFilter'),
            document.getElementById('dateFrom')?.value ?? '',
            document.getElementById('dateTo')?.value ?? '');
    }

    mobileInputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.addEventListener('input', applyMobile); el.addEventListener('change', applyMobile); }
    });

    document.getElementById('btnReset')?.addEventListener('click', () => {
        mobileInputs.forEach(id => { if(document.getElementById(id)) document.getElementById(id).value = ''; });
        applyMobile();
    });

})();
</script>
@endsection