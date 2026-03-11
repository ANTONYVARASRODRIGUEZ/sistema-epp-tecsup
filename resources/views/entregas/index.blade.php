@extends('layouts.app')

@section('content')
<style>
    /* ── TABLE (desktop) ── */
    .table-desktop {
        min-width: 800px;
    }

    /* ── CARDS (mobile) ── */
    .card-personal {
        border: none;
        border-radius: 16px;
        transition: box-shadow 0.2s;
    }
    .card-personal:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.08) !important; }

    .epp-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f1f3f5;
        border-radius: 8px;
        padding: 3px 8px;
        font-size: 0.78rem;
        margin: 2px;
    }

    /* ── FILTER BAR ── */
    .filter-bar .form-select,
    .filter-bar .input-group {
        min-width: 0;
    }

    /* ── PAGE HEADER ── */
    .page-title { font-size: clamp(1.25rem, 4vw, 1.75rem); }

    /* ── MODAL ── */
    .modal-content { 
        border-radius: 20px !important; 
        border: none !important;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

    .modal-footer {
        flex-shrink: 0;
        background: white;
        z-index: 10;
        border-top: 1px solid #dee2e6 !important;
        position: sticky;
        bottom: 0;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    {{-- ── HEADER ── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h2 class="page-title fw-bold mb-0">Entrega de EPP</h2>
            <p class="text-muted mb-0 small">Lista de personal y asignación de equipos</p>
        </div>
        <button class="btn btn-dark rounded-pill px-4 shadow-sm flex-shrink-0"
            onclick="abrirModalMasivo('{{ $departamentoIdFiltro }}')">
            <i class="bi bi-boxes me-2"></i>Asignar a Todos
        </button>
    </div>

    {{-- ── FILTER BAR ── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-3 px-md-4">
            <form action="{{ route('entregas.index') }}" method="GET"
                  class="filter-bar d-flex align-items-center gap-2 flex-wrap" id="formFiltro">

                <i class="bi bi-funnel fs-5 text-primary d-none d-sm-block"></i>

                <select name="departamento_id" class="form-select form-select-sm shadow-sm"
                        style="max-width: 220px;"
                        onchange="document.getElementById('formFiltro').submit()">
                    <option value="">Todos los departamentos</option>
                    @foreach($departamentos as $depto)
                        <option value="{{ $depto->id }}"
                            {{ (isset($departamentoIdFiltro) && $departamentoIdFiltro == $depto->id) ? 'selected' : '' }}>
                            {{ $depto->nombre }}
                        </option>
                    @endforeach
                </select>

                <div class="input-group input-group-sm shadow-sm flex-grow-1" style="max-width: 280px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0"
                           id="buscadorDocente"
                           placeholder="Buscar docente..."
                           onkeyup="filtrarTabla()">
                </div>

                <a href="{{ route('entregas.index') }}" class="btn btn-sm btn-light border">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         TABLE VIEW (md and up)
    ══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm d-none d-md-block" style="border-radius: 20px; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table table-hover align-middle mb-0 table-desktop" id="tablaPersonal">
                <thead class="table-light">
                    <tr>
                        <th style="width:22%;">Docente</th>
                        <th style="width:12%;">Tipo</th>
                        <th style="width:28%;">EPPs Asignados</th>
                        <th style="width:28%;">Estado / Acciones</th>
                        <th style="width:10%; text-align:center;">Entregar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($personals as $persona)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $persona->nombre_completo }}</p>
                                </div>
                                <button class="btn btn-link btn-sm text-muted ms-2 p-0"
                                    onclick="editarPersonal({{ $persona->id }}, '{{ addslashes($persona->nombre_completo) }}', '{{ $persona->tipo_contrato }}')"
                                    title="Editar datos">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:.82rem;">{{ $persona->tipo_contrato ?? '---' }}</span>
                        </td>

                        <td>
                            @forelse($persona->asignaciones as $asignacion)
                                <div class="py-1 border-bottom d-flex align-items-center" style="font-size:.88rem; min-height:38px;">
                                    <span class="fw-semibold text-dark">{{ $asignacion->epp->nombre }}</span>
                                    <span class="text-muted ms-1">x{{ $asignacion->cantidad }}</span>
                                </div>
                            @empty
                                <span class="text-muted small fst-italic">Sin asignaciones</span>
                            @endforelse
                        </td>

                        <td>
                            @forelse($persona->asignaciones as $asignacion)
                                <div class="py-1 border-bottom d-flex align-items-center gap-1 flex-wrap" style="min-height:38px;">
                                    @if($asignacion->estado == 'Entregado')
                                        <button type="button" class="btn btn-success btn-sm py-0 px-2"
                                                style="font-size:.78rem; white-space:nowrap;"
                                                onclick="confirmarDevolucion('{{ route('asignaciones.devolver', $asignacion->id) }}')">
                                            <i class="bi bi-check-lg me-1"></i>Devolver
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm py-0 px-2"
                                                style="font-size:.78rem; white-space:nowrap;"
                                                onclick="confirmarIncidencia({{ $asignacion->id }}, 'Dañado')">
                                            <i class="bi bi-tools me-1"></i>Dañado
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2"
                                                style="font-size:.78rem; white-space:nowrap;"
                                                onclick="confirmarIncidencia({{ $asignacion->id }}, 'Perdido')">
                                            <i class="bi bi-x-circle me-1"></i>Perdido
                                        </button>
                                    @elseif($asignacion->estado == 'Devuelto')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success" style="font-size:.75rem;">
                                            <i class="bi bi-check-circle me-1"></i>Devuelto
                                        </span>
                                    @elseif($asignacion->estado == 'Dañado')
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning" style="font-size:.75rem;">
                                            <i class="bi bi-tools me-1"></i>Dañado
                                        </span>
                                    @elseif($asignacion->estado == 'Perdido')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger" style="font-size:.75rem;">
                                            <i class="bi bi-x-circle me-1"></i>Perdido
                                        </span>
                                    @endif
                                </div>
                            @empty
                                <span class="text-muted small fst-italic">—</span>
                            @endforelse
                        </td>

                        <td class="text-center">
                            <button class="btn btn-primary btn-sm rounded-pill px-3"
                                onclick="abrirModalEntrega(
                                    {{ $persona->id }},
                                    '{{ addslashes($persona->nombre_completo) }}',
                                    {{ $persona->departamento_id ?? 'null' }}
                                )">
                                <i class="bi bi-hand-index-thumb me-1"></i>Entregar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         CARD VIEW (mobile, < md)
    ══════════════════════════════════════ --}}
    <div class="d-md-none" id="listaCardsMobile">
        @forelse($personals as $persona)
        <div class="card card-personal shadow-sm mb-3 card-mobile-item"
             data-nombre="{{ strtolower($persona->nombre_completo) }}"
             data-dni="{{ strtolower($persona->dni) }}">
            <div class="card-body p-3">

                {{-- Header row --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1 me-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-bold">{{ $persona->nombre_completo }}</span>
                            <button class="btn btn-link btn-sm text-muted p-0"
                                onclick="editarPersonal({{ $persona->id }}, '{{ addslashes($persona->nombre_completo) }}', '{{ $persona->tipo_contrato }}')"
                                title="Editar">
                                <i class="bi bi-pencil-square small"></i>
                            </button>
                        </div>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            <span class="epp-chip text-muted"><i class="bi bi-credit-card me-1"></i>{{ $persona->dni }}</span>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm rounded-pill px-3 flex-shrink-0"
                        onclick="abrirModalEntrega(
                            {{ $persona->id }},
                            '{{ addslashes($persona->nombre_completo) }}',
                            '{{ $persona->departamento_id }}'
                        )">
                        <i class="bi bi-hand-index-thumb"></i>
                    </button>
                </div>

                {{-- Badges --}}
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">{{ $persona->carrera }}</span>
                    <span class="badge bg-light text-dark border">{{ $persona->tipo_contrato ?? '---' }}</span>
                </div>

                {{-- EPPs --}}
                @if($persona->asignaciones->count())
                <div class="border-top pt-2">
                    <small class="text-uppercase text-muted fw-bold" style="font-size:.65rem; letter-spacing:.05em;">EPPs Asignados</small>
                    <div class="mt-1">
                        @foreach($persona->asignaciones as $asignacion)
                        <div class="d-flex align-items-center justify-content-between py-1 border-bottom gap-2 flex-wrap">
                            <span class="small fw-semibold">
                                {{ $asignacion->epp->nombre }}
                                <span class="text-muted fw-normal">x{{ $asignacion->cantidad }}</span>
                            </span>
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                @if($asignacion->estado == 'Entregado')
                                    <button type="button" class="btn btn-success btn-sm py-0 px-2"
                                            style="font-size:.75rem;"
                                            onclick="confirmarDevolucion('{{ route('asignaciones.devolver', $asignacion->id) }}')">
                                        <i class="bi bi-check-lg me-1"></i>Devolver
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm py-0 px-2"
                                            style="font-size:.75rem; white-space:nowrap;"
                                            onclick="confirmarIncidencia({{ $asignacion->id }}, 'Dañado')">
                                        <i class="bi bi-tools me-1"></i>Dañado
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2"
                                            style="font-size:.75rem; white-space:nowrap;"
                                            onclick="confirmarIncidencia({{ $asignacion->id }}, 'Perdido')">
                                        <i class="bi bi-x-circle me-1"></i>Perdido
                                    </button>
                                @elseif($asignacion->estado == 'Devuelto')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success" style="font-size:.7rem;">
                                        <i class="bi bi-check-circle me-1"></i>Devuelto
                                    </span>
                                @elseif($asignacion->estado == 'Dañado')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning" style="font-size:.7rem;">
                                        <i class="bi bi-tools me-1"></i>Dañado
                                    </span>
                                @elseif($asignacion->estado == 'Perdido')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger" style="font-size:.7rem;">
                                        <i class="bi bi-x-circle me-1"></i>Perdido
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                    <div class="border-top pt-2">
                        <span class="text-muted small fst-italic">Sin asignaciones</span>
                    </div>
                @endif

            </div>
        </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-person-x fs-1 d-block mb-2 opacity-25"></i>
                Sin personal registrado.
            </div>
        @endforelse
    </div>

</div>

{{-- ════════════════════════════════════════
     MODAL: Entrega Individual
════════════════════════════════════════ --}}
<div class="modal fade" id="modalEntrega" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Entregar EPP a: <span id="nombreDocente" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('asignaciones.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="personal_id" id="personal_id">
                    <div class="mb-3">
                        <label for="fecha_entrega_individual" class="form-label fw-bold small">Fecha de Entrega</label>
                        <input type="date" name="fecha_entrega" id="fecha_entrega_individual"
                               class="form-control" value="{{ now()->format('Y-m-d') }}">
                        <small class="text-muted">Podés seleccionar una fecha pasada si olvidaste registrarlo antes.</small>
                    </div>
                    <div class="alert alert-light border mb-3 py-2">
                        <small class="text-muted"><i class="bi bi-check2-square me-1"></i>Marca los equipos que deseas entregar.</small>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="buscadorIndividual"
                                   class="form-control border-start-0 ps-0" placeholder="Buscar EPP...">
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-hover align-middle table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width:30px;"></th>
                                    <th>Equipo</th>
                                    <th style="width:60px;" class="text-center">Stock</th>
                                    <th style="width:80px;">Cant.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($epps as $epp)
                                    @if($epp->stock > 0)
                                        <tr>
                                            <td>
                                                <input class="form-check-input" type="checkbox"
                                                       name="epps[{{ $epp->id }}][checked]" value="1"
                                                       id="check_ind_{{ $epp->id }}"
                                                       data-stock="{{ $epp->stock }}">
                                            </td>
                                            <td>
                                                <label class="form-check-label w-100 small"
                                                       for="check_ind_{{ $epp->id }}"
                                                       style="cursor:pointer;">
                                                    {{ $epp->nombre }}
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $epp->stock }}</span>
                                            </td>
                                            <td>
                                                <input type="number" name="epps[{{ $epp->id }}][cantidad]"
                                                       class="form-control form-control-sm text-center"
                                                       value="1" min="1">
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold">Confirmar Entrega</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     MODAL: Asignación Masiva
════════════════════════════════════════ --}}
<div class="modal fade" id="modalMasivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 bg-light" style="border-radius:20px 20px 0 0;">
                <h5 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Asignación Masiva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('entregas.asignar_masivo') }}" method="POST">
                @csrf
                <input type="hidden" name="departamento_id" value="{{ $departamentoIdFiltro ?? '' }}">
                <div class="modal-body p-3 p-sm-4">
                    <div class="alert alert-info border-0 d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-5 flex-shrink-0"></i>
                        <div class="small">Se asignará el equipo seleccionado a <strong>{{ $personals->count() }}</strong> docentes.</div>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_entrega_masiva" class="form-label fw-bold small">Fecha de Entrega</label>
                        <input type="date" name="fecha_entrega" id="fecha_entrega_masiva"
                               class="form-control" value="{{ now()->format('Y-m-d') }}">
                        <small class="text-muted">Podés seleccionar una fecha pasada si olvidaste registrarlo antes.</small>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="buscadorMasivo"
                                   class="form-control border-start-0 ps-0" placeholder="Buscar EPP para todos...">
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Equipo (EPP)</th>
                                    <th style="width:70px;" class="text-center">Stock</th>
                                    <th style="width:90px;">Cant.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($epps as $epp)
                                    @if($epp->stock > 0)
                                        <tr>
                                            <td>
                                                <input class="form-check-input" type="checkbox"
                                                       name="epps[{{ $epp->id }}][checked]" value="1"
                                                       id="check_epp_{{ $epp->id }}"
                                                       data-stock="{{ $epp->stock }}">
                                            </td>
                                            <td>
                                                <label class="form-check-label w-100 small"
                                                       for="check_epp_{{ $epp->id }}"
                                                       style="cursor:pointer;">
                                                    {{ $epp->nombre }}
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $epp->stock }}</span>
                                            </td>
                                            <td>
                                                <input type="number" name="epps[{{ $epp->id }}][cantidad]"
                                                       class="form-control form-control-sm text-center"
                                                       value="1" min="1">
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 px-3 px-sm-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Confirmar Distribución</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     MODAL: Editar Personal
════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditarPersonal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Editar Datos del Docente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarPersonal" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre Completo</label>
                        <input type="text" name="nombre_completo" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tipo de Personal</label>
                        <select name="tipo_contrato" id="edit_tipo" class="form-select">
                            <option value="Docente TC">Docente Tiempo Completo</option>
                            <option value="Docente TP">Docente Tiempo Parcial</option>
                            <option value="Administrativo">Administrativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     MODAL: Confirmación de Acciones
════════════════════════════════════════ --}}
<div class="modal fade" id="modalConfirmacionAccion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-4 text-center">
                <div id="iconoConfirmacion"
                     class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:60px; height:60px;">
                    <i class="bi bi-question-lg text-warning fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2" id="tituloConfirmacion">Confirmar Acción</h5>
                <p class="text-muted mb-4 small" id="mensajeConfirmacion">¿Estás seguro de realizar esta acción?</p>
                <form id="formConfirmacionAccion" method="POST" action="">
                    @csrf @method('PUT')
                    <input type="hidden" name="estado" id="inputEstadoAccion" disabled>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                                data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold"
                                id="btnConfirmarAccion">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

function normalizar(str) {
    return str ? str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase().trim() : "";
}

function abrirModalEntrega(id, nombre, departamentoIdDocente) {
    document.getElementById('personal_id').value = id;
    document.getElementById('nombreDocente').innerText = nombre;
    document.getElementById('personal_id').dataset.deptoId = departamentoIdDocente;

    document.getElementById('fecha_entrega_individual').value = '{{ now()->format('Y-m-d') }}';

    const eppsPorDepartamento = @json($eppsVinculados ?? []);

    document.querySelectorAll('#modalEntrega tbody tr').forEach(row => {
        row.style.display = 'none';
        const check = row.querySelector('input[type="checkbox"]');
        if (check) check.checked = false;
    });

    let idsPermitidos = eppsPorDepartamento[departamentoIdDocente] || [];

    if (idsPermitidos.length > 0) {
        idsPermitidos.forEach(eppId => {
            let checkbox = document.getElementById('check_ind_' + eppId);
            if (checkbox) {
                checkbox.closest('tr').style.display = '';
            }
        });
    }

    new bootstrap.Modal(document.getElementById('modalEntrega')).show();
}


function abrirModalMasivo() {
    let deptoId = document.querySelector('#modalMasivo input[name="departamento_id"]').value;

    document.getElementById('fecha_entrega_masiva').value = '{{ now()->format('Y-m-d') }}';
    
    const eppsPorDepartamento = @json($eppsVinculados ?? []);
    let idsPermitidos = (eppsPorDepartamento[deptoId] || []).map(id => String(id));

    document.querySelectorAll('#modalMasivo tbody tr').forEach(row => {
        let checkbox = row.querySelector('input[type="checkbox"]');
        let eppId = checkbox ? checkbox.id.replace('check_epp_', '') : null;

        if (!deptoId) {
            row.style.display = '';
        } else {
            if (idsPermitidos.includes(eppId)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
                if (checkbox) checkbox.checked = false;
            }
        }
    });

    new bootstrap.Modal(document.getElementById('modalMasivo')).show();
}

// Buscadores en modales
document.getElementById('buscadorIndividual').addEventListener('keyup', function () {
    let f = this.value.toLowerCase();
    let deptoId = document.getElementById('personal_id').dataset.deptoId;
    const eppsPorDepartamento = @json($eppsVinculados ?? []);
    let idsPermitidos = (eppsPorDepartamento[deptoId] || []).map(id => String(id));

    document.querySelectorAll('#modalEntrega tbody tr').forEach(row => {
        let checkbox = row.querySelector('input[type="checkbox"]');
        let eppId = checkbox ? checkbox.id.replace('check_ind_', '') : null;
        let nombreMatch = (row.querySelector('label')?.textContent.toLowerCase() ?? '').includes(f);
        
        if (nombreMatch && idsPermitidos.includes(eppId)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

document.getElementById('buscadorMasivo').addEventListener('keyup', function () {
    let f = this.value.toLowerCase();
    let deptoId = document.querySelector('#modalMasivo input[name="departamento_id"]').value;
    const eppsPorDepartamento = @json($eppsVinculados ?? []);
    let idsPermitidos = (eppsPorDepartamento[deptoId] || []).map(id => String(id));

    document.querySelectorAll('#modalMasivo tbody tr').forEach(row => {
        let checkbox = row.querySelector('input[type="checkbox"]');
        let eppId = checkbox ? checkbox.id.replace('check_epp_', '') : null;
        let nombreMatch = (row.querySelector('label')?.textContent.toLowerCase() ?? '').includes(f);

        if (!deptoId) {
            row.style.display = nombreMatch ? '' : 'none';
        } else {
            if (nombreMatch && idsPermitidos.includes(eppId)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});

// Buscador tabla + cards
function filtrarTabla() {
    let busqueda = document.getElementById('buscadorDocente').value.toLowerCase();

    document.querySelectorAll('#tablaPersonal tbody tr').forEach(row => {
        let nombre = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() ?? '';
        row.style.display = nombre.includes(busqueda) ? '' : 'none';
    });

    document.querySelectorAll('.card-mobile-item').forEach(card => {
        let nombre = card.dataset.nombre ?? '';
        let dni    = card.dataset.dni ?? '';
        card.style.display = (nombre.includes(busqueda) || dni.includes(busqueda)) ? '' : 'none';
    });
}

function editarPersonal(id, nombre, tipo) {
    document.getElementById('formEditarPersonal').action = '/personals/' + id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_tipo').value   = tipo;
    
    new bootstrap.Modal(document.getElementById('modalEditarPersonal')).show();
}

function confirmarDevolucion(url) {
    document.getElementById('formConfirmacionAccion').action = url;
    document.getElementById('tituloConfirmacion').innerText  = 'Confirmar Devolución';
    document.getElementById('mensajeConfirmacion').innerText = '¿Confirmar devolución en buen estado? El stock aumentará.';
    document.getElementById('inputEstadoAccion').disabled    = true;
    document.getElementById('iconoConfirmacion').className   = 'bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3';
    document.getElementById('iconoConfirmacion').innerHTML   = '<i class="bi bi-check-lg text-success fs-3"></i>';
    document.getElementById('btnConfirmarAccion').className  = 'btn btn-success rounded-pill px-4 fw-bold';
    document.getElementById('btnConfirmarAccion').innerText  = 'Sí, Devolver';
    new bootstrap.Modal(document.getElementById('modalConfirmacionAccion')).show();
}

function confirmarIncidencia(id, estado) {
    document.getElementById('formConfirmacionAccion').action = '/asignaciones/' + id + '/incidencia';
    document.getElementById('inputEstadoAccion').disabled    = false;
    document.getElementById('inputEstadoAccion').value       = estado;
    document.getElementById('tituloConfirmacion').innerText  = 'Reportar ' + estado;
    document.getElementById('mensajeConfirmacion').innerText = '¿Marcar este equipo como ' + estado + '?';
    let c = estado === 'Perdido' ? 'danger' : 'warning';
    document.getElementById('iconoConfirmacion').className   = `bg-${c} bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3`;
    document.getElementById('iconoConfirmacion').innerHTML   = `<i class="bi bi-exclamation-triangle-fill text-${c} fs-3"></i>`;
    document.getElementById('btnConfirmarAccion').className  = `btn btn-${c} rounded-pill px-4 fw-bold`;
    document.getElementById('btnConfirmarAccion').innerText  = 'Confirmar';
    new bootstrap.Modal(document.getElementById('modalConfirmacionAccion')).show();
}
</script>
@endsection