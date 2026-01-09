@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Panel Administrativo</h2>
        <span class="text-muted">Bienvenido Admin Centro</span>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-primary border-4 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Stock Disponible</p>
                        <h3 class="fw-bold mb-0">{{ $epps->sum('stock') ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-box fs-1 text-primary opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-success border-4 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">EPP Entregados</p>
                        <h3 class="fw-bold mb-0">100</h3> </div>
                    <i class="bi bi-truck fs-1 text-success opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-dark border-4 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Stock Bajo</p>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <i class="bi bi-exclamation-circle fs-1 text-dark opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-danger border-4 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Deteriorados</p>
                        <h3 class="fw-bold mb-0">5</h3>
                    </div>
                    <i class="bi bi-trash fs-1 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4">
        <h4 class="fw-bold mb-4">Gestión de Inventario</h4>
        
        <div class="d-flex justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar por nombre o código...">
            </div>
            <button type="button" class="btn btn-primary d-flex align-items-center" style="background-color: #003366;" data-bs-toggle="modal" data-bs-target="#modalNuevoEppInventario">
                <i class="bi bi-plus fs-4 me-1"></i> Nuevo
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        @if($epps->isEmpty())
            <div class="alert alert-warning border-0 shadow-sm">No hay EPP registrados aún</div>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="bg-light">
                        <tr class="text-muted small">
                            <th>EPP</th>
                            <th>Código</th>
                            <th>Stock</th>
                            <th>Total</th>
                            <th>Entregado</th>
                            <th>Deteriorado</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($epps as $epp)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $epp->nombre }}</div>
                                <small class="text-muted">{{ $epp->tipo }}</small>
                            </td>
                            <td>{{ $epp->codigo_logistica ?? 'CSK-00' . $epp->id }}</td>
                            <td class="fw-bold">{{ $epp->stock ?? 0 }}</td>
                            <td>{{ $epp->cantidad ?? 0 }}</td>
                            <td class="text-success fw-bold">{{ $epp->entregado ?? 0 }}</td>
                            <td class="text-danger fw-bold">{{ $epp->deteriorado ?? 0 }}</td>
                            <td>
                                @if($epp->stock > 10)
                                    <span class="badge bg-success-soft text-success">Disponible</span>
                                @elseif($epp->stock > 0)
                                    <span class="badge bg-warning-soft text-warning">Bajo Stock</span>
                                @else
                                    <span class="badge bg-danger-soft text-danger">Agotado</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-secondary btn-sm border-0" title="Editar"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-outline-danger btn-sm border-0" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal Nuevo Inventario -->
<div class="modal fade" id="modalNuevoEppInventario" tabindex="-1" aria-labelledby="modalNuevoInventarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalNuevoInventarioLabel">Nuevo Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevoInventario" method="POST">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">EPP</label>
                        <select name="epp_id" class="form-select" id="eppSelect" required>
                            <option value="">-- Selecciona un EPP --</option>
                            @foreach($epps as $epp)
                                <option value="{{ $epp->id }}">{{ $epp->nombre }} ({{ $epp->codigo_logistica }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Total</label>
                        <input type="number" name="cantidad" class="form-control" placeholder="Ej. 50" value="0" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stock</label>
                        <input type="number" name="stock" class="form-control" placeholder="Ej. 35" value="0" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Entregado</label>
                        <input type="number" name="entregado" class="form-control" placeholder="Ej. 15" value="0" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deteriorado</label>
                        <input type="number" name="deteriorado" class="form-control" placeholder="Ej. 0" value="0" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado" class="form-select" required>
                            <option value="">-- Selecciona estado --</option>
                            <option value="disponible">Disponible</option>
                            <option value="bajo_stock">Bajo Stock</option>
                            <option value="agotado">Agotado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: #003366;">Guardar Inventario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('formNuevoInventario').addEventListener('submit', function(e) {
    e.preventDefault();
    const eppId = document.getElementById('eppSelect').value;
    if (!eppId) {
        alert('Por favor selecciona un EPP');
        return;
    }
    
    const formData = new FormData(this);
    formData.delete('epp_id');
    formData.append('_method', 'PUT');
    const url = `{{ url('epps') }}/${eppId}`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Inventario guardado correctamente');
            bootstrap.Modal.getInstance(document.getElementById('modalNuevoEppInventario')).hide();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Algo salió mal'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar el inventario: ' + error.message);
    });
});
</script>

<style>
    .border-start-4 { border-left-width: 4px !important; }
    .bg-success-soft { background-color: #e6f7ee; }
    .bg-warning-soft { background-color: #fff3cd; }
    .bg-danger-soft { background-color: #f8d7da; }
    .bg-secondary-soft { background-color: #f0f2f5; }
    .table thead th { border-bottom: none; text-transform: none; font-weight: 500; }
    .card { border-radius: 12px; }
    .btn-primary { border-radius: 8px; }
</style>
@endsection