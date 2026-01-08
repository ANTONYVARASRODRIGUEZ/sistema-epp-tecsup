@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Panel Administrativo</h2>
        <span class="text-muted">Bienvenido Admin Centro</span>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-primary border-4 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Stock Disponible</p>
                        <h3 class="fw-bold mb-0">{{ $epps->sum('stock') ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-box fs-1 text-primary opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-success border-4 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">EPP Entregados</p>
                        <h3 class="fw-bold mb-0">100</h3> </div>
                    <i class="bi bi-truck fs-1 text-success opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-dark border-4 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Stock Bajo</p>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <i class="bi bi-exclamation-circle fs-1 text-dark opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-danger border-4 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Deteriorados</p>
                        <h3 class="fw-bold mb-0">5</h3>
                    </div>
                    <i class="bi bi-trash fs-1 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4">
        <h4 class="fw-bold mb-4">Gestión de Inventario</h4>
        
        <div class="d-flex justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar por nombre o código...">
            </div>
            <a href="{{ route('epps.create') }}" class="btn btn-primary d-flex align-items-center" style="background-color: #003366;">
                <i class="bi bi-plus fs-4 me-1"></i> Nuevo
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        @if($epps->isEmpty())
            <div class="alert alert-warning border-0 shadow-sm">No hay EPP registrados aún</div>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="bg-light">
                        <tr class="text-muted small">
                            <th>EPP</th>
                            <th>Código</th>
                            <th>Stock</th>
                            <th>Vida Útil</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($epps as $epp)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $epp->nombre }}</div>
                                <small class="text-muted">{{ $epp->tipo }}</small>
                            </td>
                            <td>CSK-00{{ $epp->id }}</td>
                            <td class="fw-bold">{{ $epp->stock ?? 0 }}</td>
                            <td>{{ $epp->vida_util_meses }} meses</td>
                            <td>
                                @if($epp->ficha_tecnica)
                                    <span class="badge bg-success-soft text-success">Disponible</span>
                                @else
                                    <span class="badge bg-secondary-soft text-secondary">Pendiente</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="#" class="btn btn-outline-secondary btn-sm border-0"><i class="bi bi-pencil"></i></a>
                                    <button class="btn btn-outline-danger btn-sm border-0"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<style>
    .border-start-4 { border-left-width: 4px !important; }
    .bg-success-soft { background-color: #e6f7ee; }
    .bg-secondary-soft { background-color: #f0f2f5; }
    .table thead th { border-bottom: none; text-transform: none; font-weight: 500; }
    .card { border-radius: 12px; }
    .btn-primary { border-radius: 8px; }
</style>
@endsection