@extends('layouts.app')

@section('content')
<style>
    :root {
        --dark-primary: #0f172a;
        --accent-blue: #2563eb;
    }

    /* ── TIPOGRAFÍA ── */
    .display-year {
        font-size: clamp(1.8rem, 6vw, 2.5rem);
        font-weight: 800;
        color: var(--dark-primary);
        letter-spacing: -2px;
        line-height: 1;
    }
    .month-badge {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        letter-spacing: 0.5px;
        display: block;
        text-align: center;
        white-space: nowrap;
    }
    .fw-black { font-weight: 900; }

    /* ── YEAR MARKER ── */
    .year-marker {
        position: relative;
        padding-left: 1.25rem;
    }
    @media (min-width: 768px) {
        .year-marker { padding-left: 2.5rem; }
    }
    .year-marker::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: linear-gradient(to bottom, var(--accent-blue), transparent);
        border-radius: 4px;
    }
    .year-marker .d-flex.align-items-baseline {
        flex-wrap: wrap;
        row-gap: 4px;
    }

    /* ── TIMELINE CARD ── */
    .timeline-card {
        border: none;
        border-radius: 16px;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        background: rgba(255,255,255,0.95);
    }
    @media (hover: hover) {
        .timeline-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }
    }
    .table tbody tr:last-child { border-bottom: none !important; }

    /* ── TABLA RESPONSIVE ── */
    .table td, .table th {
        vertical-align: middle;
        word-break: break-word;
    }
    @media (min-width: 768px) and (max-width: 1100px) {
        .table td, .table th {
            padding-top: 0.6rem !important;
            padding-bottom: 0.6rem !important;
            font-size: 0.82rem;
        }
        .table td.ps-4, .table th.ps-4 {
            padding-left: 1rem !important;
        }
    }
    .timeline-card .table-responsive {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    .timeline-card .table-responsive::-webkit-scrollbar { height: 4px; }
    .timeline-card .table-responsive::-webkit-scrollbar-track { background: transparent; }
    .timeline-card .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    td .rounded-3 { white-space: nowrap; }

    /* ── MOBILE CARD ── */
    .card-asignacion-vida {
        border: none;
        border-radius: 14px;
    }
    .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f1f3f5;
        border-radius: 8px;
        padding: 2px 8px;
        font-size: 0.73rem;
        color: #495057;
    }
    @media (max-width: 400px) {
        .card-asignacion-vida .card-body { padding: 0.85rem !important; }
        .info-chip { font-size: 0.68rem; padding: 2px 6px; }
        .status-chip { font-size: 0.68rem !important; padding: 4px 8px; }
    }

    .status-chip {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 0.78rem;
    }

    /* ── PAGE HEADER NUEVO ── */
    /*
     * Estructura:
     *   .page-header-wrap
     *     ├── fila 1: título + botones (en la misma línea siempre)
     *     └── fila 2: buscador (ancho completo)
     */
    .page-header-wrap {
        margin-bottom: 40px;
    }

    .page-header-top {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .page-header-titles {
        flex: 1 1 220px;
        min-width: 0;
    }

    .page-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        flex-shrink: 0;
    }

    /* En xs los botones se achican un poco para caber */
    @media (max-width: 480px) {
        .page-header-actions .btn {
            font-size: 0.78rem;
            padding: 8px 14px;
        }
        .page-header-actions .btn .btn-label {
            display: none; /* Oculta texto largo, deja solo ícono + palabra clave */
        }
    }

    /* Botón Volver: más discreto */
    .btn-volver {
        min-height: 40px;
        white-space: nowrap;
    }
    .btn-imprimir {
        min-height: 40px;
        white-space: nowrap;
    }
    .btn-pdf {
        min-height: 40px;
        white-space: nowrap;
    }

    /* Buscador: siempre full width dentro del header */
    .page-search-row {
        width: 100%;
        max-width: 480px;
    }
    @media (max-width: 400px) {
        .page-search-row .btn { padding-left: 0.7rem; padding-right: 0.7rem; font-size: 0.85rem; }
        .page-search-row .form-control { font-size: 0.85rem; }
    }

    /* Empty state */
    .bi.display-1 { font-size: clamp(3rem, 15vw, 5rem) !important; }

    /* No overflow */
    .container { overflow-x: hidden; }

    /* Print */
    @media print {
        .no-print { display: none !important; }
        .year-marker { break-inside: avoid; }
        .timeline-card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
    }
</style>

