@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Mi Panel de EPP</h2>
            <p class="text-muted">Bienvenido, {{ Auth::user()->name }}</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-dark shadow-sm">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </button>
        </form>
    </div>

    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center p-4 mb-4" style="background-color: #fff5f5; border-radius: 15px;">
        <i class="bi bi-exclamation-triangle-fill fs-2 text-danger me-3"></i>
        <div>
            <h5 class="fw-bold mb-0 text-danger">Tienes 3 EPP vencido(s)</h5>
            <p class="mb-0 text-muted">Requieren renovación urgente</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-start">
                        <p class="text-muted small mb-1">Total EPP Asignados</p>
                        <h3 class="fw-bold">3</h3>
                    </div>
                    <span class="badge bg-primary-subtle p-2 rounded-circle">
                        <i class="bi bi-check-lg text-primary fs-4"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-start">
                        <p class="text-muted small mb-1">Próximos a Vencer</p>
                        <h3 class="fw-bold text-warning">0</h3>
                    </div>
                    <span class="badge bg-warning-subtle p-2 rounded-circle">
                        <i class="bi bi-clock text-warning fs-4"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-start">
                        <p class="text-muted small mb-1">EPP Vencidos</p>
                        <h3 class="fw-bold text-danger">3</h3>
                    </div>
                    <span class="badge bg-danger-subtle p-2 rounded-circle">
                        <i class="bi bi-exclamation-circle text-danger fs-4"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-start">
                        <p class="text-muted small mb-1">Solicitudes Pendientes</p>
                        <h3 class="fw-bold text-warning">0</h3>
                    </div>
                    <span class="badge bg-warning-subtle p-2 rounded-circle">
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h4 class="fw-bold mb-4">Acciones Rápidas</h4>
                <div class="d-grid gap-3">
                    <a href="{{ route('epps.catalogo') }}" class="btn btn-primary py-3 fw-bold" style="background-color: #003366; border: none; border-radius: 12px;">
                        Ver Catálogo de EPP
                    </a>
                    <a href="#" class="btn btn-outline-dark py-3 fw-bold" style="border-radius: 12px;">
                        Mis EPP Asignados
                    </a>
                    <a href="#" class="btn btn-outline-dark py-3 fw-bold" style="border-radius: 12px;">
                        Ver Mis Solicitudes
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h4 class="fw-bold mb-4">Información</h4>
                <div class="mb-4">
                    <p class="mb-1 text-muted">Email: <span class="text-dark fw-bold">{{ Auth::user()->email }}</span></p>
                    <p class="mb-1 text-muted">Rol: <span class="text-dark fw-bold">Juan Docente</span></p>
                </div>
                <p class="text-muted">
                    Las solicitudes de EPP serán revisadas por el coordinador o administrador. Recibirás una notificación con el resultado.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection