@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Mi Panel de EPP</h2>
            <p class="text-muted">Bienvenido, {{ $user->name }}</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-dark shadow-sm">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </button>
        </form>
    </div>

    @if($vencidos > 0)
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center p-4 mb-4" style="background-color: #fff5f5; border-radius: 15px;">
            <i class="bi bi-exclamation-triangle-fill fs-2 text-danger me-3"></i>
            <div>
                <h5 class="fw-bold mb-0 text-danger">Tienes {{ $vencidos }} EPP vencido(s)</h5>
                <p class="mb-0 text-muted">Renueva tus equipos para mantenerte protegido.</p>
            </div>
        </div>
    @elseif($proximosAVencer > 0)
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center p-4 mb-4" style="background-color: #fffaf0; border-radius: 15px;">
            <i class="bi bi-exclamation-circle-fill fs-2 text-warning me-3"></i>
            <div>
                <h5 class="fw-bold mb-0 text-warning">Tienes {{ $proximosAVencer }} EPP por vencer</h5>
                <p class="mb-0 text-muted">Gestiona una renovación antes de la fecha límite.</p>
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-start">
                        <p class="text-muted small mb-1">Total EPP Asignados</p>
                        <h3 class="fw-bold">{{ $totalAsignados }}</h3>
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
                        <h3 class="fw-bold text-warning">{{ $proximosAVencer }}</h3>
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
                        <h3 class="fw-bold text-danger">{{ $vencidos }}</h3>
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
                        <h3 class="fw-bold text-warning">{{ $solicitudesPendientes }}</h3>
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
                    <a href="{{ route('docente.mis-epp') }}" class="btn btn-outline-dark py-3 fw-bold" style="border-radius: 12px;">
                        Mis EPP Asignados
                    </a>
                    <a href="{{ route('docente.mis-solicitudes') }}" class="btn btn-outline-dark py-3 fw-bold" style="border-radius: 12px;">
                        Ver Mis Solicitudes
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h4 class="fw-bold mb-4">Información</h4>
                <div class="mb-4">
                    <p class="mb-1 text-muted">Email: <span class="text-dark fw-bold">{{ $user->email }}</span></p>
                    <p class="mb-1 text-muted">Rol: <span class="text-dark fw-bold">{{ $user->role }}</span></p>
                </div>
                @if($ultimosEpp->isNotEmpty())
                    <h6 class="fw-bold mb-3">Últimos EPP asignados</h6>
                    <ul class="list-unstyled mb-0">
                        @foreach($ultimosEpp as $solicitud)
                            <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <span class="fw-semibold">{{ $solicitud->epp->nombre ?? 'Equipo' }}</span>
                                    <p class="small text-muted mb-0">Entregado el {{ optional($solicitud->fecha_aprobacion)->format('d/m/Y') }}</p>
                                </div>
                                <span class="badge {{ optional($solicitud->fecha_vencimiento)?->isPast() ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                                    {{ optional($solicitud->fecha_vencimiento)?->isPast() ? 'Vencido' : 'Vigente' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">
                        Aún no tienes equipos asignados. Solicita uno desde el catálogo.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection