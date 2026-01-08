@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Registrar Nuevo EPP</h2>
            <p class="text-muted">Asegúrate de completar todos los campos obligatorios.</p>
        </div>
        <a href="{{ route('epps.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <div class="card-body">
                    <form action="{{ route('epps.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre del EPP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-box-seam"></i></span>
                                    <input type="text" name="nombre" class="form-control bg-light border-start-0" placeholder="Ej: Casco de Seguridad" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tipo / Categoría</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-tag"></i></span>
                                    <input type="text" name="tipo" class="form-control bg-light border-start-0" placeholder="Ej: Protección Craneal" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Vida útil (meses)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar-event"></i></span>
                                    <input type="number" name="vida_util_meses" class="form-control bg-light border-start-0" placeholder="Ej: 12" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ficha Técnica (PDF opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-file-earmark-pdf"></i></span>
                                    <input type="file" name="ficha_tecnica" class="form-control bg-light border-start-0">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="reset" class="btn btn-light me-2">Limpiar Campos</button>
                            <button type="submit" class="btn btn-primary px-4" style="background-color: #003366; border: none;">
                                <i class="bi bi-save me-1"></i> Guardar EPP
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group-text { border: 1px solid #dee2e6; }
    .form-control { border: 1px solid #dee2e6; }
    .form-control:focus { border-color: #003366; box-shadow: none; }
</style>
@endsection