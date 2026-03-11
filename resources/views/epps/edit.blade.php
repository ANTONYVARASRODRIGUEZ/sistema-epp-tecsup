@extends('layouts.app')

@section('content')
<style>
    .input-group-text { border: 1px solid #dee2e6; }
    .form-control, .form-select { border: 1px solid #dee2e6; }
    .form-control:focus, .form-select:focus { border-color: #003366; box-shadow: none; }
    .page-title { font-size: clamp(1.2rem, 4vw, 1.6rem); }

    .section-label {
        font-size: .68rem; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; color: #6c757d;
        display: flex; align-items: center; gap: 6px;
        padding: 6px 10px; background: #f8f9fa;
        border-radius: 6px; margin-bottom: 14px;
        border-left: 3px solid #003366;
    }

    /* ── DEPTO CHIPS ── */
    .depto-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
        gap: 8px;
    }
    .depto-chip {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 12px; border-radius: 8px; cursor: pointer;
        border: 1.5px solid #dee2e6; background: #fff;
        transition: all .15s; font-size: .82rem; user-select: none;
        line-height: 1.3;
    }
    .depto-chip:hover { border-color: #003366; background: #f0f4ff; }
    .depto-chip:has(input:checked) {
        border-color: #003366; background: #e8efff;
        color: #003366; font-weight: 600;
    }
    .depto-chip input { display: none; }
    .depto-chip .chip-icon { font-size: .9rem; flex-shrink: 0; opacity: .6; }
    .depto-chip:has(input:checked) .chip-icon { opacity: 1; }

    /* ── RESPONSIVE ── */

    /* Chips en xs: 2 columnas fijas para que no queden demasiado pequeños */
    @media (max-width: 575.98px) {
        .depto-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .depto-chip {
            font-size: .75rem;
            padding: 6px 8px;
        }

        /* Botones de acción: ancho completo en xs */
        .form-actions .btn {
            width: 100%;
            justify-content: center;
        }

        /* Card body más compacto */
        .card-body { padding: 1rem !important; }

        /* Preview imagen centrada */
        #preview-container { text-align: center; }
    }

    /* En sm+ los botones quedan lado a lado */
    @media (min-width: 576px) {
        .form-actions .btn { width: auto; }
    }

    /* Imagen actual responsive */
    .current-image-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .current-image-wrapper img {
        width: 80px; height: 80px;
        object-fit: contain;
        background: #f8f9fa;
        padding: 4px;
        flex-shrink: 0;
    }
</style>

<div class="container-fluid py-2 px-3 px-md-4">

    {{-- ── HEADER ── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h2 class="page-title fw-bold mb-0">Editar EPP</h2>
            <p class="text-muted small mb-0">Modifica los datos del equipo de protección personal.</p>
        </div>
        <a href="{{ route('epps.index') }}" class="btn btn-outline-secondary shadow-sm flex-shrink-0">
            <i class="bi bi-arrow-left me-1"></i>Volver al catálogo
        </a>
    </div>

    {{-- ── ERRORS ── --}}
    @if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 p-md-4 p-lg-5">
                    <form action="{{ route('epps.update', $epp->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- ── 1. IDENTIFICACIÓN ── --}}
                        <div class="section-label"><i class="bi bi-card-text"></i>Identificación del equipo</div>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold small">Nombre del EPP <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-box-seam"></i></span>
                                    <input type="text" name="nombre"
                                           class="form-control bg-light border-start-0"
                                           value="{{ old('nombre', $epp->nombre) }}" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold small">Categoría <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-tag"></i></span>
                                    <select name="categoria_id" class="form-select bg-light border-start-0" required>
                                        <option value="">— Selecciona una categoría —</option>
                                        @foreach($categorias as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ old('categoria_id', $epp->categoria_id) == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="tipo" value="{{ old('tipo', $epp->tipo) }}">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold small">Marca / Modelo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-building"></i></span>
                                    <input type="text" name="marca_modelo"
                                           class="form-control bg-light border-start-0"
                                           value="{{ old('marca_modelo', $epp->marca_modelo) }}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold small">Código de Logística</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-barcode"></i></span>
                                    <input type="text" name="codigo_logistica"
                                           class="form-control bg-light border-start-0"
                                           value="{{ old('codigo_logistica', $epp->codigo_logistica) }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Descripción</label>
                                <textarea name="descripcion" class="form-control bg-light" rows="3">{{ old('descripcion', $epp->descripcion) }}</textarea>
                            </div>
                        </div>

                        {{-- ── 2. CONFIGURACIÓN ── --}}
                        <div class="section-label"><i class="bi bi-sliders"></i>Configuración y uso</div>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-bold small">Vida útil (meses) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar-event"></i></span>
                                    <input type="number" name="vida_util_meses"
                                           class="form-control bg-light border-start-0"
                                           value="{{ old('vida_util_meses', $epp->vida_util_meses) }}" required min="1">
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size:.72rem;">Duración estimada del equipo.</small>
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-bold small">Frecuencia de Entrega</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-arrow-repeat"></i></span>
                                    <input type="text" name="frecuencia_entrega"
                                           class="form-control bg-light border-start-0"
                                           value="{{ old('frecuencia_entrega', $epp->frecuencia_entrega) }}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-bold small">Fecha Ingreso al Almacén</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar2-check"></i></span>
                                    <input type="date" name="fecha_ingreso_almacen"
                                           class="form-control bg-light border-start-0"
                                           value="{{ old('fecha_ingreso_almacen', $epp->fecha_ingreso_almacen ?? ($epp->created_at ? $epp->created_at->format('Y-m-d') : '')) }}">
                                </div>
                                <small class="text-muted" style="font-size:.72rem;">El vencimiento se calcula automáticamente.</small>
                            </div>
                        </div>

                        {{-- ── 3. DEPARTAMENTOS ── --}}
                        <div class="section-label"><i class="bi bi-building"></i>Áreas / Departamentos autorizados</div>
                        <div class="mb-4">
                            @if($departamentos->count())
                                <div class="depto-grid">
                                    @foreach($departamentos as $depto)
                                        <label class="depto-chip" for="edit_depto_{{ $depto->id }}">
                                            <input type="checkbox"
                                                   name="departamentos[]"
                                                   value="{{ $depto->id }}"
                                                   id="edit_depto_{{ $depto->id }}"
                                                   @if(in_array($depto->id, old('departamentos', $epp->departamentos->pluck('id')->toArray()))) checked @endif>
                                            <i class="bi bi-building chip-icon"></i>
                                            <span>{{ $depto->nombre }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <small class="text-muted d-block mt-2" style="font-size:.72rem;">
                                    <i class="bi bi-info-circle me-1"></i>Marca todos los departamentos que pueden solicitar este EPP.
                                </small>
                            @else
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-exclamation-circle me-1"></i>No hay departamentos registrados.
                                </p>
                            @endif
                        </div>

                        {{-- ── 4. IMAGEN ── --}}
                        <div class="section-label"><i class="bi bi-image"></i>Imagen del EPP</div>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold small">Subir nueva imagen</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-image"></i></span>
                                    <input type="file" name="imagen" id="image-input"
                                           class="form-control bg-light border-start-0"
                                           accept="image/*">
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size:.72rem;">JPG, PNG, GIF (Máx: 2MB)</small>
                            </div>

                            {{-- Preview nueva imagen --}}
                            <div id="preview-container" class="col-12 col-sm-6 text-center text-sm-start" style="display:none;">
                                <label class="form-label fw-bold small d-block text-start">Vista previa (nueva imagen)</label>
                                <img id="image-preview" src="" alt="Vista previa"
                                     class="rounded border shadow-sm"
                                     style="max-width:180px; max-height:160px; object-fit:contain; background:#f8f9fa; padding:4px;">
                            </div>

                            {{-- Imagen actual --}}
                            @if($epp->imagen)
                            <div class="col-12">
                                <label class="form-label fw-bold small">Imagen actual</label>
                                <div class="current-image-wrapper">
                                    <img src="{{ asset('storage/' . $epp->imagen) }}"
                                         alt="Imagen actual"
                                         class="rounded border shadow-sm">
                                    <small class="text-muted">Sube una nueva imagen para reemplazarla.</small>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- ── ACTIONS ── --}}
                        <div class="form-actions d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('epps.index') }}" class="btn btn-outline-secondary order-sm-1">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold order-sm-2"
                                    style="background-color:#003366; border:none;">
                                <i class="bi bi-save me-1"></i>Guardar Cambios
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const imageInput       = document.getElementById('image-input');
    const previewContainer = document.getElementById('preview-container');
    const imagePreview     = document.getElementById('image-preview');

    if (imageInput) {
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
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