@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Historial de Entregas de EPP</h2>
            <p class="text-muted">Consulta por Departamento > Carrera > Taller/Lab</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-lg-4 col-md-12">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input id="searchInput" type="text" class="form-control border-0 bg-light" placeholder="Buscar por docente, DNI, EPP...">
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="input-group input-group-sm border rounded">
                        <span class="input-group-text bg-white border-0 text-muted small">Desde:</span>
                        <input type="date" id="dateFrom" class="form-control border-0 ps-0">
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="input-group input-group-sm border rounded">
                        <span class="input-group-text bg-white border-0 text-muted small">Hasta:</span>
                        <input type="date" id="dateTo" class="form-control border-0 ps-0">
                    </div>
                </div>

                @php
                    $deps = collect($asignaciones)->map(fn($a) => optional(optional($a->personal)->departamento)->nombre)->filter()->unique()->sort()->values();
                    $cars = collect($asignaciones)->map(fn($a) => optional($a->personal)->carrera)->filter()->unique()->sort()->values();
                    $talls = collect();
                    foreach ($asignaciones as $a) {
                        if ($a->personal && method_exists($a->personal, 'talleres') && $a->personal->talleres) {
                            $talls = $talls->merge($a->personal->talleres->pluck('nombre'));
                        }
                    }
                    $talls = $talls->filter()->unique()->sort()->values();
                @endphp

                <div class="col-lg-4 col-md-12 d-flex gap-1">
                    <select id="depFilter" class="form-select form-select-sm">
                        <option value="">Departamentos</option>
                        @foreach($deps as $d) <option value="{{ $d }}">{{ $d }}</option> @endforeach
                    </select>
                    <select id="carFilter" class="form-select form-select-sm">
                        <option value="">Carreras</option>
                        @foreach($cars as $c) <option value="{{ $c }}">{{ $c }}</option> @endforeach
                    </select>
                    <select id="tallerFilter" class="form-select form-select-sm">
                        <option value="">Talleres</option>
                        @foreach($talls as $t) <option value="{{ $t }}">{{ $t }}</option> @endforeach
                    </select>
                    <button type="button" id="btnReset" class="btn btn-sm btn-outline-secondary" title="Limpiar Filtros">
                        <i class="bi bi-eraser"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="asignacionesTable" class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 110px;">Fecha</th>
                            <th style="min-width: 160px;">Departamento</th>
                            <th style="min-width: 180px;">Carrera</th>
                            <th style="min-width: 200px;">Taller/Lab</th>
                            <th style="min-width: 220px;">Personal</th>
                            <th style="min-width: 240px;">Equipo (EPP)</th>
                            <th class="text-center">Cant.</th>
                            <th style="min-width: 120px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignaciones as $asignacion)
                            @php
                                $personal = $asignacion->personal;
                                $dep = optional(optional($personal)->departamento)->nombre;
                                $car = optional($personal)->carrera;
                                $talleres = $personal && method_exists($personal, 'talleres') && $personal->talleres ? $personal->talleres->pluck('nombre')->join(', ') : '';
                                $buscarText = strtolower(trim(
                                    ($personal->nombre_completo ?? '') . ' ' .
                                    ($personal->dni ?? '') . ' ' .
                                    ($asignacion->epp->nombre ?? '') . ' ' .
                                    ($dep ?? '') . ' ' .
                                    ($car ?? '') . ' ' .
                                    $talleres
                                ));
                            @endphp
                            <tr data-search="{{ $buscarText }}"
                                data-dep="{{ strtolower($dep ?? '') }}"
                                data-car="{{ strtolower($car ?? '') }}"
                                data-taller="{{ strtolower($talleres ?? '') }}"
                                data-fecha="{{ \Carbon\Carbon::parse($asignacion->fecha_entrega)->format('Y-m-d') }}">

                                <td>{{ \Carbon\Carbon::parse($asignacion->fecha_entrega)->format('d/m/Y') }}</td>
                                <td><span class="badge bg-info text-dark opacity-75">{{ $dep ?? '—' }}</span></td>
                                <td><span class="badge bg-light text-dark border">{{ $car ?? '—' }}</span></td>
                                <td><small class="text-muted">{{ $talleres ?: '—' }}</small></td>
                                <td>
                                    <div class="fw-bold">{{ $personal->nombre_completo ?? 'No asignado' }}</div>
                                    <small class="text-muted">{{ $personal->dni ?? '' }}</small>
                                </td>
                                <td>{{ $asignacion->epp->nombre }}</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $asignacion->cantidad }}</span></td>
                                <td>
                                    @php
                                        $estado = (string)$asignacion->estado;
                                        $cls = in_array($estado, ['Posee','Entregado']) ? 'bg-success' : (in_array($estado, ['Dañado','Perdido']) ? 'bg-danger' : 'bg-secondary');
                                    @endphp
                                    <span class="badge rounded-pill {{ $cls }}">{{ $estado }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">No hay registros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const input = document.getElementById('searchInput');
        const depSel = document.getElementById('depFilter');
        const carSel = document.getElementById('carFilter');
        const talSel = document.getElementById('tallerFilter');
        const dFrom = document.getElementById('dateFrom');
        const dTo = document.getElementById('dateTo');
        const tbody = document.querySelector('#asignacionesTable tbody');

        function applyFilters() {
            const q = input.value.trim().toLowerCase();
            const dep = depSel.value.trim().toLowerCase();
            const car = carSel.value.trim().toLowerCase();
            const tal = talSel.value.trim().toLowerCase();
            const from = dFrom.value;
            const to = dTo.value;

            tbody.querySelectorAll('tr').forEach(row => {
                if (row.cells.length === 1) return;

                const matchText = q === '' || row.getAttribute('data-search').includes(q);
                const matchDep = dep === '' || row.getAttribute('data-dep') === dep;
                const matchCar = car === '' || row.getAttribute('data-car') === car;
                const matchTal = tal === '' || row.getAttribute('data-taller').split(',').map(s => s.trim()).includes(tal);

                const rfecha = row.getAttribute('data-fecha');
                let matchFecha = true;
                if (from && rfecha < from) matchFecha = false;
                if (to && rfecha > to) matchFecha = false;

                row.style.display = (matchText && matchDep && matchCar && matchTal && matchFecha) ? '' : 'none';
            });
        }

        document.getElementById('btnReset').addEventListener('click', () => {
            input.value = ''; depSel.value = ''; carSel.value = ''; talSel.value = '';
            dFrom.value = ''; dTo.value = '';
            applyFilters();
        });

        [input, depSel, carSel, talSel, dFrom, dTo].forEach(el => {
            el.addEventListener('input', applyFilters);
            el.addEventListener('change', applyFilters);
        });
    })();
</script>

<style>
    .table-hover tbody tr:hover { background-color: #f8fafc; }
    .badge { font-weight: 600; }
    .bg-info { background-color: #e0f2fe !important; }
</style>
@endsection