@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Detalles del Usuario</h2>
            <p class="text-muted">Información completa de {{ $usuario->name }}</p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="text-muted small text-uppercase mb-2">Nombre</h5>
                        <p class="fw-bold fs-5">{{ $usuario->name }}</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-muted small text-uppercase mb-2">DNI/Código</h5>
                        <p class="fw-bold">{{ $usuario->dni ?? '-' }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted small text-uppercase mb-2">Correo Electrónico</h5>
                            <p class="fw-bold">{{ $usuario->email }}</p>
                        </div>

                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted small text-uppercase mb-2">Rol</h5>
                            <p>
                                @php
                                    $badgeColors = [
                                        'Admin' => ['bg' => '#e2e8f0', 'text' => '#475569'],
                                        'Coordinador' => ['bg' => '#f0fdf4', 'text' => '#166534'],
                                        'Docente' => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                                        'Usuario' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                    ];
                                    $colors = $badgeColors[$usuario->role] ?? ['bg' => '#e5e7eb', 'text' => '#374151'];
                                @endphp
                                <span class="badge-role" style="background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; padding: 6px 12px; border-radius: 20px;">
                                    {{ $usuario->role }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted small text-uppercase mb-2">Departamento</h5>
                            <p class="fw-bold">{{ $usuario->department ?? 'No asignado' }}</p>
                        </div>

                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted small text-uppercase mb-2">Taller/Laboratorio</h5>
                            <p class="fw-bold">{{ $usuario->workshop ?? 'No asignado' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted small text-uppercase mb-2">Fecha de Registro</h5>
                            <p class="fw-bold">{{ $usuario->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted small text-uppercase mb-2">Última Actualización</h5>
                            <p class="fw-bold">{{ $usuario->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="text-end">
                        <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning me-2">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-1"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0">
                    ¿Estás seguro de que deseas eliminar al usuario <strong>"{{ $usuario->name }}"</strong>?
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

<style>
    .badge-role {
        display: inline-block;
        font-weight: 500;
        font-size: 0.9rem;
    }
</style>
@endsection
