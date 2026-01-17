@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Mi Perfil</h2>
        <p class="text-muted mb-0">Información personal y configuración</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; max-width: 720px;">
        <div class="card-body p-4">
            <form>
                @csrf
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Nombre Completo</label>
                    <input type="text" class="form-control bg-light" value="{{ $usuario->name }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Email</label>
                    <input type="email" class="form-control bg-light" value="{{ $usuario->email }}" readonly>
                    <small class="text-muted">El email no puede ser modificado</small>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Departamento</label>
                    <input type="text" class="form-control bg-light" value="{{ $usuario->department ?? 'Sin asignar' }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Teléfono</label>
                    <input type="text" class="form-control bg-light" value="{{ $usuario->telefono ?? '' }}" placeholder="Ingresa un teléfono" readonly>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('docente.dashboard') }}" class="btn btn-primary px-4 fw-bold" style="background-color: #003da5; border-color: #003da5;">
                        Volver al panel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
