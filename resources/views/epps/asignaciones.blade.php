@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Historial de Asignaciones</h2>
            <p class="text-muted">Control y auditoría por Departamento > Carrera > Taller/Lab</p>
        </div>
    </div>

    <!-- Barra de búsqueda y filtros (cliente) -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-lg-4 col-md-6 mb-2 mb-lg-0">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input id="searchInput" type="text" class="form-control" placeholder="Buscar por docente, DNI, EPP, departamento, carrera o taller/lab...">
                    </div>
                </div>
                @php
                    $deps = collect($asignaciones)->map(fn($a) => optional(optional($a->personal)->departamento)->nombre)->filter()->unique()->sort()->values();
                    $cars = collect($asignaciones)->map(fn($a) => optional($a->personal)->carrera)->filter()->unique()->sort()->values();
                    $talls = collect();
                    foreach ($asignaciones as $a) {
                        if (method_exists($a->personal, 'talleres') && $a->personal->talleres) {
                            $talls = $talls->merge($a->personal->talleres->pluck('nombre'));
                        }
                    }
                    $talls = $talls->filter()->unique()->sort()->values();
                @endphp
                <div class="col-lg-2 col-md-6">
                    <select id="depFilter" class="form-select">
                        <option value="">Todos los departamentos</option>
                        @foreach($deps as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <select id="carFilter" class="form-select">
                        <option value="">Todas las carreras</option>
                        @foreach($cars as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <select id="tallerFilter" class="form-select">
                        <option value="">Todos los talleres/labs</option>
                        @foreach($talls as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
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
                            <th style="min-width: 220px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignaciones as $asignacion)
                            @php
                                $personal = $asignacion->personal;
                                $dep = optional(optional($personal)->departamento)->nombre;
                                $car = optional($personal)->carrera;
                                $talleres = method_exists($personal, 'talleres') && $personal->talleres ? $personal->talleres->pluck('nombre')->join(', ') : '';
                                $buscarText = strtolower(trim(
                                    (string)($personal->nombre_completo ?? '') . ' ' .
                                    (string)($personal->dni ?? '') . ' ' .
                                    (string)($asignacion->epp->nombre ?? '') . ' ' .
                                    (string)($dep ?? '') . ' ' .
                                    (string)($car ?? '') . ' ' .
                                    (string)$talleres
                                ));
                            @endphp
                            <tr data-search="{{ $buscarText }}" data-dep="{{ strtolower($dep ?? '') }}" data-car="{{ strtolower($car ?? '') }}" data-taller="{{ strtolower($talleres ?? '') }}">
                                <td>{{ \Carbon\Carbon::parse($asignacion->fecha_entrega)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $dep ?? '—' }}</span>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $car ?? '—' }}</span></td>
                                <td>{{ $talleres ?: '—' }}</td>
                                <td>
                                    <div class="fw-bold">{{ $personal->nombre_completo }}</div>
                                    <small class="text-muted">{{ $personal->dni }}</small>
                                </td>
                                <td>{{ $asignacion->epp->nombre }}</td>
                                <td class="text-center"><span class="badge bg-info text-dark">{{ $asignacion->cantidad }}</span></td>
                                <td>
                                    @php
                                        $estado = (string)$asignacion->estado;
                                        $cls = in_array($estado, ['Posee','Entregado']) ? 'bg-success' : (in_array($estado, ['Dañado','Perdido']) ? 'bg-danger' : 'bg-secondary');
                                    @endphp
                                    <span class="badge rounded-pill {{ $cls }}">{{ $estado }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- Devuelto -->
                                        <form action="{{ route('asignaciones.devolver', $asignacion->id) }}" method="POST" onsubmit="return confirm('Confirmar devolución y sumar al stock?');">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-sm btn-outline-success" {{ $asignacion->estado !== 'Entregado' ? 'disabled' : '' }} title="Marcar como Devuelto">
                                                <i class="bi bi-arrow-counterclockwise"></i> Devuelto
                                            </button>
                                        </form>
                                        <!-- Dañado -->
                                        <form action="{{ route('asignaciones.incidencia', $asignacion->id) }}" method="POST" onsubmit="return confirm('Confirmar EPP Dañado?');">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="estado" value="Dañado">
                                            <button class="btn btn-sm btn-outline-warning" {{ $asignacion->estado !== 'Entregado' ? 'disabled' : '' }} title="Marcar como Dañado">
                                                <i class="bi bi-exclamation-triangle"></i> Dañado
                                            </button>
                                        </form>
                                        <!-- Perdido -->
                                        <form action="{{ route('asignaciones.incidencia', $asignacion->id) }}" method="POST" onsubmit="return confirm('Confirmar EPP Perdido?');">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="estado" value="Perdido">
                                            <button class="btn btn-sm btn-outline-danger" {{ $asignacion->estado !== 'Entregado' ? 'disabled' : '' }} title="Marcar como Perdido">
                                                <i class="bi bi-x-octagon"></i> Perdido
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No hay asignaciones registradas todavía.</td>
                            </tr>
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
        const table = document.getElementById('asignacionesTable');
        if (!table) return;
        const tbody = table.querySelector('tbody');

        function applyFilters() {
            const q = (input?.value || '').trim().toLowerCase();
            const dep = (depSel?.value || '').trim().toLowerCase();
            const car = (carSel?.value || '').trim().toLowerCase();
            const tal = (talSel?.value || '').trim().toLowerCase();
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                const hay = (row.getAttribute('data-search') || '');
                const rdep = (row.getAttribute('data-dep') || '');
                const rcar = (row.getAttribute('data-car') || '');
                const rtal = (row.getAttribute('data-taller') || '');
                const matchText = q === '' || hay.indexOf(q) !== -1;
                const matchDep = dep === '' || rdep === dep;
                const matchCar = car === '' || rcar === car;
                const matchTal = tal === '' || (rtal.split(',').map(s=>s.trim()).includes(tal));
                row.style.display = (matchText && matchDep && matchCar && matchTal) ? '' : 'none';
            });
        }

        input?.addEventListener('input', applyFilters);
        depSel?.addEventListener('change', applyFilters);
        carSel?.addEventListener('change', applyFilters);
        talSel?.addEventListener('change', applyFilters);
    })();
</script>

<style>
    .table-hover tbody tr:hover { background-color: #f8fafc; }
    .btn:disabled { pointer-events: none; opacity: 0.5; }
</style>
@endsection
