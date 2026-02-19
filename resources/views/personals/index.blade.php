@extends('layouts.app')

@section('content')
<style>
    .card-master { border-radius: 20px; border: none; background: #ffffff; }
    .table-modern thead { background-color: #f8f9fa; border-radius: 15px; }
    .table-modern th { border: none; color: #64748b; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding: 20px; }
    .table-modern td { padding: 20px; border-bottom: 1px solid #f1f5f9; }
    .avatar-circle { width: 45px; height: 45px; background: #003a70; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; }
    .status-badge { padding: 6px 12px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; }
</style>

<div class="container py-4">
    @if($message = session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px; border: none; padding: 16px 20px;">
        <i class="bi bi-check-circle me-2"></i>
        <strong>¡Éxito!</strong> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($message = session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px; border: none; padding: 16px 20px;">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Error:</strong> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 px-3">
        <div>
            <h2 class="fw-bold mb-0">Base de Datos de Docentes</h2>
            <p class="text-muted">Lista maestra para asignación de EPP</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('organizador.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-grid-3x3-gap me-2"></i>Ir al Organizador
            </a>
            <button class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalImportarPersonal">
                <i class="bi bi-file-earmark-excel me-2"></i>Importar Excel
            </button>
            <button class="btn btn-dark rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevoDocente">
                <i class="bi bi-person-plus-fill me-2"></i>Registrar Docente
            </button>
            <button class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold d-none" id="btnEliminarSeleccionados" data-bs-toggle="modal" data-bs-target="#modalConfirmarDelete" onclick="prepararEliminacionMultiple()">
                <i class="bi bi-trash me-2"></i>Eliminar Seleccionados
            </button>
            <button class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold" onclick="confirmarVaciarTodo()">
                <i class="bi bi-exclamation-triangle me-2"></i>Vaciar Todo
            </button>
        </div>
    </div>

    <div class="card card-master shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input" onchange="seleccionarTodos(this)">
                            </th>
                            <th>Docente</th>
                            <th>Carrera</th>
                            <th>Tipo</th>
                            <th>DNI / Código</th>
                            <th>Área Asignada</th>
                            <th>EPPs en Uso</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personals as $docente)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input chkDocente" value="{{ $docente->id }}" onchange="actualizarBotonEliminar()">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        {{ strtoupper(substr($docente->nombre_completo, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $docente->nombre_completo }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $docente->carrera ?? '---' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $docente->tipo_contrato ?? '---' }}</span></td>
                            <td class="text-muted fw-bold">{{ $docente->dni ?? '---' }}</td>
                            <td>
                                @if($docente->departamento)
                                    <span class="status-badge bg-primary bg-opacity-10 text-primary">
                                        <i class="bi bi-building me-1"></i> {{ $docente->departamento->nombre }}
                                    </span>
                                @else
                                    <span class="status-badge bg-warning bg-opacity-10 text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Sin Asignar
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php $cantidad = $docente->asignaciones->count(); @endphp
                                @if($cantidad > 0)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
                                        <i class="bi bi-shield-check me-1"></i> {{ $cantidad }} Asignados
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 rounded-pill">
                                        <i class="bi bi-dash-circle me-1"></i> Ninguno
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm" 
                                            onclick="editarDocente({{ $docente->id }}, '{{ $docente->nombre_completo }}', '{{ $docente->dni }}', '{{ $docente->carrera }}', '{{ $docente->tipo_contrato }}')" 
                                            title="Editar">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm text-danger" title="Eliminar" onclick="confirmarEliminacion('{{ route('personals.destroy', $docente->id) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-people display-4 text-light"></i>
                                <p class="text-muted mt-2">No hay docentes en la base de datos.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevoDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 24px; border: none;">
            <div class="modal-body p-5 text-center">
                <div class="bg-dark bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 70px; height: 70px;">
                    <i class="bi bi-person-badge text-dark fs-2"></i>
                </div>
                <h4 class="fw-bold mb-1">Registrar Docente</h4>
                <p class="text-muted mb-4">Añade al personal a la lista maestra</p>

                <form action="{{ route('personals.store') }}" method="POST">
                    @csrf
                    <div class="text-start mb-3">
                        <label class="form-label small fw-bold text-muted">Nombre Completo</label>
                        <input type="text" name="nombre_completo" class="form-control bg-light border-0 py-3 px-4" style="border-radius: 15px;" placeholder="Ej. Pedro Picapiedra" required>
                    </div>
                    <div class="text-start mb-4">
                        <label class="form-label small fw-bold text-muted">DNI o Código de Planilla</label>
                        <input type="text" name="dni" class="form-control bg-light border-0 py-3 px-4" style="border-radius: 15px;" placeholder="Ej. 74859632">
                    </div>
                    <div class="text-start mb-4">
                        <label class="form-label small fw-bold text-muted">Carrera / Especialidad</label>
                        <input type="text" name="carrera" class="form-control bg-light border-0 py-3 px-4" style="border-radius: 15px;" placeholder="Ej. Mecánica, Software...">
                    </div>
                    <div class="text-start mb-4">
                        <label class="form-label small fw-bold text-muted">Tipo de Personal</label>
                        <select name="tipo_contrato" class="form-select bg-light border-0 py-3 px-4" style="border-radius: 15px;">
                            <option value="Docente TC">Docente Tiempo Completo</option>
                            <option value="Docente TP">Docente Tiempo Parcial</option>
                            <option value="Administrativo">Administrativo</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-sm">
                        Guardar en Lista Maestra
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Docente -->
<div class="modal fade" id="modalEditarDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 24px; border: none;">
            <div class="modal-body p-5 text-center">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 70px; height: 70px;">
                    <i class="bi bi-pencil-square text-primary fs-2"></i>
                </div>
                <h4 class="fw-bold mb-1">Editar Docente</h4>
                <p class="text-muted mb-4">Actualizar información del personal</p>

                <form id="formEditarDocente" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="text-start mb-3">
                        <label class="form-label small fw-bold text-muted">Nombre Completo</label>
                        <input type="text" name="nombre_completo" id="edit_nombre" class="form-control bg-light border-0 py-3 px-4" style="border-radius: 15px;" required>
                    </div>
                    <div class="text-start mb-4">
                        <label class="form-label small fw-bold text-muted">DNI o Código de Planilla</label>
                        <input type="text" name="dni" id="edit_dni" class="form-control bg-light border-0 py-3 px-4" style="border-radius: 15px;">
                    </div>
                    <div class="text-start mb-4">
                        <label class="form-label small fw-bold text-muted">Carrera / Especialidad</label>
                        <input type="text" name="carrera" id="edit_carrera" class="form-control bg-light border-0 py-3 px-4" style="border-radius: 15px;">
                    </div>
                    <div class="text-start mb-4">
                        <label class="form-label small fw-bold text-muted">Tipo de Personal</label>
                        <select name="tipo_contrato" id="edit_tipo" class="form-select bg-light border-0 py-3 px-4" style="border-radius: 15px;">
                            <option value="Docente TC">Docente Tiempo Completo</option>
                            <option value="Docente TP">Docente Tiempo Parcial</option>
                            <option value="Administrativo">Administrativo</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm">
                        Guardar Cambios
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminación -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-body p-4 text-center">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">¿Estás seguro?</h5>
                <p class="text-muted mb-4">Vas a eliminar a este docente de la lista maestra. Esta acción no se puede deshacer.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formEliminarDocente" method="POST" action="">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Confirmar eliminación de seleccionados -->
<div class="modal fade" id="modalConfirmarDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-body p-4 text-center">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">¿Eliminar Seleccionados?</h5>
                <p class="text-muted mb-4" id="textConfirmDelete">Vas a eliminar <strong id="countSeleccionados">0</strong> docente(s). Esta acción no se puede deshacer.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formEliminarSeleccionados" method="POST" action="{{ route('personals.delete_multiple') }}">
                        @csrf
                        <input type="hidden" id="inputIds" name="ids" value="">
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Confirmar vaciar todo -->
<div class="modal fade" id="modalConfirmarVaciar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-body p-4 text-center">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                    <i class="bi bi-exclamation-lg text-danger fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">⚠️ ¡Cuidado!</h5>
                <p class="text-muted mb-2"><strong>Estás a punto de eliminar TODOS los docentes</strong></p>
                <p class="text-muted mb-4 small">Total: <strong id="countTotalDocentes">0</strong> docente(s). Esta acción <strong>no se puede deshacer</strong>.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="{{ route('personals.delete_all') }}" style="display: inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, Vaciar Todo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL IMPORTAR PERSONAL --}}
<div class="modal fade" id="modalImportarPersonal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-excel me-2"></i>Importar Docentes desde Excel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('personals.import_excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3 text-center">
                        <i class="bi bi-cloud-arrow-up display-1 text-success opacity-50"></i>
                    </div>
                    <p class="mb-4 text-center">Selecciona el archivo Excel que contiene la matriz de docentes.</p>
                    <input type="file" name="file" class="form-control border-2" accept=".xlsx, .xls, .csv" required>
                    
                    <div class="mt-4 bg-light p-3 rounded-3 small">
                        <p class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Formato esperado del archivo:</p>
                        <ul class="ps-3 mb-2">
                            <li><strong>Hoja:</strong> "Matriz x docente" (segunda hoja)</li>
                            <li><strong>Estructura:</strong>
                                <ul>
                                    <li>Filas de Departamento: "DEPARTAMENTO DE [NOMBRE]"</li>
                                    <li>Encabezado de datos: "Puesto de Trabajo" (o similares)</li>
                                    <li><strong>Columnas después del encabezado (en orden):</strong>
                                        <ol>
                                            <li><strong>Docente:</strong> Nombre completo del docente</li>
                                            <li><strong>TC/TP:</strong> Tipo de contrato (TC, TP, Docente TC, Docente TP)</li>
                                            <li><strong>Taller/Lab:</strong> Nombre del taller o laboratorio</li>
                                            <li><strong>DNI:</strong> Documento de identidad (opcional)</li>
                                            <li><strong>Carrera:</strong> Especialidad o carrera (opcional)</li>
                                        </ol>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <p class="mb-0 text-muted small">✓ Los datos se mostrarán automáticamente en la tabla de arriba después de importar.</p>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-download me-2"></i>Subir e Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarDocente(id, nombre, dni, carrera, tipo) {
    document.getElementById('formEditarDocente').action = '/personals/' + id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_dni').value = dni;
    document.getElementById('edit_carrera').value = carrera;
    document.getElementById('edit_tipo').value = tipo;
    
    var myModal = new bootstrap.Modal(document.getElementById('modalEditarDocente'));
    myModal.show();
}

function confirmarEliminacion(url) {
    document.getElementById('formEliminarDocente').action = url;
    var myModal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    myModal.show();
}

function seleccionarTodos(checkbox) {
    const checkboxes = document.querySelectorAll('.chkDocente');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    actualizarBotonEliminar();
}

function actualizarBotonEliminar() {
    const checkboxes = document.querySelectorAll('.chkDocente:checked');
    const btnEliminar = document.getElementById('btnEliminarSeleccionados');
    const countSeleccionados = document.getElementById('countSeleccionados');
    
    if (checkboxes.length > 0) {
        btnEliminar.classList.remove('d-none');
        countSeleccionados.textContent = checkboxes.length;
    } else {
        btnEliminar.classList.add('d-none');
    }
}

function confirmarVaciarTodo() {
    const totalDocentes = document.querySelectorAll('.chkDocente').length;
    document.getElementById('countTotalDocentes').textContent = totalDocentes;
    
    var myModal = new bootstrap.Modal(document.getElementById('modalConfirmarVaciar'));
    myModal.show();
}

function prepararEliminacionMultiple() {
    const checkboxes = document.querySelectorAll('.chkDocente:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value).join(',');
    document.getElementById('inputIds').value = ids;
}

// Cerrar modal de importación si hay un mensaje de éxito
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalImportarPersonal'));
        if (modal) {
            modal.hide();
        }
    }
});
</script>
@endsection