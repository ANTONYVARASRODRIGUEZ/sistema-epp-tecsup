@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Configuración del Sistema</h2>
            <p class="text-muted">Parámetros generales y control administrativo</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Pestañas de Navegación -->
    <ul class="nav nav-tabs border-bottom-2" role="tablist" style="border-color: #e0e0e0;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                <i class="bi bi-gear me-2"></i>General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="parametros-tab" data-bs-toggle="tab" data-bs-target="#parametros" type="button" role="tab">
                <i class="bi bi-sliders me-2"></i>Parámetros EPP
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="departamentos-tab" data-bs-toggle="tab" data-bs-target="#departamentos" type="button" role="tab">
                <i class="bi bi-building me-2"></i>Departamentos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="matriz-tab" data-bs-toggle="tab" data-bs-target="#matriz" type="button" role="tab">
                <i class="bi bi-grid-3x3 me-2"></i>Matriz EPP
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="notificaciones-tab" data-bs-toggle="tab" data-bs-target="#notificaciones" type="button" role="tab">
                <i class="bi bi-bell me-2"></i>Notificaciones
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="auditoria-tab" data-bs-toggle="tab" data-bs-target="#auditoria" type="button" role="tab">
                <i class="bi bi-clock-history me-2"></i>Auditoría
            </button>
        </li>
    </ul>

    <!-- Contenido de las Pestañas -->
    <div class="tab-content mt-4">
        
        <!-- TAB 1: GENERAL -->
        <div class="tab-pane fade show active" id="general" role="tabpanel">
            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4">
                        <h5 class="fw-bold mb-4">Configuración General del Sistema</h5>
                        <form action="{{ route('configuracion.actualizar-general') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre del Sistema</label>
                                <input type="text" name="nombre_sistema" class="form-control" value="{{ old('nombre_sistema', $configuracion->nombre_sistema) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Sede</label>
                                <input type="text" name="sede" class="form-control" value="{{ old('sede', $configuracion->sede) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Año Académico</label>
                                <input type="text" name="anio_academico" class="form-control" value="{{ old('anio_academico', $configuracion->anio_academico) }}" required maxlength="4">
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" style="background-color: #003366; border: none;">
                                    <i class="bi bi-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: PARÁMETROS EPP -->
        <div class="tab-pane fade" id="parametros" role="tabpanel">
            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4">
                        <h5 class="fw-bold mb-4">Parámetros de Control de EPP</h5>
                        <form action="{{ route('configuracion.actualizar-parametros-epp') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tiempo de Renovación (días)</label>
                                <input type="number" name="tiempo_renovacion_dias" class="form-control" value="{{ old('tiempo_renovacion_dias', $configuracion->tiempo_renovacion_dias) }}" required min="1">
                                <small class="text-muted">Días antes de que se venza un EPP para enviar alertas</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Umbral de Stock Bajo (unidades)</label>
                                <input type="number" name="umbral_stock_bajo" class="form-control" value="{{ old('umbral_stock_bajo', $configuracion->umbral_stock_bajo) }}" required min="1">
                                <small class="text-muted">Cantidad mínima de unidades para generar alerta</small>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" style="background-color: #003366; border: none;">
                                    <i class="bi bi-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: DEPARTAMENTOS -->
        <div class="tab-pane fade" id="departamentos" role="tabpanel">
            <div class="row">
                <!-- Crear Departamento -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm p-4">
                        <h6 class="fw-bold mb-3">Nuevo Departamento</h6>
                        <form action="{{ route('configuracion.crear-departamento') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <input type="text" name="nombre" class="form-control" placeholder="Nombre del departamento" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" style="background-color: #003366;">
                                <i class="bi bi-plus me-2"></i>Crear
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Listado de Departamentos -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm p-4">
                        <h6 class="fw-bold mb-3">Departamentos Registrados</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($departamentos as $depto)
                                    <tr>
                                        <td>{{ $depto->nombre }}</td>
                                        <td>
                                            @if($depto->activo ?? true)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-secondary">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#editDepto{{ $depto->id }}">
                                                <i class="bi bi-pencil text-primary"></i>
                                            </button>
                                            @if($depto->activo ?? true)
                                                <form action="{{ route('configuracion.desactivar-departamento', $depto->id) }}" method="POST" style="display: inline;">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-light" onclick="return confirm('¿Desactivar este departamento?')">
                                                        <i class="bi bi-eye-slash text-warning"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('configuracion.activar-departamento', $depto->id) }}" method="POST" style="display: inline;">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-light">
                                                        <i class="bi bi-eye text-success"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Modal Editar Departamento -->
                                    <div class="modal fade" id="editDepto{{ $depto->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title fw-bold">Editar Departamento</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('configuracion.actualizar-departamento', $depto->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-body">
                                                        <input type="text" name="nombre" class="form-control" value="{{ $depto->nombre }}" required>
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary" style="background-color: #003366;">Guardar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No hay departamentos</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: MATRIZ DE HOMOLOGACIÓN -->
        <div class="tab-pane fade" id="matriz" role="tabpanel">
            <div class="row">
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm p-4">
                        <h6 class="fw-bold mb-3">Agregar a Matriz</h6>
                        <form action="{{ route('configuracion.agregar-matriz') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small">Departamento</label>
                                <select name="departamento_id" class="form-select form-select-sm" required>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">EPP</label>
                                <select name="epp_id" class="form-select form-select-sm" required>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($epps as $epp)
                                    <option value="{{ $epp->id }}">{{ $epp->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Tipo de Requerimiento</label>
                                <select name="tipo_requerimiento" class="form-select form-select-sm" required>
                                    <option value="obligatorio">Obligatorio</option>
                                    <option value="especifico">Específico</option>
                                    <option value="opcional">Opcional</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Puesto (opcional)</label>
                                <input type="text" name="puesto" class="form-control form-control-sm" placeholder="Ej. Jefe de taller">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Taller (opcional)</label>
                                <input type="text" name="taller" class="form-control form-control-sm" placeholder="Ej. Laboratorio de Sistemas">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-sm" style="background-color: #003366;">
                                <i class="bi bi-plus me-2"></i>Agregar a Matriz
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm p-4">
                        <h6 class="fw-bold mb-3">Matriz de Homologación</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Departamento</th>
                                        <th>EPP</th>
                                        <th>Tipo</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($matrizHomologacion as $matriz)
                                    <tr>
                                        <td><small>{{ $matriz->departamento->nombre ?? '-' }}</small></td>
                                        <td><small>{{ $matriz->epp->nombre ?? '-' }}</small></td>
                                        <td>
                                            <span class="badge" style="background-color: 
                                                @if($matriz->tipo_requerimiento === 'obligatorio') #dc3545
                                                @elseif($matriz->tipo_requerimiento === 'especifico') #0d6efd
                                                @else #6c757d
                                                @endif
                                            ">
                                                {{ ucfirst($matriz->tipo_requerimiento) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('configuracion.eliminar-matriz', $matriz->id) }}" method="POST" style="display: inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light" onclick="return confirm('¿Eliminar de matriz?')">
                                                    <i class="bi bi-trash text-danger"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No hay elementos en la matriz</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: NOTIFICACIONES -->
        <div class="tab-pane fade" id="notificaciones" role="tabpanel">
            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4">
                        <h5 class="fw-bold mb-4">Configuración de Notificaciones</h5>
                        <form action="{{ route('configuracion.actualizar-notificaciones') }}" method="POST">
                            @csrf
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="alertas_vencimiento" id="alertas_vencimiento" {{ $configuracion->alertas_vencimiento ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="alertas_vencimiento">
                                    <i class="bi bi-exclamation-circle me-2" style="color: #ff6b6b;"></i>Alertas por Vencimiento
                                </label>
                                <small class="d-block text-muted ms-4">Notificar cuando EPP están próximos a vencer</small>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="alertas_stock_bajo" id="alertas_stock_bajo" {{ $configuracion->alertas_stock_bajo ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="alertas_stock_bajo">
                                    <i class="bi bi-graph-down me-2" style="color: #ffa94d;"></i>Alertas por Stock Bajo
                                </label>
                                <small class="d-block text-muted ms-4">Notificar cuando stock es menor al umbral establecido</small>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="alertas_solicitudes_pendientes" id="alertas_solicitudes_pendientes" {{ $configuracion->alertas_solicitudes_pendientes ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="alertas_solicitudes_pendientes">
                                    <i class="bi bi-clock me-2" style="color: #4dabf7;"></i>Alertas por Solicitudes Pendientes
                                </label>
                                <small class="d-block text-muted ms-4">Notificar cuando hay solicitudes de EPP sin procesar</small>
                            </div>

                            <hr class="my-4">

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" style="background-color: #003366; border: none;">
                                    <i class="bi bi-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 6: AUDITORÍA -->
        <div class="tab-pane fade" id="auditoria" role="tabpanel">
            <div class="row">
                <div class="col-md-12">
                    <!-- Configuración de Auditoría -->
                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <h5 class="fw-bold mb-4">Configuración de Auditoría y Trazabilidad</h5>
                        <form action="{{ route('configuracion.actualizar-auditoria') }}" method="POST">
                            @csrf
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="auditoria_activa" id="auditoria_activa" {{ $configuracion->auditoria_activa ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="auditoria_activa">
                                    <i class="bi bi-shield-lock me-2"></i>Registro de Eventos Activo
                                </label>
                                <small class="d-block text-muted ms-4">Registrar todas las acciones del sistema</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Retención de Logs (días)</label>
                                <input type="number" name="dias_retencion_logs" class="form-control" value="{{ $configuracion->dias_retencion_logs }}" required min="30" max="1825">
                                <small class="text-muted">Tiempo de almacenamiento de registros de auditoría</small>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" style="background-color: #003366; border: none;">
                                    <i class="bi bi-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Visor de Logs -->
                    <div class="card border-0 shadow-sm p-4">
                        <h6 class="fw-bold mb-3">Últimos Eventos Registrados</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Evento</th>
                                        <th>Modelo</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                    <tr>
                                        <td><small>{{ $log->created_at->format('d/m/Y H:i') }}</small></td>
                                        <td><small>{{ $log->usuario->name ?? 'Sistema' }}</small></td>
                                        <td><small><span class="badge bg-info">{{ $log->evento }}</span></small></td>
                                        <td><small>{{ $log->modelo }}</small></td>
                                        <td><small class="text-muted">{{ Str::limit($log->descripcion, 50) }}</small></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No hay eventos registrados</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .nav-tabs .nav-link {
        border: none;
        color: #666;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link:hover {
        border-bottom: 3px solid #003366;
        color: #003366;
    }
    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #003366;
        background: none;
        color: #003366;
    }
</style>
@endsection
