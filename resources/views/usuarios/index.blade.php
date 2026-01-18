@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000;">Gestión de Usuarios</h1>
            <p class="text-muted">Crear y administrar cuentas de usuario (Carga simplificada)</p>
        </div>
        <button type="button" 
            class="btn btn-primary px-4 py-2 d-flex align-items-center" 
            style="background-color: #003366; border-radius: 8px;"
            data-bs-toggle="modal" 
            data-bs-target="#modalNuevoUsuario">
            <i class="bi bi-plus-lg me-2"></i> Nuevo Usuario
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card card-custom bg-white shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Departamento</th>
                            <th>Estado Perfil</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $usuario->name }}</div>
                                <small class="text-muted">DNI: {{ $usuario->dni ?? 'Pendiente' }}</small>
                            </td>
                            <td class="text-muted">{{ $usuario->email }}</td>
                            <td>
                                @php
                                    $role = $usuario->role ?? 'Usuario';
                                    $badgeColors = [
                                        'Admin' => ['bg' => '#e2e8f0', 'text' => '#475569'],
                                        'Coordinador' => ['bg' => '#f0fdf4', 'text' => '#166534'],
                                        'Docente' => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                                    ];
                                    $colors = $badgeColors[$role] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                                @endphp
                                <span class="badge px-3 py-2 rounded-pill" style="background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }};">
                                    {{ $role }}
                                </span>
                            </td>
                            <td>
                                @if($usuario->departamento)
                                    <span class="text-dark">{{ $usuario->departamento->nombre }}</span>
                                @else
                                    <span class="badge bg-light text-warning border">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @if($usuario->departamento_id && $usuario->talla_zapatos)
                                    <span class="text-success small"><i class="bi bi-check-circle-fill"></i> Completo</span>
                                @else
                                    <span class="text-danger small"><i class="bi bi-clock"></i> Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('usuarios.show', $usuario->id) }}" class="btn btn-sm btn-light border p-1" title="Ver"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-light border p-1" title="Editar"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger p-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $usuario->id }}"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <p class="text-muted mb-0">No hay usuarios registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Registrar Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre Completo</label>
                        <input type="text" name="name" id="inputNombre" class="form-control" placeholder="Ej. Juan Pérez García" required autocomplete="off">
                        <div id="emailHelp" class="form-text mt-2 text-primary small"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo Institucional (Prefijo)</label>
                        <div class="input-group">
                            <input type="text" name="email_prefix" id="inputEmail" class="form-control" placeholder="j.perez" required>
                            <span class="input-group-text bg-light text-muted">@tecsup.edu.pe</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rol del Sistema</label>
                        <select name="role" class="form-select" required>
                            <option value="Docente" selected>Docente</option>
                            <option value="Coordinador">Coordinador</option>
                            <option value="Admin">Administrador</option>
                        </select>
                    </div>

                    <div class="p-3 bg-light rounded-3">
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-shield-lock-fill me-2 fs-5 text-primary"></i>
                            <span>La contraseña por defecto será: <strong>Tecsup2026</strong>. El usuario podrá cambiarla luego.</span>
                        </div>
                        <input type="hidden" name="password" value="Tecsup2026">
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background-color: #003366;">Crear Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($usuarios as $usuario)
<div class="modal fade" id="deleteModal{{ $usuario->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title fw-bold text-danger">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <p>¿Estás seguro de eliminar a <strong>"{{ $usuario->name }}"</strong>?</p>
                <small class="text-muted">Esta acción es irreversible.</small>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Sí, Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    // Lógica para autogenerar correo basado en el nombre
    document.getElementById('inputNombre').addEventListener('input', function() {
        let nombre = this.value.toLowerCase().trim();
        let emailInput = document.getElementById('inputEmail');
        let helpText = document.getElementById('emailHelp');

        if (nombre === "") {
            emailInput.value = "";
            helpText.innerText = "";
            return;
        }

        let partes = nombre.split(" ");
        let sugerencia = "";

        if (partes.length >= 2) {
            // Ejemplo: Juan Perez -> j.perez
            sugerencia = partes[0].charAt(0) + "." + partes[partes.length - 1];
        } else {
            sugerencia = partes[0];
        }

        // Limpiar acentos y caracteres raros
        sugerencia = sugerencia.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^a-z.]/g, "");
        
        emailInput.value = sugerencia;
        helpText.innerText = "Sugerencia generada: " + sugerencia + "@tecsup.edu.pe";
    });
</script>

<style>
    .table-hover tbody tr:hover { background-color: #f8fafc; }
    .badge { font-weight: 500; font-size: 0.75rem; }
    .btn-sm { border-radius: 6px; }
</style>
@endsection