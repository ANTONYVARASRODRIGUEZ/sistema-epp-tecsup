@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Catálogo de EPP</h2>
            <p class="text-muted">Matriz de homologación y especificaciones técnicas</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportarEpp">
                <i class="bi bi-file-earmark-excel me-1"></i> Importar Matriz
            </button>
            
            <a href="#" class="btn btn-primary d-flex align-items-center shadow-sm" style="background-color: #003366; border: none;" data-bs-toggle="modal" data-bs-target="#modalNuevoEpp">
                <i class="bi bi-plus fs-4 me-1"></i> Nuevo EPP
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        @forelse($epps as $epp)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
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

                    <div style="background: #f0f7ff; padding: 10px 12px; border-radius: 8px; margin-bottom: 12px;">
                        <div class="row g-0">
                            <div class="col-6">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Precio</small>
                                <span class="text-success fw-bold" style="font-size: 1.2rem;">S/ {{ number_format($epp->precio ?? 0, 2) }}</span>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Stock</small>
                                <span style="font-size: 1.2rem; font-weight: bold;">{{ $epp->cantidad ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="specification-group">
                        <div class="spec-item">
                            <span class="spec-label">Vida útil:</span>
                            <span class="spec-value">{{ $epp->vida_util_meses }} meses</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Entrega:</span>
                            <span class="spec-value text-truncate" style="max-width: 120px;">{{ $epp->frecuencia_entrega ?? '—' }}</span>
                        </div>
                    </div>

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

<div class="modal fade" id="modalImportarEpp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Importación Masiva (Excel)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('epps.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <div class="alert alert-info border-0 small">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Asegúrate de que el archivo tenga las columnas: <strong>EPP, Descripción, Frecuencia de Entrega, Código de logística, MARCA / MODELO, PRECIO Soles.</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Seleccionar archivo .xlsx o .csv</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold text-white">Subir e Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevoEpp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Registrar Nuevo EPP</h5>
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
                        <textarea name="descripcion" class="form-control" placeholder="Describe las características" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Marca / Modelo</label>
                            <input type="text" name="marca_modelo" class="form-control" placeholder="Ej. 3M H-700">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Código de Logística</label>
                            <input type="text" name="codigo_logistica" class="form-control" placeholder="Ej. 24370">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Vida útil (meses)</label>
                            <input type="number" name="vida_util_meses" class="form-control" value="12">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Precio (S/.)</label>
                            <input type="number" name="precio" class="form-control" step="0.01" value="0.00">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Frecuencia</label>
                            <input type="text" name="frecuencia_entrega" class="form-control" placeholder="Ej. Anual">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Stock Inicial</label>
                            <input type="number" name="cantidad" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Imagen</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ficha (PDF)</label>
                            <input type="file" name="ficha_tecnica" class="form-control" accept=".pdf">
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

@foreach($epps as $epp)
<div class="modal fade" id="deleteModal{{ $epp->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                ¿Estás seguro de eliminar <strong>"{{ $epp->nombre }}"</strong>? Esta acción es irreversible.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('epps.destroy', $epp->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">Sí, Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .card { transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%; }
    .card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0, 51, 102, 0.15) !important; }
    .specification-group { display: flex; flex-direction: column; gap: 8px; }
    .spec-item { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; border-bottom: 1px solid #f0f0f0; font-size: 0.85rem; }
    .spec-label { color: #6c757d; font-weight: 500; }
    .spec-value { font-weight: 600; color: #333; }
    .epp-image { max-width: 80%; max-height: 80%; object-fit: contain; }
    .btn-success { background-color: #198754; border: none; }
</style>
@endsection