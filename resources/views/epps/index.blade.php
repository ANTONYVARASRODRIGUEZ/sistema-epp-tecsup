@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Panel de Gestión EPP</h2>
            <p class="text-muted">Control de inventario y asignaciones para Jiancarlo</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-danger d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#modalVaciarEpps">
                <i class="bi bi-trash3 me-1"></i> Vaciar Todo
            </button>
            <button type="button" class="btn btn-success d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportarEpp">
                <i class="bi bi-file-earmark-excel me-1"></i> Importar Matriz
            </button>
            <button type="button" class="btn btn-primary d-flex align-items-center shadow-sm" style="background-color: #003366; border: none;" data-bs-toggle="modal" data-bs-target="#modalNuevoEpp">
                <i class="bi bi-plus fs-4 me-1"></i> Nuevo EPP
            </button>
        </div>
    </div>

    {{-- BARRA DE FILTROS AVANZADA --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-3">
                <div class="row g-3">
                    {{-- 1. Filtro por Categoría (Botones) --}}
                    <div class="col-md-12">
                        <div class="d-flex align-items-center gap-2 overflow-auto pb-2">
                            <span class="fw-bold text-muted small text-nowrap"><i class="bi bi-tag"></i> Categoría:</span>
                            <button class="btn btn-sm btn-primary rounded-pill filter-btn px-3" data-filter="all">Todas</button>
                            @foreach($categorias as $cat)
                                <button class="btn btn-sm btn-outline-primary rounded-pill filter-btn text-nowrap px-3" data-filter="cat-{{ $cat->id }}">
                                    {{ $cat->nombre }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. Filtro por Subtipo (Select) --}}
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1 d-block"><i class="bi bi-list-stars"></i> Seleccionar Tipo Específico:</label>
                        <select id="subtipoFilter" class="form-select form-select-sm border-primary shadow-sm">
                            <option value="all">-- Todos los tipos --</option>
                            @foreach($epps->pluck('nombre')->unique()->sort() as $nombreUnico)
                                <option value="{{ strtolower($nombreUnico) }}">{{ $nombreUnico }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. Buscador Manual --}}
                    <div class="col-md-8">
                        <label class="small fw-bold text-muted mb-1 d-block"><i class="bi bi-search"></i> Búsqueda por texto:</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchEpp" class="form-control border-start-0 ps-0" placeholder="Escribe código o descripción...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRILLA DE CARDS --}}
    <div class="row" id="eppGrid">
        @forelse($epps as $epp)
        <div class="col-md-4 mb-4 epp-item cat-{{ $epp->categoria_id }}" 
             data-subtipo="{{ strtolower($epp->nombre) }}" 
             data-nombre="{{ strtolower($epp->nombre) }}" 
             data-codigo="{{ strtolower($epp->codigo_logistica) }}">
            
            <div class="card border-0 shadow-sm h-100 card-epp">
                <div class="epp-image-container position-relative" style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                    @if($epp->imagen)
                        <img src="{{ asset('storage/' . $epp->imagen) }}" class="img-fluid h-100 p-2" style="object-fit: contain;">
                    @else
                        <i class="bi bi-box-seam display-4 text-light"></i>
                    @endif
                    
                    <div class="position-absolute top-0 end-0 m-2">
                        @if($epp->stock > 10)
                            <span class="badge bg-success shadow-sm">Disponible</span>
                        @elseif($epp->stock > 0)
                            <span class="badge bg-warning text-dark shadow-sm">Stock Bajo</span>
                        @else
                            <span class="badge bg-danger shadow-sm">Agotado</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            @php
                                // Lógica de Subtipos: Separamos el nombre por el guión "-" si existe
                                $partes = explode('-', $epp->nombre);
                                $principal = trim($partes[0]);
                                $subtipoDetalle = isset($partes[1]) ? trim($partes[1]) : null;
                            @endphp

                            <h6 class="fw-bold mb-0 text-dark">{{ Str::upper($principal) }}</h6>
                            @if($subtipoDetalle)
                                <span class="badge bg-light text-primary border border-primary-subtle" style="font-size: 0.65rem;">
                                    {{ Str::upper($subtipoDetalle) }}
                                </span>
                            @endif
                            <div class="mt-1">
                                <small class="text-primary fw-semibold" style="font-size: 0.75rem;">
                                    <i class="bi bi-tag-fill me-1"></i>{{ $epp->categoria->nombre ?? 'Sin Categoría' }}
                                </small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm border" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <a class="dropdown-item btn-edit-inventario" href="javascript:void(0)" 
                                       data-epp-id="{{ $epp->id }}" 
                                       data-cantidad="{{ $epp->cantidad }}"
                                       data-stock="{{ $epp->stock }}"
                                       data-entregado="{{ $epp->entregado }}"
                                       data-deteriorado="{{ $epp->deteriorado }}">
                                         <i class="bi bi-pencil me-2 text-primary"></i> Editar Stock
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger btn-delete-epp" data-epp-id="{{ $epp->id }}" data-epp-url="{{ route('epps.destroy', $epp) }}">
                                        <i class="bi bi-trash me-2"></i> Eliminar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="spec-container mb-3 bg-light p-2 rounded border">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Código:</span>
                            <span class="fw-bold text-dark">{{ $epp->codigo_logistica ?? 'CSK-'.$epp->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Stock actual:</span>
                            <span class="fw-bold {{ $epp->stock <= 10 ? 'text-danger' : 'text-success' }}">{{ $epp->stock }} und.</span>
                        </div>
                    </div>

                    <div class="d-grid">
                        <a href="{{ route('departamentos.index') }}" class="btn btn-success btn-sm shadow-sm {{ $epp->stock <= 0 ? 'disabled' : '' }}">
    <i class="bi bi-building me-1"></i> Ir a Departamentos para Asignar
</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-archive display-1 text-muted opacity-25"></i>
            <p class="mt-3 text-muted">No hay EPPs registrados en esta categoría.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- MODALES INTEGRADOS --}}

<div class="modal fade" id="modalNuevoEpp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Registrar Nuevo EPP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('epps.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Nombre del Equipo</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Guante - Nitrilo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Categoría</label>
                            <select name="categoria_id" class="form-select" required>
                                <option value="">Seleccione...</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Código Logística</label>
                            <input type="text" name="codigo_logistica" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Precio S/.</label>
                            <input type="number" name="precio" step="0.01" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Stock Inicial</label>
                            <input type="number" name="cantidad" class="form-control" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Imagen del producto</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: #003366;">Guardar EPP</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImportarEpp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Importar Matriz desde Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('epps.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-file-earmark-spreadsheet display-1 text-success"></i>
                    </div>
                    <p class="text-muted small">Asegúrate que el nombre tenga el formato "Nombre - Subtipo" para una mejor clasificación.</p>
                    <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4">Subir e Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarInventario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Actualizar Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarInventario" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Total Comprado (Histórico)</label>
                        <input type="number" name="cantidad" id="edit_cantidad" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Stock Disponible en Almacén</label>
                        <input type="number" name="stock" id="edit_stock" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-primary">Entregados</label>
                            <input type="number" name="entregado" id="edit_entregado" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-danger">Deteriorados</label>
                            <input type="number" name="deteriorado" id="edit_deteriorado" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #003366;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVaciarEpps" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('epps.clearAll') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center p-4">
                    <i class="bi bi-exclamation-octagon-fill text-danger display-4 mb-3"></i>
                    <h5 class="fw-bold">¿Limpiar Almacén?</h5>
                    <p class="text-muted small">Se borrarán todos los registros de EPP.</p>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-danger shadow-sm">Sí, borrar todo</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No, cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteEppModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <form id="deleteEppForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center p-4">
                    <i class="bi bi-trash3 text-danger display-4 mb-3"></i>
                    <h5 class="fw-bold">¿Eliminar este EPP?</h5>
                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-danger px-4 shadow-sm">Sí, eliminar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPTS DE FILTRADO --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const subtipoFilter = document.getElementById('subtipoFilter');
        const searchInput = document.getElementById('searchEpp');
        const items = document.querySelectorAll('.epp-item');

        function applyFilters() {
            const activeCat = document.querySelector('.filter-btn.btn-primary').dataset.filter;
            const activeSubtipo = subtipoFilter.value;
            const searchText = searchInput.value.toLowerCase();

            items.forEach(item => {
                const matchesCat = activeCat === 'all' || item.classList.contains(activeCat);
                const matchesSubtipo = activeSubtipo === 'all' || item.dataset.subtipo === activeSubtipo;
                const matchesSearch = item.dataset.nombre.includes(searchText) || item.dataset.codigo.includes(searchText);

                item.style.display = (matchesCat && matchesSubtipo && matchesSearch) ? 'block' : 'none';
            });
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
                this.classList.replace('btn-outline-primary', 'btn-primary');
                applyFilters();
            });
        });

        subtipoFilter.addEventListener('change', applyFilters);
        searchInput.addEventListener('input', applyFilters);

        // Script para cargar datos en el modal de edición
        document.querySelectorAll('.btn-edit-inventario').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('formEditarInventario').action = `/epps/${this.dataset.eppId}`;
                document.getElementById('edit_cantidad').value = this.dataset.cantidad;
                document.getElementById('edit_stock').value = this.dataset.stock;
                document.getElementById('edit_entregado').value = this.dataset.entregado;
                document.getElementById('edit_deteriorado').value = this.dataset.deteriorado;
                new bootstrap.Modal(document.getElementById('modalEditarInventario')).show();
            });
        });

        // Script para cargar URL en el modal de eliminación
        document.querySelectorAll('.btn-delete-epp').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('deleteEppForm').action = this.dataset.eppUrl;
                new bootstrap.Modal(document.getElementById('deleteEppModal')).show();
            });
        });
    });
</script>

<style>
    .card-epp { transition: transform 0.2s, box-shadow 0.2s; border-radius: 12px; }
    .card-epp:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .filter-btn { transition: all 0.2s; }
    .overflow-auto::-webkit-scrollbar { display: none; }
</style>
@endsection