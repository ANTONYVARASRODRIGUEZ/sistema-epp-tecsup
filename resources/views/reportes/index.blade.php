@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark">Reportes y Consultas</h2>
        <p class="text-muted">Seleccione el tipo de reporte que desea generar.</p>
    </div>

    <div class="row g-4">
        <!-- Tarjeta Reporte de Stock -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; transition: transform 0.3s;">
                <div class="card-body p-5 text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-box-seam text-success fs-1"></i>
                    </div>
                    <h4 class="fw-bold">Stock de Inventario</h4>
                    <p class="text-muted mb-4">Consulte la cantidad actual de todos los EPPs registrados en el almacén, incluyendo estado y categorías.</p>
                    <a href="{{ route('reportes.stock') }}" class="btn btn-success rounded-pill px-4 fw-bold">
                        Ver Reporte de Stock <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta Reporte por Departamento -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; transition: transform 0.3s;">
                <div class="card-body p-5 text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-people text-primary fs-1"></i>
                    </div>
                    <h4 class="fw-bold">Asignaciones por Área</h4>
                    <p class="text-muted mb-4">Genere listados detallados por departamento (ej. Tecnología Digital) para ver qué docentes tienen equipos asignados.</p>
                    <a href="{{ route('reportes.departamento') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                        Ver Reporte por Área <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta Reporte de Incidencias -->
        <div class="col-md-6 mt-4 mt-md-0">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; transition: transform 0.3s;">
                <div class="card-body p-5 text-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    </div>
                    <h4 class="fw-bold">Incidencias y Bajas</h4>
                    <p class="text-muted mb-4">Consulte el listado de todos los EPPs que han sido reportados como dañados, perdidos o dados de baja.</p>
                    <a href="{{ route('reportes.incidencias') }}" class="btn btn-danger rounded-pill px-4 fw-bold">
                        Ver Reporte de Incidencias <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection