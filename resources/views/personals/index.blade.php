@extends('layouts.app')

@section('content')
<style>
    .card-master { border-radius: 20px; border: none; background: #ffffff; }

    .table-modern thead { background-color: #f8f9fa; }
    .table-modern th {
        border: none;
        color: #64748b;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 1px;
        padding: 14px 12px;
        white-space: nowrap;
    }
    .table-modern td {
        padding: 13px 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .table-modern tbody tr:last-child td { border-bottom: none; }

    .avatar-circle {
        width: 38px; height: 38px; min-width: 38px;
        background: #003366; color: white;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; font-weight: 700; font-size: 0.85rem;
    }

    .status-badge {
        padding: 4px 10px; border-radius: 8px;
        font-size: 0.72rem; font-weight: 700; white-space: nowrap;
    }

    .search-box {
        border-radius: 50px; border: 2px solid #e9ecef;
        padding: 8px 16px; transition: 0.2s;
    }
    .search-box:focus {
        border-color: #003366;
        box-shadow: 0 0 0 0.2rem rgba(0,51,102,0.08);
        outline: none;
    }

    /* ── BOTONES DEL HEADER ── */
    .btn-group-header {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    @media (min-width: 768px) {
        .btn-group-header { justify-content: flex-end; }
    }
    /* Altura mínima táctil */
    .btn-group-header .btn {
        min-height: 42px;
        white-space: nowrap;
    }
    /* En xs muy pequeño: solo ícono en botones secundarios */
    @media (max-width: 420px) {
        .btn-group-header .btn { font-size: 0.8rem; padding-left: 10px; padding-right: 10px; }
        .btn-label-long { display: none; }
    }

    .table-scroll-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    .table-scroll-wrapper::-webkit-scrollbar { height: 4px; }
    .table-scroll-wrapper::-webkit-scrollbar-track { background: transparent; }
    .table-scroll-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .row-hidden { display: none; }

    /* Modal form */
    .modal-form .form-control,
    .modal-form .form-select {
        background: #f8f9fa; border: 1.5px solid #e9ecef;
        border-radius: 10px; padding: 10px 14px;
        transition: border-color .2s;
    }
    .modal-form .form-control:focus,
    .modal-form .form-select:focus {
        border-color: #003366; box-shadow: none; background: #fff;
    }
    .modal-form label { font-size: .8rem; font-weight: 700; color: #555; margin-bottom: 5px; }

    .badge-tc    { background: #e8eef7; color: #003366; }
    .badge-tp    { background: #e8f5e9; color: #1b5e20; }
    .badge-admin { background: #f3e8ff; color: #6a1b9a; }
    .badge-otro  { background: #f1f5f9; color: #475569; }

    /* ── HEADER: título + botones ── */
    .page-top-row {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 16px;
    }
    @media (min-width: 768px) {
        .page-top-row {
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
        }
    }

    /* ── BUSCADOR ── */
    .search-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .search-row .search-wrap {
        position: relative;
        flex: 1 1 200px;
        max-width: 440px;
        min-width: 0;
    }
    @media (max-width: 400px) {
        .search-box { font-size: 0.85rem; }
    }

    /* ── TABLA: nombre truncado en pantallas medias ── */
    .personal-nombre {
        word-break: break-word;
        min-width: 140px;
    }

    /* ── ACCIONES: botones circulares con tamaño táctil ── */
    .btn-accion {
        width: 34px; height: 34px;
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0;
        flex-shrink: 0;
    }

    /* ── MODALES: padding reducido en móvil ── */
    @media (max-width: 480px) {
        .modal-body.p-4 { padding: 1.1rem !important; }
        .modal-footer.px-4 { padding-left: 1.1rem !important; padding-right: 1.1rem !important; }
        .modal-header.px-4 { padding-left: 1.1rem !important; padding-right: 1.1rem !important; }
    }

    /* Empty state */
    .bi.display-4 { font-size: clamp(2rem, 10vw, 3.5rem) !important; }
</style>

<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ── ALERTAS ── --}}
    @if($message = session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius:12px;">
        <i class="bi bi-check-circle-fill me-2"></i><strong>¡Éxito!</strong> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($message = session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius:12px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Error:</strong> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── HEADER ── --}}
    <div class="mb-4">
        <div class="page-top-row">
            {{-- Título --}}
            <div>
                <h2 class="fw-bold mb-0" style="font-size: clamp(1.15rem, 4vw, 1.5rem);">Base de Datos de Personal</h2>
                <p class="text-muted mb-0 small">
                    <strong>{{ $personals->count() }}</strong> persona(s) · lista maestra para asignación de EPP
                </p>
            </div>

            {{-- Botones --}}
            <div class="btn-group-header">
                <a href="{{ route('organizador.index') }}"
                   class="btn btn-outline-primary rounded-pill px-3 fw-bold">
                    <i class="bi bi-grid-3x3-gap me-1"></i><span class="btn-label-long">Organizador</span><span class="d-inline d-sm-none"><span class="d-none d-xs-inline">Org.</span></span>
                </a>
                <button class="btn btn-success rounded-pill px-3 fw-bold"
                        data-bs-toggle="modal" data-bs-target="#modalImportarPersonal">
                    <i class="bi bi-file-earmark-excel me-1"></i><span class="btn-label-long">Importar Excel</span>
                </button>
                <button class="btn btn-dark rounded-pill px-3 fw-bold"
                        data-bs-toggle="modal" data-bs-target="#modalNuevoDocente">
                    <i class="bi bi-person-plus-fill me-1"></i><span class="btn-label-long">Registrar</span>
                </button>
                <button class="btn btn-warning rounded-pill px-3 fw-bold d-none"
                        id="btnEliminarSeleccionados"
                        data-bs-toggle="modal" data-bs-target="#modalConfirmarDelete"
                        onclick="prepararEliminacionMultiple()">
                    <i class="bi bi-trash me-1"></i><span class="btn-label-long">Eliminar sel.</span>
                </button>
                <button class="btn btn-danger rounded-pill px-3 fw-bold"
                        onclick="confirmarVaciarTodo()">
                    <i class="bi bi-exclamation-triangle me-1"></i><span class="btn-label-long">Vaciar Todo</span>
                </button>
            </div>
        </div>

        {{-- Buscador --}}
        <div class="search-row">
            <div class="search-wrap">
                <i class="bi bi-search position-absolute text-muted"
                   style="top:50%;left:14px;transform:translateY(-50%);pointer-events:none;"></i>
                <input type="text" id="buscadorDocentes"
                       class="form-control search-box ps-5 w-100"
                       placeholder="Buscar por nombre o tipo...">
            </div>
            <span class="text-muted small" id="contadorResultados"></span>
        </div>
    </div>

    {{-- ── TABLA ── --}}
    <div class="card card-master shadow-sm">
        <div class="card-body p-0">
            <div class="table-scroll-wrapper">
                <table class="table table-modern mb-0" style="min-width:620px;">
                    <thead>
                        <tr>
                            <th style="width:36px;">
                                <input type="checkbox" id="selectAll" class="form-check-input"
                                       onchange="seleccionarTodos(this)">
                            </th>
                            <th>Docente / Personal</th>
                            <th>Tipo</th>
                            <th>Área Asignada</th>
                            <th class="text-center">EPPs en Uso</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaDocentes">
                        @forelse($personals as $docente)
                        @php
                            $tipo = $docente->tipo_contrato ?? '---';
                            $tipoClass = match(true) {
                                str_contains($tipo, 'TC')    => 'badge-tc',
                                str_contains($tipo, 'TP')    => 'badge-tp',
                                str_contains($tipo, 'Admin') => 'badge-admin',
                                default                      => 'badge-otro',
                            };
                            $eppsCount = $docente->asignaciones->count();
                        @endphp
                        <tr class="fila-docente"
                            data-nombre="{{ strtolower($docente->nombre_completo) }}"
                            data-tipo="{{ strtolower($tipo) }}">
                            <td>
                                <input type="checkbox" class="form-check-input chkDocente"
                                       value="{{ $docente->id }}"
                                       onchange="actualizarBotonEliminar()">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle flex-shrink-0">
                                        {{ strtoupper(substr($docente->nombre_completo, 0, 1)) }}
                                    </div>
                                    <span class="fw-bold text-dark personal-nombre">{{ $docente->nombre_completo }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge {{ $tipoClass }}">{{ $tipo }}</span>
                            </td>
                            <td>
                                @if($docente->departamento)
                                    <span class="status-badge badge-tc">
                                        <i class="bi bi-building me-1"></i>{{ $docente->departamento->nombre }}
                                    </span>
                                @else
                                    <span class="status-badge" style="background:#fff8e1;color:#b45309;">
                                        <i class="bi bi-exclamation-circle me-1"></i>Sin asignar
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($eppsCount > 0)
                                    <span class="badge rounded-pill px-3 py-1"
                                          style="background:#d1fae5;color:#065f46;font-size:.75rem;">
                                        <i class="bi bi-shield-check me-1"></i>{{ $eppsCount }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-1"
                                          style="background:#f1f5f9;color:#94a3b8;font-size:.75rem;">
                                        <i class="bi bi-dash me-1"></i>Ninguno
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-accion"
                                            onclick="abrirModalEditar(this)"
                                            data-id="{{ $docente->id }}"
                                            data-nombre="{{ $docente->nombre_completo }}"
                                            data-tipo="{{ $tipo }}"
                                            title="Editar">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-accion"
                                            onclick="confirmarEliminacion('{{ route('personals.destroy', $docente->id) }}')"
                                            title="Eliminar">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-people display-4 text-muted opacity-25"></i>
                                <p class="text-muted mt-3 mb-2">No hay personal registrado.</p>
                                <button class="btn btn-sm btn-outline-dark rounded-pill"
                                        data-bs-toggle="modal" data-bs-target="#modalNuevoDocente">
                                    <i class="bi bi-person-plus me-1"></i>Registrar primero
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="sinResultados" class="text-center py-5 d-none">
                <i class="bi bi-search display-4 text-muted opacity-25"></i>
                <p class="text-muted mt-3 mb-1 fw-semibold">Sin resultados</p>
                <p class="text-muted small mb-2">Ningún personal coincide con la búsqueda.</p>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="limpiarBusqueda()">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════ MODALES ══════════════════ --}}

{{-- Registrar --}}
<div class="modal fade" id="modalNuevoDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:12px;background:#e8eef7;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-person-plus-fill" style="color:#003366;font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Registrar Personal</h5>
                        <p class="text-muted mb-0" style="font-size:.78rem;">Añade a la lista maestra</p>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('personals.store') }}" method="POST" class="modal-form">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label>Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_completo" class="form-control"
                               placeholder="Ej. Pedro Ramírez Torres" required autocomplete="off">
                    </div>
                    <div>
                        <label>Tipo de Personal <span class="text-danger">*</span></label>
                        <select name="tipo_contrato" class="form-select" required>
                            <option value="Docente TC">Docente Tiempo Completo (TC)</option>
                            <option value="Docente TP">Docente Tiempo Parcial (TP)</option>
                            <option value="Administrativo">Administrativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                    <button type="button" class="btn btn-light flex-fill rounded-pill"
                            data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn flex-fill rounded-pill fw-bold text-white"
                            style="background:#003366;">
                        <i class="bi bi-save me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Editar --}}
<div class="modal fade" id="modalEditarDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:12px;background:#e8eef7;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-pencil-square" style="color:#003366;font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Editar Personal</h5>
                        <p class="text-muted mb-0" style="font-size:.78rem;">Actualizar información</p>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarDocente" method="POST" class="modal-form">
                @csrf @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label>Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_completo" id="edit_nombre"
                               class="form-control" required>
                    </div>
                    <div>
                        <label>Tipo de Personal</label>
                        <select name="tipo_contrato" id="edit_tipo" class="form-select">
                            <option value="Docente TC">Docente Tiempo Completo (TC)</option>
                            <option value="Docente TP">Docente Tiempo Parcial (TP)</option>
                            <option value="Administrativo">Administrativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                    <button type="button" class="btn btn-light flex-fill rounded-pill"
                            data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn flex-fill rounded-pill fw-bold text-white"
                            style="background:#003366;">
                        <i class="bi bi-save me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Eliminar individual --}}
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
        <div class="modal-content border-0 shadow" style="border-radius:18px;">
            <div class="modal-body p-4 text-center">
                <div style="width:56px;height:56px;border-radius:50%;background:#fdecea;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                </div>
                <h5 class="fw-bold mb-2">¿Eliminar este registro?</h5>
                <p class="text-muted mb-4" style="font-size:.88rem;">
                    Se eliminará el personal y sus asignaciones de EPP.<br>
                    <strong>Esta acción no se puede deshacer.</strong>
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light flex-fill rounded-pill"
                            data-bs-dismiss="modal">Cancelar</button>
                    <form id="formEliminarDocente" method="POST" class="flex-fill">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">
                            Sí, eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Eliminar seleccionados --}}
<div class="modal fade" id="modalConfirmarDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
        <div class="modal-content border-0 shadow" style="border-radius:18px;">
            <div class="modal-body p-4 text-center">
                <div style="width:56px;height:56px;border-radius:50%;background:#fdecea;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                </div>
                <h5 class="fw-bold mb-2">¿Eliminar seleccionados?</h5>
                <p class="text-muted mb-4" style="font-size:.88rem;">
                    Vas a eliminar <strong id="countSeleccionados">0</strong> registro(s)
                    y sus asignaciones de EPP.
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light flex-fill rounded-pill"
                            data-bs-dismiss="modal">Cancelar</button>
                    <form id="formEliminarSeleccionados" method="POST"
                          action="{{ route('personals.delete_multiple') }}" class="flex-fill">
                        @csrf
                        <input type="hidden" id="inputIds" name="ids" value="">
                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">
                            Sí, eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Vaciar Todo --}}
<div class="modal fade" id="modalConfirmarVaciar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
        <div class="modal-content border-0 shadow" style="border-radius:18px;">
            <div class="modal-body p-4 text-center">
                <div style="width:56px;height:56px;border-radius:50%;background:#fdecea;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <i class="bi bi-exclamation-lg text-danger fs-4"></i>
                </div>
                <h5 class="fw-bold mb-2">¡Vaciar toda la base?</h5>
                <p class="text-muted mb-1" style="font-size:.88rem;">
                    Se eliminarán <strong id="countTotalDocentes">0</strong> registro(s)
                    y <strong>todas</strong> sus asignaciones de EPP.
                </p>
                <p class="text-danger small fw-semibold mb-4">Esta acción no se puede deshacer.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light flex-fill rounded-pill"
                            data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="{{ route('personals.delete_all') }}" class="flex-fill">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">
                            Sí, vaciar todo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Importar Excel --}}
<div class="modal fade" id="modalImportarPersonal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
            <div class="modal-header border-0 text-white px-4 py-3" style="background:#198754;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-excel fs-5"></i>
                    <h5 class="modal-title fw-bold mb-0">Importar desde Excel</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('personals.import_excel') }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <i class="bi bi-cloud-arrow-up"
                           style="font-size:3rem;color:#198754;opacity:.55;"></i>
                    </div>
                    <p class="text-center text-muted mb-3">
                        Selecciona el archivo Excel con la lista de personal.
                    </p>
                    <input type="file" name="file" class="form-control"
                           accept=".xlsx,.xls,.csv" required>
                    <div class="mt-3 p-3 rounded-3" style="background:#f8f9fa;font-size:.8rem;">
                        <p class="fw-bold mb-2">
                            <i class="bi bi-info-circle me-1 text-success"></i>Formato esperado:
                        </p>
                        <ul class="mb-0 ps-3 text-muted">
                            <li><strong>Hoja:</strong> "Matriz x docente" (segunda hoja)</li>
                            <li><strong>Columnas:</strong> Docente, TC/TP, Taller/Lab, DNI, Carrera</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light flex-fill rounded-pill"
                            data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success flex-fill rounded-pill fw-bold">
                        <i class="bi bi-download me-1"></i>Subir e Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Buscador ──────────────────────────────────────────
    const buscador   = document.getElementById('buscadorDocentes');
    const contador   = document.getElementById('contadorResultados');
    const sinRes     = document.getElementById('sinResultados');
    const tblWrapper = document.querySelector('.table-scroll-wrapper');

    buscador.addEventListener('input', function () {
        const q     = this.value.trim().toLowerCase();
        const filas = document.querySelectorAll('.fila-docente');
        let vis     = 0;

        filas.forEach(f => {
            const match = !q
                || f.dataset.nombre.includes(q)
                || f.dataset.tipo.includes(q);
            f.classList.toggle('row-hidden', !match);
            if (match) vis++;
        });

        contador.textContent = q ? `${vis} de ${filas.length}` : '';
        sinRes.classList.toggle('d-none', vis > 0 || !q);
        if (tblWrapper) tblWrapper.classList.toggle('d-none', vis === 0 && !!q);
    });

    // Cerrar modal importar tras éxito
    @if(session('success'))
    const m = document.getElementById('modalImportarPersonal');
    if (m) { const inst = bootstrap.Modal.getInstance(m); if (inst) inst.hide(); }
    @endif
});

function limpiarBusqueda() {
    const b = document.getElementById('buscadorDocentes');
    b.value = ''; b.dispatchEvent(new Event('input'));
}

function abrirModalEditar(btn) {
    document.getElementById('formEditarDocente').action = '/personals/' + btn.dataset.id;
    document.getElementById('edit_nombre').value = btn.dataset.nombre;
    document.getElementById('edit_tipo').value   = btn.dataset.tipo;
    const el = document.getElementById('modalEditarDocente');
    (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show();
}

function confirmarEliminacion(url) {
    document.getElementById('formEliminarDocente').action = url;
    new bootstrap.Modal(document.getElementById('modalConfirmarEliminar')).show();
}

function seleccionarTodos(cb) {
    document.querySelectorAll('.chkDocente').forEach(c => c.checked = cb.checked);
    actualizarBotonEliminar();
}

function actualizarBotonEliminar() {
    const n = document.querySelectorAll('.chkDocente:checked').length;
    document.getElementById('btnEliminarSeleccionados').classList.toggle('d-none', n === 0);
    document.getElementById('countSeleccionados').textContent = n;
}

function prepararEliminacionMultiple() {
    const ids = [...document.querySelectorAll('.chkDocente:checked')].map(c => c.value).join(',');
    document.getElementById('inputIds').value = ids;
}

function confirmarVaciarTodo() {
    document.getElementById('countTotalDocentes').textContent =
        document.querySelectorAll('.chkDocente').length;
    new bootstrap.Modal(document.getElementById('modalConfirmarVaciar')).show();
}
</script>
@endsection