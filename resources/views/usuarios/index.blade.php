@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000;">Gestión de Usuarios</h1>
            <p class="text-muted">Crear y administrar cuentas de usuario</p>
        </div>
        <button type="button" 
        class="btn btn-primary px-4 py-2 d-flex align-items-center" 
        style="background-color: #003366; border-radius: 8px;"
        data-bs-toggle="modal" 
        data-bs-target="#modalNuevoUsuario">
    <i class="bi bi-plus-lg me-2"></i> Nuevo Usuario
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
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Departamento</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr>
                            <td class="fw-bold">{{ $usuario->name }}</td>
                            <td class="text-muted">{{ $usuario->email }}</td>
                            <td>
                                @php
                                    $role = $usuario->role ?? 'Usuario';
                                    $badgeColors = [
                                        'Admin' => ['bg' => '#e2e8f0', 'text' => '#475569'],
                                        'Coordinador' => ['bg' => '#f0fdf4', 'text' => '#166534'],
                                        'Docente' => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                                        'Usuario' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                    ];
                                    $colors = $badgeColors[$role] ?? ['bg' => '#e5e7eb', 'text' => '#374151'];
                                @endphp
                                <span class="badge-role" style="background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }};">{{ $role }}</span>
                            </td>
                            <td>{{ $usuario->department ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('usuarios.show', $usuario->id) }}" class="btn btn-action p-1" title="Ver detalles"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-action p-1" title="Editar"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-action btn-delete p-1" title="Eliminar" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $usuario->id }}"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                <p class="mt-2">No hay usuarios registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalNuevoUsuarioLabel">Registrar Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre Completo</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej. Juan Pérez" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo Institucional</label>
                        <input type="email" name="email" class="form-control" placeholder="usuario@tecsup.edu.pe" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Rol</label>
                            <select name="role" class="form-select" required>
                                <option value="Admin">Admin</option>
                                <option value="Coordinador">Coordinador</option>
                                <option value="Docente">Docente</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Departamento</label>
                            <input type="text" name="department" class="form-control" placeholder="Ej. Operaciones">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña Temporal</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: #003366;">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales de Eliminación -->
@foreach($usuarios as $usuario)
<div class="modal fade" id="deleteModal{{ $usuario->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $usuario->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel{{ $usuario->id }}">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0">
                    ¿Estás seguro de que deseas eliminar el usuario <strong>"{{ $usuario->name }}"</strong> ({{ $usuario->email }})?
                </p>
                <p class="text-muted small mt-2 mb-0">
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
                <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline">
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
