@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Mi Perfil</h2>
            <p class="text-muted">Información personal y seguridad de la cuenta</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- COLUMNA IZQUIERDA: Información Personal y Seguridad -->
        <div class="col-lg-6">
            
            <!-- 1️⃣ INFORMACIÓN PERSONAL -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-person-circle me-2" style="color: #003366;"></i>Información Personal</h5>
                
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Nombre Completo</label>
                    <p class="fw-bold fs-6">{{ $usuario->name }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">DNI / Código Institucional</label>
                    <p class="fw-bold fs-6">{{ $usuario->dni ?? '-' }}</p>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">Rol</label>
                        <p>
                            <span class="badge" style="background-color: #003366; padding: 8px 12px; font-size: 0.9rem;">
                                {{ $usuario->role }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">Centro</label>
                        <p class="fw-bold">Tecsup Norte</p>
                    </div>
                </div>

                <hr class="my-3">

                <h6 class="fw-bold mb-3">Datos Editables</h6>

                <!-- Cambiar Email -->
                <form action="{{ route('perfil.actualizar-email') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control bg-light border-start-0" 
                                   value="{{ old('email', $usuario->email) }}" required>
                        </div>
                        <small class="text-muted">Recibirás un correo de verificación</small>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-save me-1"></i>Actualizar Email
                    </button>
                </form>

                <!-- Cambiar Contraseña -->
                <form action="{{ route('perfil.cambiar-contrasena') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña Actual</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password_actual" class="form-control bg-light border-start-0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nueva Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_nueva" class="form-control bg-light border-start-0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirmar Nueva Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_nueva_confirmation" class="form-control bg-light border-start-0" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="background-color: #003366; border: none;">
                        <i class="bi bi-save me-1"></i>Cambiar Contraseña
                    </button>
                </form>
            </div>

            <!-- 2️⃣ INFORMACIÓN LABORAL -->
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-briefcase me-2" style="color: #003366;"></i>Información Laboral</h5>
                
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Puesto</label>
                    <p class="fw-bold">Administrador del Centro de Seguridad</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Departamento</label>
                    <p class="fw-bold">{{ $usuario->department ?? 'Centro de Seguridad' }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Taller/Laboratorio</label>
                    <p class="fw-bold">{{ $usuario->workshop ?? 'Centro de Seguridad' }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Fecha de Asignación</label>
                    <p class="fw-bold">{{ $usuario->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="alert alert-info alert-sm border-0" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>Esta información no es editable desde esta sección</small>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Seguridad y Actividad -->
        <div class="col-lg-6">
            
            <!-- 3️⃣ SEGURIDAD DE LA CUENTA -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-shield-lock me-2" style="color: #dc3545;"></i>Seguridad de la Cuenta</h5>
                
                <!-- Último Acceso -->
                <div class="mb-4 pb-3" style="border-bottom: 1px solid #eee;">
                    <label class="form-label text-muted small fw-bold">Último Inicio de Sesión</label>
                    @if($ultimoAcceso)
                    <p class="mb-1">
                        <span class="badge bg-success">{{ $ultimoAcceso->created_at->format('d/m/Y') }}</span>
                        <strong>{{ $ultimoAcceso->created_at->format('H:i') }}</strong>
                    </p>
                    <small class="text-muted">IP: {{ $ultimoAcceso->ip_address ?? 'No registrada' }}</small>
                    @else
                    <p class="text-muted">Sin registros</p>
                    @endif
                </div>

                <!-- Intentos Fallidos -->
                @if($intentosFallidos > 0)
                <div class="alert alert-warning alert-sm mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>⚠️ Alerta:</strong> Hubo <strong>{{ $intentosFallidos }}</strong> intento(s) fallido(s) en los últimos 7 días
                </div>
                @else
                <div class="alert alert-success alert-sm mb-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>✓ Seguro:</strong> No hay intentos fallidos recientes
                </div>
                @endif

                <hr class="my-3">

                <h6 class="fw-bold mb-3">Historial de Accesos Recientes</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accesosRecientes as $acceso)
                            <tr>
                                <td><small>{{ $acceso->created_at->format('d/m/Y') }}</small></td>
                                <td><small>{{ $acceso->created_at->format('H:i:s') }}</small></td>
                                <td><small class="text-muted">{{ Str::limit($acceso->ip_address, 20) }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-2"><small>Sin registros</small></td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4️⃣ ACTIVIDAD DEL ADMINISTRADOR -->
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-graph-up me-2" style="color: #0d6efd;"></i>Actividad del Administrador</h5>
                
                <!-- Resumen de Actividad -->
                <div class="row mb-4">
                    <div class="col-6 mb-3">
                        <div class="text-center p-3 rounded" style="background: #f0f7ff;">
                            <h3 class="fw-bold" style="color: #003366;">{{ $entregasRegistradas }}</h3>
                            <small class="text-muted">Entregas Registradas</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-center p-3 rounded" style="background: #f0fff4;">
                            <h3 class="fw-bold" style="color: #166534;">{{ $aprobacionesRealizadas }}</h3>
                            <small class="text-muted">Aprobaciones</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 rounded" style="background: #fff0f6;">
                            <h3 class="fw-bold" style="color: #dc3545;">{{ $bajaEpp }}</h3>
                            <small class="text-muted">Bajas de EPP</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 rounded" style="background: #fffbf0;">
                            <h3 class="fw-bold" style="color: #ff6b6b;">{{ $modificacionesInventario }}</h3>
                            <small class="text-muted">Modificaciones</small>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <h6 class="fw-bold mb-3">Últimas Acciones Registradas</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Evento</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($actividadAdmin as $actividad)
                            <tr>
                                <td><small>{{ $actividad->created_at->format('d/m/Y H:i') }}</small></td>
                                <td>
                                    <small>
                                        <span class="badge bg-info">{{ $actividad->evento }}</span>
                                    </small>
                                </td>
                                <td><small class="text-muted">{{ Str::limit($actividad->descripcion, 40) }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-2"><small>Sin actividad registrada</small></td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .alert-sm {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
</style>
@endsection
