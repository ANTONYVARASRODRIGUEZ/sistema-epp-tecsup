@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Reporte de Stock Actual</h2>
            <p class="text-muted mb-0">Inventario general de EPPs al {{ date('d/m/Y') }}</p>
        </div>
        <div>
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill me-2">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <button onclick="window.print()" class="btn btn-dark rounded-pill shadow-sm">
                <i class="bi bi-printer me-2"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nombre del EPP</th>
                            <th>Categoría</th>
                            <th class="text-center">Stock Físico</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Deteriorados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($epps as $epp)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $epp->nombre }}</td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    {{ $epp->categoria->nombre ?? 'General' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $epp->stock < 10 ? 'bg-danger' : 'bg-success' }} fs-6">
                                    {{ $epp->stock }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($epp->stock == 0)
                                    <span class="text-danger fw-bold small">AGOTADO</span>
                                @elseif($epp->stock < 10)
                                    <span class="text-warning fw-bold small">CRÍTICO</span>
                                @else
                                    <span class="text-success fw-bold small">DISPONIBLE</span>
                                @endif
                            </td>
                            <td class="text-center text-muted">{{ $epp->deteriorado ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection