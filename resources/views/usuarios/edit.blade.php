@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Editar Usuario</h2>
            <p class="text-muted">Modifica los datos de {{ $usuario->name }}</p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <div class="card-body">
                    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre Completo</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $usuario->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Correo Institucional</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Rol</label>
                                <select name="role" class="form-select" required>
                                    <option value="Admin" @if($usuario->role === 'Admin') selected @endif>Admin</option>
                                    <option value="Coordinador" @if($usuario->role === 'Coordinador') selected @endif>Coordinador</option>
                                    <option value="Docente" @if($usuario->role === 'Docente') selected @endif>Docente</option>
                                    <option value="Usuario" @if($usuario->role === 'Usuario') selected @endif>Usuario</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Departamento</label>
                                <input type="text" name="department" class="form-control" value="{{ old('department', $usuario->department) }}" placeholder="Ej. Operaciones">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contraseña (dejar en blanco para no cambiar)</label>
                            <input type="password" name="password" class="form-control" placeholder="Nueva contraseña (opcional)">
                            <small class="text-muted">Si no completas este campo, la contraseña actual no será modificada.</small>
                        </div>

                        <hr class="my-4">

                        <div class="text-end">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-light me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4" style="background-color: #003366;">
                                <i class="bi bi-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control, .form-select {
        border: 1px solid #dee2e6;
    }
    .form-control:focus, .form-select:focus {
        border-color: #003366;
        box-shadow: 0 0 0 0.2rem rgba(0, 51, 102, 0.15);
    }
</style>
@endsection
