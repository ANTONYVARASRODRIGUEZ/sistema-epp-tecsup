@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000;">Gestión de Departamentos</h1>
            <p class="text-muted">Crear y administrar departamentos y talleres</p>
        </div>
        <button type="button" 
        class="btn btn-primary px-4 py-2 d-flex align-items-center" 
        style="background-color: #003366; border-radius: 8px;"
        data-bs-toggle="modal" 
        data-bs-target="#modalNuevoDepartamento">
    <i class="bi bi-plus-lg me-2"></i> Nuevo Departamento
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
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Talleres</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departamentos as $depto)
                        <tr>
                            <td class="fw-semibold">{{ $depto->nombre }}</td>
                            <td>
                                @php
                                    $codigo = $depto->codigo ?? strtoupper(substr($depto->nombre, 0, 3));
                                @endphp
                                {{ $codigo }}
                            </td>
                            <td>{{ $depto->descripcion ?? '-' }}</td>
                            <td>
                                @if($depto->talleres)
                                    @php
                                        $tallerArray = array_map('trim', explode(',', $depto->talleres));
                                    @endphp
                                    @foreach($tallerArray as $taller)
                                        <span class="badge" style="background-color: #cfe2ff; color: #084298; margin-right: 5px;">{{ $taller }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background-color: #d1e7dd; color: #0f5132;">{{ $depto->activo ? 'activo' : 'inactivo' }}</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-action p-1" title="Ver" data-bs-toggle="modal" data-bs-target="#modalVerDepto{{ $depto->id }}"><i class="bi bi-eye"></i></button>
                                <button type="button" class="btn btn-action p-1" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarDepto{{ $depto->id }}"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-action btn-delete p-1" title="Eliminar" data-bs-toggle="modal" data-bs-target="#deleteDeptoModal{{ $depto->id }}"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                <p class="mt-2">No hay departamentos registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Departamento -->
<div class="modal fade" id="modalNuevoDepartamento" tabindex="-1" aria-labelledby="modalNuevoDepartamentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalNuevoDepartamentoLabel">Crear Nuevo Departamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('departamentos.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del Departamento</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Operaciones" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Código</label>
                        <input type="text" name="codigo" class="form-control" placeholder="Ej. OPS" maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" placeholder="Descripción del departamento" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Talleres (separados por coma)</label>
                        <textarea name="talleres" class="form-control" placeholder="Ej. Taller 1, Taller 2, Taller 3" rows="2"></textarea>
                        <small class="text-muted">Ingresa los talleres separados por coma</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="activo" class="form-select">
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: #003366;">Guardar Departamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales de Ver/Editar Departamentos -->
@foreach($departamentos as $depto)

<!-- Modal Ver Departamento -->
<div class="modal fade" id="modalVerDepto{{ $depto->id }}" tabindex="-1" aria-labelledby="modalVerDeptoLabel{{ $depto->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalVerDeptoLabel{{ $depto->id }}">{{ $depto->nombre }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <p><strong>Código:</strong> {{ $depto->codigo ?? 'No especificado' }}</p>
                <p><strong>Descripción:</strong> {{ $depto->descripcion ?? 'No especificada' }}</p>
                <p><strong>Talleres:</strong> 
                    @if($depto->talleres)
                        @php
                            $tallerArray = array_map('trim', explode(',', $depto->talleres));
                        @endphp
                        @foreach($tallerArray as $taller)
                            <span class="badge" style="background-color: #cfe2ff; color: #084298; margin-right: 5px;">{{ $taller }}</span>
                        @endforeach
                    @else
                        No especificados
                    @endif
                </p>
                <p><strong>Estado:</strong> 
                    <span class="badge" style="background-color: #d1e7dd; color: #0f5132;">{{ $depto->activo ? 'activo' : 'inactivo' }}</span>
                </p>
                <p><strong>Fecha de Creación:</strong> {{ $depto->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Departamento -->
<div class="modal fade" id="modalEditarDepto{{ $depto->id }}" tabindex="-1" aria-labelledby="modalEditarDeptoLabel{{ $depto->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalEditarDeptoLabel{{ $depto->id }}">Editar Departamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('departamentos.update', $depto->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del Departamento</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $depto->nombre }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Código</label>
                        <input type="text" name="codigo" class="form-control" value="{{ $depto->codigo }}" maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ $depto->descripcion }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Talleres (separados por coma)</label>
                        <textarea name="talleres" class="form-control" rows="2">{{ $depto->talleres }}</textarea>
                        <small class="text-muted">Ingresa los talleres separados por coma</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="activo" class="form-select">
                            <option value="1" {{ $depto->activo ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ !$depto->activo ? 'selected' : '' }}>Inactivo</option>
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

<!-- Modal Eliminar Departamento -->
<div class="modal fade" id="deleteDeptoModal{{ $depto->id }}" tabindex="-1" aria-labelledby="deleteDeptoLabel{{ $depto->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title fw-bold text-danger" id="deleteDeptoLabel{{ $depto->id }}">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0">
                    ¿Estás seguro de que deseas eliminar el departamento <strong>"{{ $depto->nombre }}"</strong>?
                </p>
                <p class="text-muted small mt-2 mb-0">
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
                <form action="{{ route('departamentos.destroy', $depto->id) }}" method="POST" class="d-inline">
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
