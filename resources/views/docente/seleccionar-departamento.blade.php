@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">¡Bienvenido al Sistema de Gestión EPP!</h2>
        <p class="text-muted">Para comenzar, selecciona el departamento al que perteneces.</p>
    </div>

    <div class="row g-4 justify-content-center">
        @foreach($departamentos as $depto)
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 text-center p-4" style="border-radius: 20px; transition: transform 0.3s;">
                    <div class="mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block">
                            <i class="bi bi-building text-primary fs-1"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold">{{ $depto->nombre }}</h5>
                    <p class="small text-muted">Haz clic abajo para unirte a este equipo y configurar tus tallas.</p>
                    
                    <button class="btn btn-outline-primary w-100 rounded-pill fw-bold" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalUnirse{{ $depto->id }}">
                        Unirme a {{ $depto->nombre }}
                    </button>
                </div>
            </div>

            <div class="modal fade" id="modalUnirse{{ $depto->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                        <form action="{{ route('docente.unirse') }}" method="POST">
                            @csrf
                            <input type="hidden" name="departamento_id" value="{{ $depto->id }}">
                            <div class="modal-body p-4">
                                <h5 class="fw-bold mb-3">Completar Registro: {{ $depto->nombre }}</h5>
                                <p class="text-muted small mb-4">Por favor, ingresa tus tallas de EPP para finalizar.</p>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Talla de Zapatos (Perú)</label>
                                    <input type="text" name="talla_zapatos" class="form-control" placeholder="Ej: 41" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Talla de Mandil / Chaleco</label>
                                    <select name="talla_mandil" class="form-select" required>
                                        <option value="S">S (Small)</option>
                                        <option value="M">M (Medium)</option>
                                        <option value="L">L (Large)</option>
                                        <option value="XL">XL (Extra Large)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary px-4">Confirmar y Entrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection