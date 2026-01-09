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
                                    <input type="text" name="nombre" class="form-control bg-light border-start-0" placeholder="Ej: Casco de Seguridad" value="{{ old('nombre') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tipo / Categoría</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-tag"></i></span>
                                    <input type="text" name="tipo" class="form-control bg-light border-start-0" placeholder="Ej: Protección Craneal" value="{{ old('tipo') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control bg-light" rows="3" placeholder="Describe las características y uso del EPP">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Marca / Modelo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-building"></i></span>
                                    <input type="text" name="marca_modelo" class="form-control bg-light border-start-0" placeholder="Ej: 3M H-700" value="{{ old('marca_modelo') }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Código de Logística</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-barcode"></i></span>
                                    <input type="text" name="codigo_logistica" class="form-control bg-light border-start-0" placeholder="Ej: LOG-001" value="{{ old('codigo_logistica') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Departamento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-building"></i></span>
                                    <select name="departamento_id" class="form-select bg-light border-start-0">
                                        <option value="">-- Selecciona un departamento --</option>
                                        @forelse($departamentos as $depto)
                                            <option value="{{ $depto->id }}" {{ old('departamento_id') == $depto->id ? 'selected' : '' }}>
                                                {{ $depto->nombre }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No hay departamentos disponibles</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Vida útil (meses)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar-event"></i></span>
                                    <input type="number" name="vida_util_meses" class="form-control bg-light border-start-0" placeholder="Ej: 12" value="{{ old('vida_util_meses') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Frecuencia de Entrega</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-arrow-repeat"></i></span>
                                    <input type="text" name="frecuencia_entrega" class="form-control bg-light border-start-0" placeholder="Ej: Mensual" value="{{ old('frecuencia_entrega') }}">
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Precio (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="number" name="precio" class="form-control bg-light border-start-0" placeholder="0.00" step="0.01" value="{{ old('precio') }}">
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Cantidad</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-box"></i></span>
                                    <input type="number" name="cantidad" class="form-control bg-light border-start-0" placeholder="0" value="{{ old('cantidad') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ficha Técnica (PDF opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-file-earmark-pdf"></i></span>
                                    <input type="file" name="ficha_tecnica" class="form-control bg-light border-start-0" accept=".pdf">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Imagen del EPP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-image"></i></span>
                                    <input type="file" name="imagen" class="form-control bg-light border-start-0" accept="image/*">
                                </div>
                                <small class="text-muted d-block mt-1">Formatos: JPG, PNG, GIF (Máx: 2MB)</small>
                            </div>
                        </div>

                        <div class="row" id="preview-container" style="display: none;">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Vista Previa</label>
                                <div class="text-center">
                                    <img id="image-preview" src="" alt="Vista previa" class="img-fluid rounded border" style="max-width: 300px; max-height: 250px; object-fit: cover;">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.querySelector('input[name="imagen"]');
        const previewContainer = document.getElementById('preview-container');
        const imagePreview = document.getElementById('image-preview');

        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imagePreview.src = event.target.result;
                        previewContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.style.display = 'none';
                    imagePreview.src = '';
                }
            });
        }
    });
</script>
@endsection