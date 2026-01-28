@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold">{{ $departamento->nombre }}</h2>
    <p class="text-muted">Lista de personal y asignación de equipos</p>

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="table-responsive p-4">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Carrera</th>
                        <th>DNI</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departamento->personals as $persona)
                    <tr>
                        <td>{{ $persona->nombre_completo }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $persona->carrera }}</span></td>
                        <td>{{ $persona->dni }}</td>
                        <td class="text-end">
                            <button class="btn btn-primary btn-sm rounded-pill px-3" 
                                    onclick="abrirModalEntrega({{ $persona->id }}, '{{ $persona->nombre_completo }}')">
                                <i class="bi bi-hand-index-thumb me-1"></i> Entregar EPP
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEntrega" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Entregar EPP a: <span id="nombreDocente" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('asignaciones.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="personal_id" id="personal_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Seleccionar Equipo (EPP)</label>
                        <select name="epp_id" class="form-select" required>
                            <option value="">-- Buscar equipo --</option>
                            @foreach($epps as $epp)
                                <option value="{{ $epp->id }}">{{ $epp->nombre }} (Stock: {{ $epp->stock }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" value="1" min="1" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold">Confirmar Entrega</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalEntrega(id, nombre) {
    document.getElementById('personal_id').value = id;
    document.getElementById('nombreDocente').innerText = nombre;
    var myModal = new bootstrap.Modal(document.getElementById('modalEntrega'));
    myModal.show();
}
</script>
@endsection