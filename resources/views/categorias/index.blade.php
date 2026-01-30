@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-tags"></i> Gestión de Categorías de EPP</h5>
                </div>
                <div class="card-body">
                    
                    <form action="{{ route('categorias.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Guantes Dieléctricos, Cascos, Protección Visual..." required>
                            <button class="btn btn-success" type="submit">
                                <i class="bi bi-plus-circle"></i> Agregar Categoría
                            </button>
                        </div>
                    </form>

                    <hr>

                    <table class="table table-hover mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre de la Categoría</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categorias as $categoria)
                            <tr>
                                <td>{{ $categoria->id }}</td>
                                <td><strong>{{ $categoria->nombre }}</strong></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmarEliminacion('{{ route('categorias.destroy', $categoria->id) }}')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No hay categorías registradas aún.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminación -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-body p-4 text-center">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">¿Eliminar Categoría?</h5>
                <p class="text-muted mb-4">Esta acción borrará la categoría permanentemente. ¿Estás seguro?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formEliminarCategoria" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarEliminacion(url) {
    document.getElementById('formEliminarCategoria').action = url;
    var myModal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    myModal.show();
}
</script>
@endsection