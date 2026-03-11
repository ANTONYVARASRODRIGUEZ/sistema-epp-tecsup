@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Header con Kpis Rápidos --}}
    <div class="row mb-4">
        <div class="col-12 col-md-8">
            <h3 class="fw-bold text-dark mb-1">Gestión de Existencias</h3>
            <p class="text-muted">Control centralizado de stock para equipos de protección.</p>
        </div>
        <div class="col-12 col-md-4 text-md-end">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Buscar por código o nombre...">
            </div>
        </div>
    </div>

    {{-- Tarjetas de Resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between px-2">
                        <div>
                            <p class="mb-0 opacity-75 small fw-bold">TOTAL EPPs</p>
                            <h3 class="mb-0 fw-bold">{{ $epps->count() }}</h3>
                        </div>
                        <i class="bi bi-box-seam fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between px-2">
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Stock Crítico</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ $epps->where('stock', '<=', 5)->count() }}</h3>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between px-2">
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Sin Stock</p>
                            <h3 class="mb-0 fw-bold text-danger">{{ $epps->where('stock', '<=', 0)->count() }}</h3>
                        </div>
                        <i class="bi bi-x-octagon fs-1 text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla Profesional --}}
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                <thead class="bg-dark text-white text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                    <tr>
                        <th class="ps-4 py-3">Código Logístico</th>
                        <th>Descripción del EPP</th>
                        <th class="text-center">Existencias</th>
                        <th class="text-center">Nivel</th>
                        <th class="text-end pe-4">Gestión</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($epps as $epp)
                    <tr class="border-bottom">
                        <td class="ps-4">
                            <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                {{ $epp->codigo_logistica ?? '---' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="ms-1">
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $epp->nombre }}</div>
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">{{ $epp->marca_modelo ?? 'Estándar' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold fs-5 {{ $epp->stock <= 5 ? 'text-danger' : 'text-dark' }}">
                                {{ $epp->stock ?? 0 }}
                            </span>
                            <small class="text-muted small">uds</small>
                        </td>
                        <td class="text-center">
                            @if($epp->stock <= 0)
                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill small">Agotado</span>
                            @elseif($epp->stock <= 5)
                                <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill small">Crítico</span>
                            @else
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill small">Óptimo</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-dark rounded-3 px-3 shadow-sm border-2 fw-bold" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editStock{{ $epp->id }}">
                                <i class="bi bi-plus-slash-minus me-1"></i> Ajustar
                            </button>
                        </td>
                    </tr>

                    {{-- Modal Estilizado --}}
                    <div class="modal fade" id="editStock{{ $epp->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-dark text-white border-0">
                                    <h6 class="modal-title fw-bold">Ajuste de Stock</h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('inventario.update', $epp->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body py-4 text-center">
                                        <p class="small text-muted mb-3">{{ $epp->nombre }}</p>
                                        <div class="d-flex justify-content-center align-items-center gap-3">
                                            <input type="number" name="stock" class="form-control form-control-lg text-center fw-bold border-2" 
                                                   value="{{ $epp->stock }}" min="0" style="font-size: 1.5rem; color: #003366;">
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-3 pt-0">
                                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">Actualizar Unidades</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted fst-italic">
                            No se encontraron registros en el inventario.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-dark { background-color: #1a1a1a !important; }
    .bg-primary { background-color: #003366 !important; }
    .bg-success-subtle { background-color: #d1e7dd; }
    .bg-danger-subtle { background-color: #f8d7da; }
    .bg-warning-subtle { background-color: #fff3cd; }
    .table-hover tbody tr:hover { background-color: #f8f9ff !important; transition: 0.2s; }
</style>
@endsection