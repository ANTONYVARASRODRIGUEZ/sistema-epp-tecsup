@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000;">Matriz de EPP</h1>
            <p class="text-muted">Define EPP obligatorio y específico por puesto</p>
        </div>
        <button type="button" 
        class="btn btn-primary px-4 py-2 d-flex align-items-center" 
        style="background-color: #003366; border-radius: 8px;"
        data-bs-toggle="modal" 
        data-bs-target="#modalNuevaMatriz">
    <i class="bi bi-plus-lg me-2"></i> Nueva Matriz
</button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <div class="card card-custom bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Departamento</th>
                            <th>Tipo de Puesto</th>
                            <th>Taller</th>
                            <th>EPP Obligatorio</th>
                            <th>EPP Específico</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matrizEpp as $matriz)
                        <tr>
                            <td class="fw-semibold">{{ $matriz->departamento->nombre ?? '-' }}</td>
                            <td>{{ $matriz->puesto ?? '-' }}</td>
                            <td>{{ $matriz->taller ?? '-' }}</td>
                            <td>
                                @if($matriz->tipo_requerimiento === 'obligatorio')
                                    <span class="badge" style="background-color: #d1e7dd; color: #0f5132;">{{ $matriz->epp->nombre ?? '-' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($matriz->tipo_requerimiento === 'especifico')
                                    <span class="badge" style="background-color: #cfe2ff; color: #084298;">{{ $matriz->epp->nombre ?? '-' }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background-color: #d1e7dd; color: #0f5132;">{{ $matriz->activo ? 'activo' : 'inactivo' }}</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-action p-1" title="Ver" data-bs-toggle="modal" data-bs-target="#modalVerMatriz{{ $matriz->id }}"><i class="bi bi-eye"></i></button>
                                <button type="button" class="btn btn-action p-1" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarMatriz{{ $matriz->id }}"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-action btn-delete p-1" title="Eliminar" data-bs-toggle="modal" data-bs-target="#deleteMatrizModal{{ $matriz->id }}"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                <p class="mt-2">No hay matrices EPP registradas</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Matriz EPP -->
<div class="modal fade" id="modalNuevaMatriz" tabindex="-1" aria-labelledby="modalNuevaMatrizLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalNuevaMatrizLabel">Crear Nueva Matriz EPP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('matriz-epp.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Departamento</label>
                            <select name="departamento" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->nombre }}">{{ $depto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tipo de Puesto</label>
                            <input type="text" name="tipo_puesto" class="form-control" placeholder="Ej. Instructor" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Taller</label>
                        <input type="text" name="taller" class="form-control" placeholder="Ej. Taller 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">EPP Obligatorio</label>
                        @forelse($epps as $epp)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="epp_obligatorio[]" value="{{ $epp->nombre }}" id="epp_obligatorio_{{ $epp->id }}">
                            <label class="form-check-label" for="epp_obligatorio_{{ $epp->id }}">{{ $epp->nombre }}</label>
                        </div>
                        @empty
                        <p class="text-muted small">No hay EPPs disponibles</p>
                        @endforelse
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">EPP Específico</label>
                        @forelse($epps as $epp)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="epp_especifico[]" value="{{ $epp->nombre }}" id="epp_especifico_{{ $epp->id }}">
                            <label class="form-check-label" for="epp_especifico_{{ $epp->id }}">{{ $epp->nombre }}</label>
                        </div>
                        @empty
                        <p class="text-muted small">No hay EPPs disponibles</p>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: #003366;">Guardar Matriz</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales de Ver/Editar/Eliminar Matrices -->
@foreach($matrizEpp as $matriz)

<!-- Modal Ver Matriz -->
<div class="modal fade" id="modalVerMatriz{{ $matriz->id }}" tabindex="-1" aria-labelledby="modalVerMatrizLabel{{ $matriz->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalVerMatrizLabel{{ $matriz->id }}">Detalles de la Matriz EPP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <p><strong>Departamento:</strong> {{ $matriz->departamento->nombre ?? '-' }}</p>
                <p><strong>Tipo de Puesto:</strong> {{ $matriz->puesto ?? '-' }}</p>
                <p><strong>Taller:</strong> {{ $matriz->taller ?? '-' }}</p>
                <p><strong>EPP:</strong> 
                    <span class="badge" style="background-color: {{ $matriz->tipo_requerimiento === 'obligatorio' ? '#d1e7dd' : '#cfe2ff' }}; color: {{ $matriz->tipo_requerimiento === 'obligatorio' ? '#0f5132' : '#084298' }};">
                        {{ $matriz->epp->nombre ?? '-' }}
                    </span>
                </p>
                <p><strong>Tipo de Requerimiento:</strong> 
                    <span class="badge" style="background-color: {{ $matriz->tipo_requerimiento === 'obligatorio' ? '#d1e7dd' : '#cfe2ff' }}; color: {{ $matriz->tipo_requerimiento === 'obligatorio' ? '#0f5132' : '#084298' }};">
                        {{ ucfirst($matriz->tipo_requerimiento) }}
                    </span>
                </p>
                <p><strong>Estado:</strong> 
                    <span class="badge" style="background-color: #d1e7dd; color: #0f5132;">{{ $matriz->activo ? 'activo' : 'inactivo' }}</span>
                </p>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Matriz -->
<div class="modal fade" id="modalEditarMatriz{{ $matriz->id }}" tabindex="-1" aria-labelledby="modalEditarMatrizLabel{{ $matriz->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalEditarMatrizLabel{{ $matriz->id }}">Editar Matriz EPP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('matriz-epp.update', $matriz->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Departamento</label>
                        <select name="departamento_id" class="form-select" required>
                            @foreach($departamentos as $depto)
                                <option value="{{ $depto->id }}" {{ $matriz->departamento_id == $depto->id ? 'selected' : '' }}>{{ $depto->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">EPP</label>
                        <select name="epp_id" class="form-select" required>
                            @foreach($epps as $epp)
                                <option value="{{ $epp->id }}" {{ $matriz->epp_id == $epp->id ? 'selected' : '' }}>{{ $epp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de Puesto</label>
                        <input type="text" name="puesto" class="form-control" value="{{ $matriz->puesto }}" placeholder="Ej. Instructor">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Taller</label>
                        <input type="text" name="taller" class="form-control" value="{{ $matriz->taller }}" placeholder="Ej. Taller 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de Requerimiento</label>
                        <select name="tipo_requerimiento" class="form-select" required>
                            <option value="obligatorio" {{ $matriz->tipo_requerimiento == 'obligatorio' ? 'selected' : '' }}>Obligatorio</option>
                            <option value="especifico" {{ $matriz->tipo_requerimiento == 'especifico' ? 'selected' : '' }}>Específico</option>
                            <option value="opcional" {{ $matriz->tipo_requerimiento == 'opcional' ? 'selected' : '' }}>Opcional</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: #003366;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar Matriz -->
<div class="modal fade" id="deleteMatrizModal{{ $matriz->id }}" tabindex="-1" aria-labelledby="deleteMatrizLabel{{ $matriz->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title fw-bold text-danger" id="deleteMatrizLabel{{ $matriz->id }}">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0">
                    ¿Estás seguro de que deseas eliminar esta matriz EPP?
                </p>
                <p class="text-muted small mt-2 mb-0">
                    <strong>{{ $matriz->departamento->nombre ?? '-' }}</strong> - 
                    <strong>{{ $matriz->epp->nombre ?? '-' }}</strong> ({{ ucfirst($matriz->tipo_requerimiento) }})
                </p>
                <p class="text-muted small mt-1 mb-0">
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
                <form action="{{ route('matriz-epp.destroy', $matriz->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endforeach

@endsection
