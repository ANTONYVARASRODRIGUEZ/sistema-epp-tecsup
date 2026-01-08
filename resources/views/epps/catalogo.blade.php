@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Catálogo de EPP</h2>
            <p class="text-muted">Matriz de homologación y especificaciones técnicas</p>
        </div>
        <a href="{{ route('epps.create') }}" class="btn btn-primary d-flex align-items-center shadow-sm" style="background-color: #003366; border: none;">
            <i class="bi bi-plus fs-4 me-1"></i> Nuevo EPP
        </a>
    </div>

    <div class="row">
        @forelse($epps as $epp)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; overflow: hidden;">
                <div style="height: 220px; overflow: hidden;">
                    <img src="{{ asset('storage/' . $epp->imagen) }}" 
                         class="card-img-top w-100 h-100" 
                         style="object-fit: cover;" 
                         onerror="this.src='https://via.placeholder.com/400x250?text=EPP+Imagen'">
                </div>
                
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold mb-0">{{ $epp->nombre }}</h5>
                        <div>
                            <a href="{{ route('epps.edit', $epp->id) }}" class="text-primary me-2"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('epps.destroy', $epp->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="border-0 bg-transparent text-danger p-0" onclick="return confirm('¿Eliminar este EPP?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <p class="text-muted small mb-4">Categoría: {{ $epp->tipo }}</p>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="text-muted small d-block">Marca</label>
                            <span class="fw-bold">{{ $epp->marca ?? 'Genérico' }}</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Código</label>
                            <span class="fw-bold text-uppercase">{{ substr($epp->nombre, 0, 3) }}-{{ str_pad($epp->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Precio</label>
                            <span class="fw-bold text-dark">${{ number_format($epp->precio ?? 0, 2) }}</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Renovación</label>
                            <span class="fw-bold">{{ $epp->vida_util_meses * 30 }} días</span>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="text-muted small d-block mb-1">Normas/Certificaciones</label>
                        <span class="badge bg-light text-dark border p-2 w-100 text-start fw-normal shadow-sm">
                            <i class="bi bi-check-circle-fill text-success me-1"></i> ANSI Z89.1, ISO 3873
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-box-seam display-1 text-muted"></i>
            <p class="mt-3 text-muted">No hay equipos registrados en el catálogo.</p>
        </div>
        @endforelse
    </div>
</div>

<style>
    .card { transition: transform 0.3s ease, shadow 0.3s ease; }
    .card:hover { transform: translateY(-8px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .badge { font-size: 0.85rem; color: #555 !important; border-radius: 8px; }
</style>
@endsection