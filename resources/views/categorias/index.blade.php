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
                                    <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta categoría? Esto podría afectar a los EPPs vinculados.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
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
@endsection