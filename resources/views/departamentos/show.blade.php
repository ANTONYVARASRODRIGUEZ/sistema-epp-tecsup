@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">{{ $departamento->nombre }}</h2>
            <p class="text-muted mb-0">Lista de personal y asignación de equipos</p>
        </div>
        <button class="btn btn-dark rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalMasivo">
            <i class="bi bi-boxes me-2"></i> Asignar a Todos
        </button>
    </div>

    <!-- Filtro rápido por Carrera -->
    <div class="mb-3">
        <select id="filtroCarrera" class="form-select w-auto d-inline-block shadow-sm" onchange="filtrarPorCarrera()">
            <option value="">Todos las carreras</option>
            @foreach($departamento->personals->pluck('carrera')->unique() as $carrera)
                <option value="{{ $carrera }}">{{ $carrera }}</option>
            @endforeach
        </select>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="table-responsive p-4">
            <table class="table align-middle" id="tablaPersonal">
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Carrera</th>
                        <th>Tipo</th>
                        <th>DNI</th>
                        <th>EPPs Asignados</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departamento->personals as $persona)
                    <tr class="fila-personal" data-carrera="{{ $persona->carrera }}">
                        <td>
                            <div class="d-flex align-items-center">
                                {{ $persona->nombre_completo }}
                                <button class="btn btn-link btn-sm text-muted ms-2 p-0" onclick="editarPersonal({{ $persona->id }}, '{{ $persona->nombre_completo }}', '{{ $persona->dni }}', '{{ $persona->carrera }}', '{{ $persona->tipo_contrato }}')" title="Editar datos">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $persona->carrera }}</span></td>
                        <td><span class="badge bg-light text-dark border">{{ $persona->tipo_contrato ?? '---' }}</span></td>
                        <td>{{ $persona->dni }}</td>
                        <td>
                            @forelse($persona->asignaciones as $asignacion)
                                @if($asignacion->estado == 'Posee')
                                    <div class="d-flex align-items-center justify-content-between border rounded p-1 mb-1 bg-white">
                                        <small class="text-dark">
                                            {{ $asignacion->epp->nombre }} <span class="fw-bold">x{{ $asignacion->cantidad }}</span>
                                        </small>
                                        <div class="ms-2">
                                            <!-- Botón Devolver (OK) -->
                                            <button type="button" class="btn btn-success btn-sm p-0 px-1" title="Devolver (OK)" onclick="confirmarDevolucion('{{ route('asignaciones.devolver', $asignacion->id) }}')">
                                                <i class="bi bi-check"></i>
                                            </button>
                                            <!-- Botón Dañado/Perdido -->
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-danger btn-sm p-0 px-1 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                                                <ul class="dropdown-menu p-1" style="font-size: 0.85rem;">
                                                    <li><button class="dropdown-item text-warning" onclick="confirmarIncidencia({{ $asignacion->id }}, 'Dañado')">Reportar Dañado</button></li>
                                                    <li><button class="dropdown-item text-danger" onclick="confirmarIncidencia({{ $asignacion->id }}, 'Perdido')">Reportar Perdido</button></li>
                                                </ul>
                                            </div>
                                        @elseif($asignacion->estado == 'Devuelto')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success">Devuelto</span>
                                        @elseif($asignacion->estado == 'Dañado')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Dañado</span>
                                        @elseif($asignacion->estado == 'Perdido')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Perdido</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <span class="text-muted small fst-italic">Sin asignaciones</span>
                            @endforelse
                        </td>
                        <td class="text-end">
                            <button class="btn btn-primary btn-sm rounded-pill px-3" 
                                    onclick="abrirModalEntrega({{ $persona->id }}, '{{ $persona->nombre_completo }}')">
                                <i class="bi bi-hand-index-thumb me-1"></i> Entregar EPP
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEntrega" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Entregar EPP a: <span id="nombreDocente" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('asignaciones.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="personal_id" id="personal_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Seleccionar Equipo (EPP)</label>
                        <select name="epp_id" class="form-select" required>
                            <option value="">-- Buscar equipo --</option>
                            @foreach($epps as $epp)
                                <option value="{{ $epp->id }}">{{ $epp->nombre }} (Stock: {{ $epp->stock }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" value="1" min="1" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold">Confirmar Entrega</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Asignación Masiva -->
<div class="modal fade" id="modalMasivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-light" style="border-radius: 20px 20px 0 0;">
                <h5 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Asignación Masiva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('departamentos.asignar_masivo', $departamento->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                        <div>
                            Se asignará el equipo seleccionado a <strong>{{ $departamento->personals->count() }}</strong> docentes de esta área.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Seleccionar Equipo (EPP)</label>
                        <select name="epp_id" class="form-select form-select-lg" required>
                            <option value="">-- Seleccionar EPP --</option>
                            @foreach($epps as $epp)
                                <option value="{{ $epp->id }}">{{ $epp->nombre }} (Stock: {{ $epp->stock }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Cantidad por persona</label>
                        <input type="number" name="cantidad" class="form-control form-control-lg" value="1" min="1" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Confirmar Distribución</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Personal -->
<div class="modal fade" id="modalEditarPersonal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Editar Datos del Docente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarPersonal" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre_completo" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">DNI</label>
                        <input type="text" name="dni" id="edit_dni" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Carrera / Especialidad</label>
                        <input type="text" name="carrera" id="edit_carrera" class="form-control" placeholder="Ej: Tecnología Digital">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Personal</label>
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

<!-- Modal Confirmación de Acciones (Devolver / Incidencia) -->
<div class="modal fade" id="modalConfirmacionAccion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-body p-4 text-center">
                <div id="iconoConfirmacion" class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                    <i class="bi bi-question-lg text-warning fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2" id="tituloConfirmacion">Confirmar Acción</h5>
                <p class="text-muted mb-4" id="mensajeConfirmacion">¿Estás seguro de realizar esta acción?</p>
                
                <form id="formConfirmacionAccion" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="estado" id="inputEstadoAccion" disabled>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold" id="btnConfirmarAccion">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalEntrega(id, nombre) {
    document.getElementById('personal_id').value = id;
    document.getElementById('nombreDocente').innerText = nombre;
    var myModal = new bootstrap.Modal(document.getElementById('modalEntrega'));
    myModal.show();
}

function editarPersonal(id, nombre, dni, carrera, tipo) {
    document.getElementById('formEditarPersonal').action = '/personals/' + id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_dni').value = dni;
    document.getElementById('edit_carrera').value = carrera;
    document.getElementById('edit_tipo').value = tipo;
    
    var myModal = new bootstrap.Modal(document.getElementById('modalEditarPersonal'));
    myModal.show();
}

function filtrarPorCarrera() {
    let carreraSeleccionada = document.getElementById('filtroCarrera').value;
    let filas = document.querySelectorAll('.fila-personal');

    filas.forEach(fila => {
        if (carreraSeleccionada === "" || fila.getAttribute('data-carrera') === carreraSeleccionada) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
}

function confirmarDevolucion(url) {
    document.getElementById('formConfirmacionAccion').action = url;
    document.getElementById('tituloConfirmacion').innerText = 'Confirmar Devolución';
    document.getElementById('mensajeConfirmacion').innerText = '¿Confirmar devolución en buen estado? El stock aumentará.';
    document.getElementById('inputEstadoAccion').disabled = true;
    
    // Estilos visuales para éxito
    document.getElementById('iconoConfirmacion').className = 'bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4';
    document.getElementById('iconoConfirmacion').innerHTML = '<i class="bi bi-check-lg text-success fs-3"></i>';
    document.getElementById('btnConfirmarAccion').className = 'btn btn-success rounded-pill px-4 fw-bold';
    document.getElementById('btnConfirmarAccion').innerText = 'Sí, Devolver';

    var myModal = new bootstrap.Modal(document.getElementById('modalConfirmacionAccion'));
    myModal.show();
}

function confirmarIncidencia(id, estado) {
    let url = '/asignaciones/' + id + '/incidencia';
    document.getElementById('formConfirmacionAccion').action = url;
    document.getElementById('inputEstadoAccion').disabled = false;
    document.getElementById('inputEstadoAccion').value = estado;
    
    document.getElementById('tituloConfirmacion').innerText = 'Reportar ' + estado;
    document.getElementById('mensajeConfirmacion').innerText = '¿Marcar este equipo como ' + estado + '?';

    // Estilos visuales para alerta/peligro
    let colorClass = estado === 'Perdido' ? 'danger' : 'warning';
    document.getElementById('iconoConfirmacion').className = 'bg-' + colorClass + ' bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4';
    document.getElementById('iconoConfirmacion').innerHTML = '<i class="bi bi-exclamation-triangle-fill text-' + colorClass + ' fs-3"></i>';
    document.getElementById('btnConfirmarAccion').className = 'btn btn-' + colorClass + ' rounded-pill px-4 fw-bold';
    document.getElementById('btnConfirmarAccion').innerText = 'Confirmar';

    var myModal = new bootstrap.Modal(document.getElementById('modalConfirmacionAccion'));
    myModal.show();
}
</script>
@endsection