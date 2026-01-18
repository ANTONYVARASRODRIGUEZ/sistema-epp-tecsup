@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <nav aria-label="breadcrumb" class="mb-4">

        <ol class="breadcrumb py-2 px-3 bg-white shadow-sm" style="border-radius: 10px;">

            <li class="breadcrumb-item"><a href="{{ route('departamentos.index') }}" class="text-decoration-none">Departamentos</a></li>

            <li class="breadcrumb-item active fw-bold" aria-current="page">{{ $departamento->nombre }}</li>

        </ol>

    </nav>



    <div class="row mb-4">

        <div class="col-md-8">

            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">

                <div class="card-body d-flex align-items-center">

                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">

                        <i class="bi bi-person-badge text-primary fs-2"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold mb-0">{{ $departamento->nombre }}</h3>

                        <p class="text-muted mb-0">Total de docentes asignados: <strong>{{ $docentes->count() }}</strong></p>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px; background-color: #003366;">

                <div class="card-body text-white d-flex flex-column justify-content-center align-items-center">

                    <button class="btn btn-light w-100 fw-bold mb-2" data-bs-toggle="modal" data-bs-target="#importModal">

                        <i class="bi bi-file-earmark-excel-fill me-2"></i>Sincronizar con Excel

                    </button>

                    <button class="btn btn-outline-light w-100 fw-bold mb-2" data-bs-toggle="modal" data-bs-target="#manualModal">
                <i class="bi bi-person-plus-fill me-2"></i>Agregar Manualmente
            </button>

                    <small class="opacity-75">Última actualización: Hoy, {{ date('H:i A') }}</small>

                </div>

            </div>

        </div>

    </div>



    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="bg-light">

                    <tr>

                        <th class="ps-4">Docente</th>

                        <th>DNI / ID</th>

                        <th>Checking Visual EPP</th>

                        <th>Críticos (Vencidos)</th>

                        <th class="text-end pe-4">Acciones</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($docentes as $docente)

                    <tr>

                        <td class="ps-4">

                            <div class="d-flex align-items-center">

                                <div class="avatar-text rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px; font-size: 0.9rem;">

                                    {{ collect(explode(' ', $docente->name))->map(fn($n) => substr($n, 0, 1))->take(2)->implode('') }}

                                </div>

                                <div>

                                    <span class="fw-bold d-block">{{ $docente->name }}</span>

                                    <small class="text-muted">{{ $docente->email }}</small>

                                </div>

                            </div>

                        </td>

                        <td><code class="text-dark">{{ $docente->dni ?? '72451XXX' }}</code></td>

                        <td>

                            @if($loop->index % 3 == 0)

                                <div class="d-flex gap-2 mb-1">

                                    <i class="bi bi-person-workspace text-success fs-5" title="Casco: OK"></i>

                                    <i class="bi bi-eyeglasses text-success fs-5" title="Lentes: OK"></i>

                                    <i class="bi bi-hand-index-thumb text-danger fs-5" title="Guantes: FALTANTE"></i>

                                </div>

                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3" style="font-size: 0.7rem;">● Incompleto</span>

                            @else

                                <div class="d-flex gap-2 mb-1">

                                    <i class="bi bi-person-workspace text-success fs-5" title="Casco: OK"></i>

                                    <i class="bi bi-eyeglasses text-success fs-5" title="Lentes: OK"></i>

                                    <i class="bi bi-hand-index-thumb text-success fs-5" title="Guantes: OK"></i>

                                </div>

                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3" style="font-size: 0.7rem;">● Equipado</span>

                            @endif

                            <small class="text-muted d-block mt-1" style="font-size: 0.65rem;">Certificado: 2026</small>

                        </td>

                        <td>

                            @if($loop->index % 3 == 0)

                                <span class="text-danger small fw-bold"><i class="bi bi-exclamation-triangle me-1"></i> 1 EPP por renovar</span>

                            @else

                                <span class="text-muted small">Al día</span>

                            @endif

                        </td>

                        <td class="text-end pe-4">

                            <div class="btn-group">

                                <a href="{{ route('usuarios.show', $docente->id) }}" class="btn btn-sm btn-outline-primary" title="Ver Ficha Técnica">

                                    <i class="bi bi-file-earmark-person"></i>

                                </a>

                                <a href="#" class="btn btn-sm btn-primary" title="Registrar Entrega">

                                    <i class="bi bi-plus-lg"></i>

                                </a>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="5" class="text-center py-5 text-muted">

                            <div class="mb-3">

                                <i class="bi bi-people fs-1 opacity-25"></i>

                            </div>

                            No hay docentes registrados en el área de {{ $departamento->nombre }}<br>

                            <small>Usa el botón "Sincronizar con Excel" para cargar la lista.</small>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>



<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">

            <div class="modal-header border-0 pt-4 px-4">

                <h5 class="modal-title fw-bold" id="importModalLabel">Sincronizar Matriz de EPP</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

           

            <form action="{{ route('departamentos.importar', $departamento->id) }}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="modal-body px-4">

                    <div class="text-center mb-4">

                        <div class="bg-success bg-opacity-10 p-4 rounded-circle d-inline-block mb-3">

                            <i class="bi bi-file-earmark-excel text-success" style="font-size: 2.5rem;"></i>

                        </div>

                        <p class="text-muted">Sube la <strong>Matriz de Consistencia</strong> en formato Excel para cargar los docentes a este departamento.</p>

                    </div>

                   

                    <div class="mb-4">

                        <label for="excel_file" class="form-label fw-bold small text-uppercase">Seleccionar Archivo</label>

                        <input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xlsx, .xls" required style="border-radius: 10px;">

                    </div>



                    <div class="p-3 bg-light rounded-3">

                        <div class="d-flex">

                            <i class="bi bi-info-circle-fill text-primary me-2 mt-1"></i>

                            <p class="small mb-0 text-secondary">

                                Al procesar, el sistema mapeará a los docentes y marcará su estado de equipamiento inicial como <strong>Incompleto</strong>.

                            </p>

                        </div>

                    </div>

                </div>

                <div class="modal-footer border-0 pb-4 px-4">

                    <button type="button" class="btn btn-link text-decoration-none text-muted fw-bold" data-bs-dismiss="modal">Cancelar</button>

                    <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm" style="border-radius: 10px;">

                        <i class="bi bi-cloud-arrow-up-fill me-2"></i>Procesar Matriz

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>



<style>

    .avatar-text {

        background: linear-gradient(45deg, #003366, #0052a3);

    }

    .table thead th {

        font-size: 0.8rem;

        text-transform: uppercase;

        letter-spacing: 0.5px;

        font-weight: 700;

        color: #6c757d;

        border-bottom: none;

        padding-top: 15px;

        padding-bottom: 15px;

    }

    .table tbody tr:hover {

        background-color: #f8f9fa;

    }

</style>


<div class="modal fade" id="manualModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold">Nuevo Docente: {{ $departamento->nombre }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <input type="hidden" name="departamento_id" value="{{ $departamento->id }}">
                <input type="hidden" name="role" value="Docente">
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre Completo</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej: Juan Perez" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Correo Institucional</label>
                        <input type="email" name="email" class="form-control" placeholder="jperez@tecsup.edu.pe" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Talla Zapatos</label>
                            <input type="text" name="talla_zapatos" class="form-control" placeholder="42">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Talla Mandil/Chaleco</label>
                            <input type="text" name="talla_mandil" class="form-control" placeholder="M">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Guardar Docente</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