<div class="container py-4 py-md-5">

    {{-- ── PAGE HEADER REESTRUCTURADO ── --}}
    <div class="page-header-wrap">

        {{-- Fila 1: Títulos + Botones en la misma línea --}}
        <div class="page-header-top">

            {{-- Títulos --}}
            <div class="page-header-titles">
                <h6 class="text-primary fw-bold text-uppercase mb-1" style="letter-spacing:2px; font-size:.75rem;">
                    Gestión de Activos
                </h6>
                <h1 class="fw-black text-dark mb-1" style="font-size: clamp(1.4rem, 5vw, 2.2rem); letter-spacing:-1px;">
                    Master Plan de Vida Útil
                </h1>
                <p class="text-muted mb-0" style="font-size:.88rem;">
                    Proyección de renovaciones basada en las asignaciones actuales al personal docente y administrativo.
                </p>
            </div>

            {{-- Botones — misma fila que el título, se alinean arriba --}}
            <div class="page-header-actions no-print">
                <a href="{{ route('reportes.index') }}" class="btn btn-light border fw-bold rounded-pill btn-volver">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
                <button onclick="imprimirPagina()" class="btn btn-outline-dark fw-bold rounded-pill btn-imprimir">
                    <i class="bi bi-printer me-1"></i><span class="d-none d-sm-inline">Imprimir</span>
                </button>
                <button onclick="descargarPDF()" class="btn btn-dark fw-bold rounded-pill btn-pdf">
                    <i class="bi bi-file-earmark-pdf me-1"></i><span class="d-none d-sm-inline">Descargar </span>PDF
                </button>
            </div>

        </div>

        {{-- Fila 2: Buscador --}}
        <div class="page-search-row no-print">
            <form action="{{ route('reportes.vida_util') }}" method="GET">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search"
                           class="form-control border-start-0 ps-0"
                           placeholder="Buscar docente o DNI..."
                           value="{{ $search ?? '' }}">
                    @if(request('search'))
                        <a href="{{ route('reportes.vida_util') }}"
                           class="btn btn-light border" title="Limpiar">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>
        </div>

    </div>

    {{-- ── EMPTY STATE ── --}}
    @if($proyeccionPorAnio->isEmpty())
        <div class="card p-5 text-center border-0 shadow-sm rounded-4">
            <div class="py-4">
                <i class="bi bi-calendar-x display-1 text-light"></i>
                <h3 class="mt-4 fw-bold">Sin datos para proyectar</h3>
                @if(request('search'))
                    <p class="text-muted">No se encontraron asignaciones para "<strong>{{ request('search') }}</strong>".</p>
                    <a href="{{ route('reportes.vida_util') }}" class="btn btn-outline-primary rounded-pill mt-2">
                        <i class="bi bi-x me-1"></i>Limpiar búsqueda
                    </a>
                @else
                    <p class="text-muted">No hay asignaciones activas para calcular renovaciones.</p>
                @endif
            </div>
        </div>

    @else
        @foreach($proyeccionPorAnio as $anio => $asignaciones)
        <div class="year-marker mb-5">

            {{-- Year heading --}}
            <div class="d-flex align-items-baseline mb-3 mb-md-4 gap-2">
                <span class="display-year">{{ $anio }}</span>
                <span class="text-muted fw-medium small">Renovaciones Programadas</span>
            </div>

            {{-- ── TABLE (md and up) ── --}}
            <div class="card timeline-card shadow-sm overflow-hidden d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small text-uppercase fw-bold">
                                <th class="ps-4 py-3">Fecha Entrega</th>
                                <th>Personal / Área</th>
                                <th>EPP a Renovar</th>
                                <th style="min-width:140px;">Mes Vencimiento</th>
                                <th>Vida Útil</th>
                                <th class="text-center">Estado Actual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asignaciones as $item)
                            @php
                                $fecha  = \Carbon\Carbon::parse($item->fecha_vencimiento);
                                $hoy    = now();
                                $dias   = (int) $hoy->diffInDays($fecha, false);
                                $meses  = (int) $hoy->diffInMonths($fecha, false);
                            @endphp
                            <tr class="border-bottom border-light">
                                <td class="ps-4 py-4" style="width:130px;">
                                    <div class="fw-semibold text-dark">
                                        {{ \Carbon\Carbon::parse($item->fecha_entrega)->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->personal->nombre_completo ?? 'N/A' }}</div>
                                    <span class="badge bg-light text-secondary border mt-1">
                                        {{ $item->personal->departamento->nombre ?? 'Sin Área' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $item->epp->nombre ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $item->epp->codigo_logistica ?? '' }}</small>
                                </td>
                                <td style="width:150px;">
                                    <div class="month-badge mb-1">{{ $fecha->translatedFormat('F') }}</div>
                                    <div class="text-center small text-muted fw-semibold">{{ $fecha->format('d/m/Y') }}</div>
                                </td>
                                <td>{{ $item->epp->vida_util_meses ?? 12 }} meses</td>
                                <td class="text-center">
                                    @if($fecha->isPast())
                                        <div class="px-3 py-2 bg-danger bg-opacity-10 text-danger rounded-3 d-inline-block">
                                            <span class="fw-black small"><i class="bi bi-shield-exclamation me-1"></i>EXPIRÓ</span>
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

            {{-- ── CARDS (mobile, < md) ── --}}
            <div class="d-md-none d-flex flex-column gap-3">
                @foreach($asignaciones as $item)
                @php
                    $fecha  = \Carbon\Carbon::parse($item->fecha_vencimiento);
                    $hoy    = now();
                    $dias   = (int) $hoy->diffInDays($fecha, false);
                    $meses  = (int) $hoy->diffInMonths($fecha, false);
                @endphp
                <div class="card card-asignacion-vida shadow-sm">
                    <div class="card-body p-3">

                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div class="fw-bold text-primary flex-grow-1">{{ $item->epp->nombre ?? 'N/A' }}</div>
                            @if($fecha->isPast())
                                <span class="status-chip bg-danger bg-opacity-10 text-danger fw-black flex-shrink-0" style="font-size:.72rem;">
                                    <i class="bi bi-shield-exclamation me-1"></i>EXPIRÓ
                                </span>
                            @elseif($dias < 30)
                                <span class="status-chip bg-warning bg-opacity-10 text-dark fw-black flex-shrink-0" style="font-size:.72rem;">
                                    {{ $dias }}d · RENOVAR
                                </span>
                            @else
                                <span class="status-chip bg-success bg-opacity-10 text-success fw-black flex-shrink-0" style="font-size:.72rem;">
                                    {{ $meses }}m · VIGENTE
                                </span>
                            @endif
                        </div>

                        <div class="small fw-semibold mb-1">
                            <i class="bi bi-person me-1 text-muted"></i>
                            {{ $item->personal->nombre_completo ?? 'N/A' }}
                        </div>

                        <div class="d-flex flex-wrap gap-1 mt-2">
                            <span class="info-chip">
                                <i class="bi bi-building"></i>
                                {{ $item->personal->departamento->nombre ?? 'Sin Área' }}
                            </span>
                            <span class="info-chip">
                                <i class="bi bi-box-arrow-in-down"></i>
                                Entrega: {{ \Carbon\Carbon::parse($item->fecha_entrega)->format('d/m/Y') }}
                            </span>
                            <span class="info-chip">
                                <i class="bi bi-calendar-event"></i>
                                Vence: {{ $fecha->format('d/m/Y') }}
                            </span>
                            <span class="info-chip">
                                <i class="bi bi-clock-history"></i>
                                {{ $item->epp->vida_util_meses ?? 12 }} meses
                            </span>
                            @if($item->epp->codigo_logistica ?? false)
                            <span class="info-chip">
                                <i class="bi bi-upc-scan"></i>
                                {{ $item->epp->codigo_logistica }}
                            </span>
                            @endif
                        </div>

                        <div class="mt-2">
                            <span class="month-badge">{{ $fecha->translatedFormat('F') }}</span>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>

        </div>
        @endforeach
    @endif

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
function imprimirPagina() {
    window.print();
}

function descargarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

    doc.setFontSize(18);
    doc.setFont('helvetica', 'bold');
    doc.text('Master Plan de Vida Útil', 14, 18);
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(100);
    doc.text('Proyección de renovaciones - EPP Sistema', 14, 25);
    doc.text('Generado: ' + new Date().toLocaleDateString('es-PE'), 14, 31);
    doc.setTextColor(0);

    let startY = 38;

    document.querySelectorAll('.year-marker').forEach(section => {
        const anio = section.querySelector('.display-year')?.textContent?.trim() ?? '';
        const table = section.querySelector('table');
        if (!table) return;

        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(37, 99, 235);
        doc.text(anio + ' — Renovaciones Programadas', 14, startY);
        doc.setTextColor(0);
        startY += 4;

        const rows = [];
        table.querySelectorAll('tbody tr').forEach(tr => {
            const cells = tr.querySelectorAll('td');
            if (cells.length < 6) return;
            rows.push([
                cells[0].textContent.trim(),
                cells[1].textContent.trim(),
                cells[2].textContent.trim(),
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim(),
            ]);
        });

        doc.autoTable({
            startY: startY,
            head: [['Fecha Entrega', 'Personal / Área', 'EPP a Renovar', 'Mes Vencimiento', 'Vida Útil', 'Estado Actual']],
            body: rows,
            theme: 'striped',
            headStyles: { fillColor: [15, 23, 42], textColor: 255, fontStyle: 'bold', fontSize: 8 },
            bodyStyles: { fontSize: 8 },
            columnStyles: {
                0: { cellWidth: 28 },
                1: { cellWidth: 60 },
                2: { cellWidth: 60 },
                3: { cellWidth: 38 },
                4: { cellWidth: 22 },
                5: { cellWidth: 30 },
            },
            margin: { left: 14, right: 14 },
            didDrawPage: (data) => { startY = data.cursor.y + 8; }
        });

        startY = doc.lastAutoTable.finalY + 12;
    });

    const searchVal = '{{ $search ?? '' }}';
    const filename = searchVal
        ? 'vida_util_' + searchVal.replace(/\s+/g, '_') + '.pdf'
        : 'master_plan_vida_util.pdf';

    doc.save(filename);
}
</script>
@endsection