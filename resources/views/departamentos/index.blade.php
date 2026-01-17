@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Panel de Seguridad: Departamentos</h2>
            <p class="text-muted">Mapeo de EPP y control de cumplimiento por área</p>
        </div>
        <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#importarMatrizGeneralModal">
            <i class="fas fa-file-upload me-2"></i>Cargar Matriz General
        </button>
    </div>

    <div class="row">
        @foreach($departamentos as $depto)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm card-depto" style="border-radius: 20px; overflow: hidden;">
                
                <div style="height: 200px; overflow: hidden; position: relative;">
                    <img src="{{ $depto->imagen_url ?? 'https://via.placeholder.com/400x200?text=Tecsup+Area' }}" 
                         class="card-img-top w-100 h-100" 
                         style="object-fit: cover;" 
                         alt="{{ $depto->nombre }}">
                    
                    <span class="position-absolute top-0 end-0 m-3 badge rounded-pill {{ $depto->nivel_riesgo == 'Alto' ? 'bg-danger' : 'bg-warning' }} shadow">
                        Riesgo {{ $depto->nivel_riesgo }}
                    </span>
                </div>

                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark">{{ $depto->nombre }}</h5>
                    <p class="text-muted small">{{ Str::limit($depto->descripcion, 50) }}</p>
                    
                    <div class="row my-3 text-center bg-light py-2 rounded-3 mx-1">
                        <div class="col-6 border-end">
                            <h4 class="mb-0 fw-bold text-dark">{{ $depto->usuarios_count }}</h4>
                            <small class="text-muted">Docentes</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0 fw-bold text-success">100%</h4>
                            <small class="text-muted">Checking</small>
                        </div>
                    </div>

                    <a href="{{ route('departamentos.show', $depto->id) }}" class="btn btn-primary w-100 rounded-pill fw-bold">
                        <i class="fas fa-clipboard-check me-2"></i>Gestionar Asignación
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="modal fade" id="importarMatrizGeneralModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Subir Matriz General (Excel)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('departamentos.importar_general') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4 text-center">
                    <div class="bg-success bg-opacity-10 p-4 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-file-excel text-success fs-1"></i>
                    </div>
                    <p class="text-muted">Selecciona el archivo Excel para mapear automáticamente a los docentes según su departamento correspondiente.</p>
                    <input class="form-control mb-3" type="file" name="excel_file" accept=".xlsx, .xls" required style="border-radius: 10px;">
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Procesar Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card-depto { transition: all 0.3s ease; }
    .card-depto:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
    }
    .btn-primary { background-color: #003a70; border: none; }
    .btn-primary:hover { background-color: #002a50; }
</style>
@endsection