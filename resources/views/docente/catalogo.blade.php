@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold mb-0">Catálogo de EPP Homologados</h2>
        <p class="text-muted">Solicita los equipos de protección personal que necesitas</p>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-3">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-0 bg-transparent" placeholder="Buscar EPP por nombre o descripción...">
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($epps as $epp)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; overflow: hidden;">
                <div style="height: 250px; background-color: #f8f9fa;">
                    <img src="{{ asset('storage/' . $epp->imagen) }}" 
                         class="w-100 h-100" 
                         style="object-fit: cover;"
                         onerror="this.src='https://via.placeholder.com/400x250?text=Sin+Imagen'">
                </div>

                <div class="card-body p-4">
                    <h4 class="fw-bold mb-2">{{ $epp->nombre }}</h4>
                    <p class="text-muted small mb-4">{{ $epp->descripcion ?? 'Equipo de protección para labores técnicas.' }}</p>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Marca:</span>
                        <span class="fw-bold">{{ $epp->marca ?? '3M' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Norma:</span>
                        <span class="fw-bold">ANSI Z89.1</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">Renovación:</span>
                        <span class="fw-bold text-dark">{{ $epp->vida_util_meses * 30 }} días</span>
                    </div>

                    <button class="btn btn-primary w-100 py-2 fw-bold d-flex align-items-center justify-content-center" 
                            style="background-color: #003366; border: none; border-radius: 10px;"
                            data-bs-toggle="modal" 
                            data-bs-target="#modalSolicitud{{ $epp->id }}">
                        <i class="bi bi-plus fs-5 me-2"></i> Solicitar este EPP
                    </button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalSolicitud{{ $epp->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0" style="border-radius: 20px;">
                    <div class="modal-header border-0 pt-4 px-4">
                        <h5 class="fw-bold">Confirmar Solicitud</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form action="{{ route('solicitudes.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="epp_id" value="{{ $epp->id }}">
                        
                        <div class="modal-body px-4">
                            <div class="d-flex align-items-center mb-4 p-3 bg-light" style="border-radius: 15px;">
                                <img src="{{ asset('storage/' . $epp->imagen) }}" width="60" height="60" class="rounded shadow-sm me-3" style="object-fit: cover;" onerror="this.src='https://via.placeholder.com/60x60?text=EPP'">
                                <div>
                                    <p class="mb-0 text-muted small">Vas a solicitar:</p>
                                    <h6 class="fw-bold mb-0">{{ $epp->nombre }}</h6>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">¿POR QUÉ NECESITAS ESTE EQUIPO?</label>
                                <textarea name="motivo" class="form-control border-0 bg-light" rows="3" placeholder="Ej: Mi casco actual está dañado o vencido..." style="border-radius: 12px;" required></textarea>
                            </div>

                            <div class="alert alert-info border-0 small d-flex align-items-center" style="border-radius: 12px; background-color: #e7f3ff; color: #004a99;">
                                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                                <span>Tu solicitud será enviada al administrador para su revisión.</span>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pb-4 px-4">
                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold" style="background-color: #003366; border: none; border-radius: 10px;">
                                Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">No hay equipos disponibles en el catálogo en este momento.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection