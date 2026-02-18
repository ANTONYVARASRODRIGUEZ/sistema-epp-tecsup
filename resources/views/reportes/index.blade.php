@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark">Reportes y Consultas</h2>
        <p class="text-muted">Seleccione el tipo de reporte que desea generar.</p>
    </div>

    <div class="row g-4">
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

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; transition: transform 0.3s;">
                <div class="card-body p-5 text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-people text-primary fs-1"></i>
                    </div>
                    <h4 class="fw-bold">Asignaciones por Área</h4>
                    <p class="text-muted mb-4">Genere listados detallados por departamento para ver qué docentes tienen equipos asignados.</p>
                    <a href="{{ route('reportes.departamento') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                        Ver Reporte por Área <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
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

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; transition: transform 0.3s; background: linear-gradient(145deg, #ffffff, #f0f7ff);">
                <div class="card-body p-5 text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-calendar-check text-info fs-1"></i>
                    </div>
                    <h4 class="fw-bold">Planificación de Vida Útil</h4>
                    <span class="badge bg-info text-dark mb-2">Cronograma a Largo Plazo</span>
                    <p class="text-muted mb-4">Proyecte los vencimientos por año y mes. Ideal para planificar compras y renovaciones futuras (2026-2030).</p>
                    <a href="{{ route('reportes.vida_util') }}" class="btn btn-info text-white rounded-pill px-4 fw-bold">
                        Ver Cronograma Futuro <i class="bi bi-calendar3 ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection