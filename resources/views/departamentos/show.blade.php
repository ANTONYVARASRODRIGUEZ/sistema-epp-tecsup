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
                                @php $tallerNombre = $persona->talleres->first()->nombre ?? ''; @endphp
                                {{ $persona->nombre_completo }}
                                <button class="btn btn-link btn-sm text-muted ms-2 p-0" onclick="editarPersonal({{ $persona->id }}, '{{ $persona->nombre_completo }}', '{{ $persona->dni }}', '{{ $persona->carrera }}', '{{ $persona->tipo_contrato }}', '{{ $tallerNombre }}')" title="Editar datos">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $persona->carrera }}</span></td>
                        <td><span class="badge bg-light text-dark border">{{ $persona->tipo_contrato ?? '---' }}</span></td>
                        <td>{{ $persona->dni }}</td>
                        <td>
                            @forelse($persona->asignaciones as $asignacion)
                                <div class="d-flex align-items-center justify-content-between border rounded p-1 mb-1 bg-white">
                                    <small class="text-dark">
                                        {{ $asignacion->epp->nombre }} <span class="fw-bold">x{{ $asignacion->cantidad }}</span>
                                    </small>
                                    
                                    <div class="ms-2">
                                        @if($asignacion->estado == 'Entregado')
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
                                    onclick="abrirModalEntrega({{ $persona->id }}, '{{ $persona->nombre_completo }}', '{{ $tallerNombre }}', '{{ $persona->tipo_contrato ?? '' }}')">
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
                    
                    <div class="alert alert-light border mb-3 py-2">
                        <small class="text-muted"><i class="bi bi-check2-square me-1"></i> Marca los equipos que deseas entregar.</small>
                    </div>

                    <!-- Buscador Individual -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="buscadorIndividual" class="form-control border-start-0 ps-0" placeholder="Buscar EPP...">
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-hover align-middle table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 30px;"></th>
                                    <th>Equipo</th>
                                    <th style="width: 60px;" class="text-center">Stock</th>
                                    <th style="width: 80px;">Cant.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($epps as $epp)
                                <tr>
                                    <td>
                                        <input class="form-check-input" type="checkbox" name="epps[{{ $epp->id }}][checked]" value="1" id="check_ind_{{ $epp->id }}">
                                    </td>
                                    <td>
                                        <label class="form-check-label w-100 small" for="check_ind_{{ $epp->id }}" style="cursor: pointer;">
                                            {{ $epp->nombre }}
                                            <span id="badge_info_{{ $epp->id }}" class="badge ms-1" style="display: none; font-size: 0.7em;"></span>
                                        </label>
                                    </td>
                                    <td class="text-center"><span class="badge bg-secondary">{{ $epp->stock }}</span></td>
                                    <td>
                                        <input type="number" name="epps[{{ $epp->id }}][cantidad]" class="form-control form-control-sm text-center" value="1" min="1">
                                    </td>
                                </tr>
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

                    <!-- Buscador Masivo -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="buscadorMasivo" class="form-control border-start-0 ps-0" placeholder="Buscar EPP para todos...">
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Equipo (EPP)</th>
                                    <th style="width: 80px;">Stock</th>
                                    <th style="width: 100px;">Cant.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($epps as $epp)
                                <tr>
                                    <td>
                                        <input class="form-check-input" type="checkbox" name="epps[{{ $epp->id }}][checked]" value="1" id="check_epp_{{ $epp->id }}">
                                    </td>
                                    <td>
                                        <label class="form-check-label w-100" for="check_epp_{{ $epp->id }}" style="cursor: pointer;">
                                            {{ $epp->nombre }}
                                        </label>
                                    </td>
                                    <td class="text-center"><span class="badge bg-secondary">{{ $epp->stock }}</span></td>
                                    <td>
                                        <input type="number" name="epps[{{ $epp->id }}][cantidad]" class="form-control form-control-sm text-center" value="1" min="1">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                    <div class="mb-3">
                        <label class="form-label">Taller / Laboratorio</label>
                        <input type="text" name="taller_nombre" id="edit_taller" class="form-control" list="listaTalleres" placeholder="Escribe o selecciona..." autocomplete="off">
                        <datalist id="listaTalleres">
                            @foreach($talleres as $taller)
                                <option value="{{ $taller->nombre }}">
                            @endforeach
                        </datalist>
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
// Recibimos la matriz desde el controlador
const matrizReglas = @json($matriz ?? []);

// Función para normalizar texto (quitar tildes, minúsculas y espacios extra)
const normalizar = (str) => str ? str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase().trim() : "";

function abrirModalEntrega(id, nombre, tallerDocente, puestoDocente) {
    document.getElementById('personal_id').value = id;
    document.getElementById('nombreDocente').innerText = nombre;
    
    // 0. Limpiar buscador y mostrar todas las filas
    document.getElementById('buscadorIndividual').value = '';
    document.querySelectorAll('#modalEntrega tbody tr').forEach(row => row.style.display = '');

    // 1. Resetear checkboxes, cantidades y badges
    document.querySelectorAll('#modalEntrega input[type="checkbox"]').forEach(chk => chk.checked = false);
    document.querySelectorAll('#modalEntrega input[type="number"]').forEach(input => input.value = 1);
    document.querySelectorAll('[id^="badge_info_"]').forEach(el => el.style.display = 'none');

    // 2. Aplicar lógica de la Matriz de Homologación
    matrizReglas.forEach(regla => {
        let rTaller = normalizar(regla.taller);
        let rPuesto = normalizar(regla.puesto);
        let dTaller = normalizar(tallerDocente);
        let dPuesto = normalizar(puestoDocente);

        // Coincidencia flexible:
        // - Taller: Si la regla no tiene taller (es general) O si coincide con el del docente
        let coincideTaller = !rTaller || rTaller === dTaller || (dTaller && dTaller.includes(rTaller));
        
        // - Puesto: Si la regla no tiene puesto O si el puesto del docente contiene la palabra clave (ej: "Docente" en "Docente TC")
        let coincidePuesto = !rPuesto || (dPuesto && dPuesto.includes(rPuesto));

        // Si coincide el perfil, marcamos el EPP (sea obligatorio o específico para ese taller)
        if (coincideTaller && coincidePuesto) {
            // Buscar el checkbox de este EPP y marcarlo
            let checkbox = document.getElementById('check_ind_' + regla.epp_id);
            if (checkbox) {
                checkbox.checked = true;
                
                // Mostrar etiqueta visual para que Jiancarlo sepa por qué se marcó
                let badge = document.getElementById('badge_info_' + regla.epp_id);
                if (badge) {
                    let esObligatorio = regla.tipo_requerimiento === 'obligatorio';
                    badge.innerText = esObligatorio ? 'Obligatorio' : 'Sugerido Taller';
                    badge.className = esObligatorio ? 'badge bg-danger ms-1' : 'badge bg-info text-dark ms-1';
                    badge.style.display = 'inline-block';
                }
            }
        }
    });

    // 3. Ordenar la tabla: Los marcados primero para facilitar la vista
    let tbody = document.querySelector('#modalEntrega tbody');
    let rows = Array.from(tbody.querySelectorAll('tr'));
    rows.sort((a, b) => {
        let chkA = a.querySelector('input[type="checkbox"]').checked;
        let chkB = b.querySelector('input[type="checkbox"]').checked;
        return (chkA === chkB) ? 0 : (chkA ? -1 : 1);
    });
    rows.forEach(row => tbody.appendChild(row));

    var myModal = new bootstrap.Modal(document.getElementById('modalEntrega'));
    myModal.show();
}

// Lógica del Buscador Individual
document.getElementById('buscadorIndividual').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#modalEntrega tbody tr');
    rows.forEach(row => {
        let text = row.querySelector('label').textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Lógica del Buscador Masivo
document.getElementById('buscadorMasivo').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#modalMasivo tbody tr');
    rows.forEach(row => {
        let text = row.querySelector('label').textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

function editarPersonal(id, nombre, dni, carrera, tipo, tallerNombre) {
    document.getElementById('formEditarPersonal').action = '/personals/' + id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_dni').value = dni;
    document.getElementById('edit_carrera').value = carrera;
    document.getElementById('edit_tipo').value = tipo;
    document.getElementById('edit_taller').value = tallerNombre;
    
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