@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Catálogo de EPP</h2>
            <p class="text-muted">Matriz de homologación y especificaciones técnicas</p>
        </div>
        <a href="#" class="btn btn-primary d-flex align-items-center shadow-sm" style="background-color: #003366; border: none;" data-bs-toggle="modal" data-bs-target="#modalNuevoEpp">
            <i class="bi bi-plus fs-4 me-1"></i> Nuevo EPP
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        @forelse($epps as $epp)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <!-- Imagen -->
                <div class="epp-image-container" style="height: 240px; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); display: flex; align-items: center; justify-content: center;">
                    @if($epp->imagen)
                        <img src="{{ asset('storage/' . $epp->imagen) }}" 
                             class="epp-image" 
                             onerror="this.src='https://via.placeholder.com/300x200?text=Sin+imagen'">
                    @else
                        <div class="text-center">
                            <i class="bi bi-box-seam" style="font-size: 4rem; color: #ddd;"></i>
                            <p class="text-muted mt-2" style="font-size: 0.85rem;">Sin imagen</p>
                        </div>
                    @endif
                </div>
                
                <div class="card-body" style="padding: 16px;">
                    <!-- Encabezado con nombre y acciones -->
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-2" style="border-bottom: 2px solid #003366;">
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size: 1.1rem; color: #333;">{{ Str::ucfirst($epp->nombre) }}</h6>
                            <small class="text-muted">{{ $epp->tipo }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('epps.show', $epp->id) }}" class="btn btn-sm btn-light" title="Ver detalles" style="padding: 4px 8px;">
                                <i class="bi bi-eye text-info"></i>
                            </a>
                            <a href="{{ route('epps.edit', $epp->id) }}" class="btn btn-sm btn-light" title="Editar" style="padding: 4px 8px;">
                                <i class="bi bi-pencil text-primary"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-light" title="Eliminar" style="padding: 4px 8px;" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $epp->id }}">
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Especificaciones principales -->
                    <div class="specification-group mb-3">
                        <div class="spec-item">
                            <span class="spec-label">Marca:</span>
                            <span class="spec-value">{{ $epp->marca_modelo ?? '—' }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Código:</span>
                            <span class="spec-value text-uppercase" style="color: #003366; font-weight: bold;">{{ $epp->codigo_logistica ?? '—' }}</span>
                        </div>
                    </div>

                    <!-- Precio y Cantidad -->
                    <div style="background: #f0f7ff; padding: 10px 12px; border-radius: 8px; margin-bottom: 12px;">
                        <div class="row g-0">
                            <div class="col-6">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Precio</small>
                                <span class="text-success fw-bold" style="font-size: 1.2rem;">${{ number_format($epp->precio ?? 0, 2) }}</span>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Stock</small>
                                <span style="font-size: 1.2rem; font-weight: bold;">{{ $epp->cantidad ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Información de uso -->
                    <div class="specification-group">
                        <div class="spec-item">
                            <span class="spec-label">Vida útil:</span>
                            <span class="spec-value">{{ $epp->vida_util_meses }} meses</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Entrega:</span>
                            <span class="spec-value">{{ $epp->frecuencia_entrega ?? '—' }}</span>
                        </div>
                    </div>

                    <!-- Ficha Técnica -->
                    @if($epp->ficha_tecnica)
                    <div class="mt-3">
                        <a href="{{ asset('storage/' . $epp->ficha_tecnica) }}" target="_blank" class="btn btn-sm w-100" style="background-color: #003366; color: white; border: none;">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Ver Ficha Técnica
                        </a>
                    </div>
                    @endif
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

<!-- Modales de Eliminación -->
@foreach($epps as $epp)
<div class="modal fade" id="deleteModal{{ $epp->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $epp->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel{{ $epp->id }}">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0">
                    ¿Estás seguro de que deseas eliminar el EPP <strong>"{{ $epp->nombre }}"</strong>?
                </p>
                <p class="text-muted small mt-2 mb-0">
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
                <form action="{{ route('epps.destroy', $epp->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .card { 
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    .card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 12px 24px rgba(0, 51, 102, 0.15) !important; 
    }
    
    .specification-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .spec-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
    }
    
    .spec-label {
        color: #6c757d;
        font-weight: 500;
        font-size: 0.85rem;
    }
    
    .spec-value {
        font-weight: 600;
        color: #333;
        text-align: right;
        max-width: 50%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .btn-light {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    
    .btn-light:hover {
        background-color: #e9ecef;
    }

    .epp-image-container {
        position: relative;
        overflow: hidden;
    }

    .epp-image {
        max-width: 90%;
        max-height: 90%;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
    }
</style>

<!-- Modal Nuevo EPP -->
<div class="modal fade" id="modalNuevoEpp" tabindex="-1" aria-labelledby="modalNuevoEppLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalNuevoEppLabel">Registrar Nuevo EPP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('epps.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nombre del EPP</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Casco de Seguridad" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tipo / Categoría</label>
                            <input type="text" name="tipo" class="form-control" placeholder="Ej. Protección Craneal" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" placeholder="Describe las características y uso del EPP" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Marca / Modelo</label>
                            <input type="text" name="marca_modelo" class="form-control" placeholder="Ej. 3M H-700">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Código de Logística</label>
                            <input type="text" name="codigo_logistica" class="form-control" placeholder="Ej. LOG-001">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Departamento</label>
                            <select name="departamento_id" class="form-select">
                                <option value="">-- Selecciona un departamento --</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Vida útil (meses)</label>
                            <input type="number" name="vida_util_meses" class="form-control" placeholder="Ej. 12" value="12">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Frecuencia de Entrega</label>
                            <input type="text" name="frecuencia_entrega" class="form-control" placeholder="Ej. Mensual">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Precio (USD)</label>
                            <input type="number" name="precio" class="form-control" placeholder="0.00" step="0.01" value="0.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cantidad / Stock</label>
                        <input type="number" name="cantidad" class="form-control" placeholder="0" value="0">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ficha Técnica (PDF opcional)</label>
                            <input type="file" name="ficha_tecnica" class="form-control" accept=".pdf">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Imagen del EPP</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                            <small class="text-muted">Formatos: JPG, PNG, GIF (Máx: 2MB)</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: #003366;">Guardar EPP</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection