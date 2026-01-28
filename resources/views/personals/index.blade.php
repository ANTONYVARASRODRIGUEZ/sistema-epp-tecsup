@extends('layouts.app')

@section('content')
<style>
    .card-master { border-radius: 20px; border: none; background: #ffffff; }
    .table-modern thead { background-color: #f8f9fa; border-radius: 15px; }
    .table-modern th { border: none; color: #64748b; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding: 20px; }
    .table-modern td { padding: 20px; border-bottom: 1px solid #f1f5f9; }
    .avatar-circle { width: 45px; height: 45px; background: #003a70; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; }
    .status-badge { padding: 6px 12px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 px-3">
        <div>
            <h2 class="fw-bold mb-0">Base de Datos de Docentes</h2>
            <p class="text-muted">Lista maestra para asignación de EPP</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('organizador.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-grid-3x3-gap me-2"></i>Ir al Organizador
            </a>
            <button class="btn btn-dark rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevoDocente">
                <i class="bi bi-person-plus-fill me-2"></i>Registrar Docente
            </button>
        </div>
    </div>

    <div class="card card-master shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Docente</th>
                            <th>DNI / Código</th>
                            <th>Área Asignada</th>
                            <th>Estado de EPP</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personals as $docente)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        {{ strtoupper(substr($docente->nombre_completo, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $docente->nombre_completo }}</div>
                                        <small class="text-muted">Registrado el {{ $docente->created_at->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted fw-bold">{{ $docente->dni ?? '---' }}</td>
                            <td>
                                @if($docente->departamento)
                                    <span class="status-badge bg-primary bg-opacity-10 text-primary">
                                        <i class="bi bi-building me-1"></i> {{ $docente->departamento->nombre }}
                                    </span>
                                @else
                                    <span class="status-badge bg-warning bg-opacity-10 text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Sin Asignar
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress w-100 me-2" style="height: 6px; border-radius: 10px;">
                                        <div class="progress-bar bg-success" style="width: 85%"></div>
                                    </div>
                                    <small class="fw-bold text-success">85%</small>
                                </div>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-light btn-sm rounded-circle shadow-sm me-1"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-light btn-sm rounded-circle shadow-sm text-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-people display-4 text-light"></i>
                                <p class="text-muted mt-2">No hay docentes en la base de datos.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevoDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 24px; border: none;">
            <div class="modal-body p-5 text-center">
                <div class="bg-dark bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 70px; height: 70px;">
                    <i class="bi bi-person-badge text-dark fs-2"></i>
                </div>
                <h4 class="fw-bold mb-1">Registrar Docente</h4>
                <p class="text-muted mb-4">Añade al personal a la lista maestra</p>

                <form action="{{ route('personals.store') }}" method="POST">
                    @csrf
                    <div class="text-start mb-3">
                        <label class="form-label small fw-bold text-muted">Nombre Completo</label>
                        <input type="text" name="nombre_completo" class="form-control bg-light border-0 py-3 px-4" style="border-radius: 15px;" placeholder="Ej. Pedro Picapiedra" required>
                    </div>
                    <div class="text-start mb-4">
                        <label class="form-label small fw-bold text-muted">DNI o Código de Planilla</label>
                        <input type="text" name="dni" class="form-control bg-light border-0 py-3 px-4" style="border-radius: 15px;" placeholder="Ej. 74859632">
                    </div>
                    
                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-sm">
                        Guardar en Lista Maestra
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection