@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold">Talleres / Laboratorios</h2>
            <p class="text-muted">Gestión de ambientes por departamento</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="{{ route('talleres.index') }}" class="row g-2">
                <div class="col-md-4">
                    <select name="departamento_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los departamentos</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id }}" {{ ($depId == $dep->id) ? 'selected' : '' }}>{{ $dep->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
                        <i class="bi bi-plus-lg"></i> Nuevo Taller/Lab
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Departamento</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($talleres as $t)
                        <tr>
                            <td>{{ $t->nombre }}</td>
                            <td>{{ $t->departamento->nombre ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $t->activo ? 'bg-success' : 'bg-secondary' }}">{{ $t->activo ? 'Activo' : 'Inactivo' }}</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $t->id }}">Editar</button>
                                <form action="{{ route('talleres.toggle', $t) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-warning">{{ $t->activo ? 'Desactivar' : 'Activar' }}</button>
                                </form>
                                <form action="{{ route('talleres.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este taller/lab?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Editar -->
                        <div class="modal fade" id="modalEditar{{ $t->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Taller/Lab</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="{{ route('talleres.update', $t) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nombre</label>
                                                <input type="text" name="nombre" class="form-control" value="{{ $t->nombre }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Departamento</label>
                                                <select name="departamento_id" class="form-select" required>
                                                    @foreach($departamentos as $dep)
                                                        <option value="{{ $dep->id }}" {{ $t->departamento_id == $dep->id ? 'selected' : '' }}>{{ $dep->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                            <button class="btn btn-primary">Guardar cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Sin talleres/labs registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($talleres, 'links'))
        <div class="card-footer bg-white">
            {{ $talleres->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Taller/Lab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('talleres.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Departamento</label>
                        <select name="departamento_id" class="form-select" required>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
