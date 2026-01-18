@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Gestionar Acceso</h2>
            <p class="text-muted">Actualiza las credenciales de <strong>{{ $usuario->name }}</strong></p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm" style="border-radius: 15px;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                <div class="card-body">
                    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre Completo</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $usuario->name) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Correo Institucional</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Rol del Usuario</label>
                            <select name="role" class="form-select" required>
                                <option value="Admin" {{ $usuario->role === 'Admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="Coordinador" {{ $usuario->role === 'Coordinador' ? 'selected' : '' }}>Coordinador</option>
                                <option value="Docente" {{ $usuario->role === 'Docente' ? 'selected' : '' }}>Docente</option>
                            </select>
                        </div>

                        <hr class="my-4" style="opacity: 0.1;">

                        <div class="p-3 rounded-3" style="background-color: #f8f9fa; border: 1px dashed #dee2e6;">
                            <label class="form-label fw-bold text-dark mb-1">
                                <i class="bi bi-shield-lock me-2 text-primary"></i>Cambiar Contraseña
                            </label>
                            <input type="password" name="password" class="form-control mb-1" placeholder="Nueva contraseña">
                            <small class="text-muted">Dejar en blanco si el usuario mantendrá su contraseña actual.</small>
                        </div>

                        <div class="mt-4 pt-2 text-end">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background-color: #003366;">
                                <i class="bi bi-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-4 px-3">
                <div class="d-flex justify-content-between align-items-center small text-muted">
                    <span><i class="bi bi-person-badge me-1"></i> DNI: {{ $usuario->dni ?? 'No registrado' }}</span>
                    <span><i class="bi bi-building me-1"></i> Depto: {{ $usuario->departamento->nombre ?? 'Pendiente' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control, .form-select {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 0.7rem 1rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #003366;
        box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.1);
    }
</style>
@endsection