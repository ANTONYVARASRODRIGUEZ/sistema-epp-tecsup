@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}" class="text-decoration-none">Usuarios</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ficha Técnica</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0">Perfil del Docente</h2>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary shadow-sm px-3" style="border-radius: 10px;">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center p-4 h-100" style="border-radius: 20px;">
                <div class="card-body">
                    <div class="avatar-text rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3 shadow" 
                         style="width: 100px; height: 100px; font-size: 2.5rem; background: linear-gradient(45deg, #003366, #0052a3);">
                        {{ collect(explode(' ', $usuario->name))->map(fn($n) => substr($n, 0, 1))->take(2)->implode('') }}
                    </div>
                    
                    <h4 class="fw-bold mb-1">{{ $usuario->name }}</h4>
                    <p class="text-muted mb-3">{{ $usuario->email }}</p>

                    @php
                        $badgeColors = [
                            'Admin' => ['bg' => '#e2e8f0', 'text' => '#475569'],
                            'Coordinador' => ['bg' => '#f0fdf4', 'text' => '#166534'],
                            'Docente' => ['bg' => '#f1f5f9', 'text' => '#003366'],
                        ];
                        $colors = $badgeColors[$usuario->role] ?? ['bg' => '#e5e7eb', 'text' => '#374151'];
                    @endphp
                    
                    <span class="badge mb-4" style="background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; padding: 8px 16px; border-radius: 12px; font-size: 0.9rem;">
                        <i class="bi bi-shield-lock me-1"></i> {{ $usuario->role }}
                    </span>

                    <div class="bg-light rounded-4 p-3 mt-2">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Estado de Entrega</small>
                        <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Al día con EPP</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-info-circle me-2 text-primary"></i>Datos Generales</h5>
                    
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted text-uppercase fw-bold">Departamento</label>
                            <p class="fw-bold fs-6 text-primary mb-0">
                                {{ $usuario->departamento->nombre ?? 'No asignado' }}
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted text-uppercase fw-bold">Taller / Lab</label>
                            <p class="fw-bold fs-6 mb-0">{{ $usuario->workshop ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted text-uppercase fw-bold">DNI / ID</label>
                            <p class="fw-bold fs-6 mb-0">{{ $usuario->dni ?? '-' }}</p>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-4 mt-4"><i class="bi bi-ruler me-2 text-primary"></i>Medidas Biométricas</h5>
                    
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded-4 bg-light bg-opacity-50">
                                <div class="icon-shape bg-info bg-opacity-10 text-info p-3 rounded-3 me-3">
                                    <i class="bi bi-footprint fs-4"></i>
                                </div>
                                <div>
                                    <label class="small text-muted d-block fw-bold">Talla de Calzado</label>
                                    <span class="fw-bold fs-4">{{ $usuario->talla_zapatos ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded-4 bg-light bg-opacity-50">
                                <div class="icon-shape bg-warning bg-opacity-10 text-warning p-3 rounded-3 me-3">
                                    <i class="bi bi-person-workspace fs-4"></i>
                                </div>
                                <div>
                                    <label class="small text-muted d-block fw-bold">Talla de Mandil</label>
                                    <span class="fw-bold fs-4">{{ $usuario->talla_mandil ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Miembro desde</small>
                            <p class="mb-0">{{ $usuario->created_at->format('d M, Y') }}</p>
                        </div>
                        <div class="col-md-6 text-md-end mt-4 mt-md-0">
                            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning px-4 shadow-sm" style="border-radius: 10px;">
                                <i class="bi bi-pencil-square me-1"></i> Editar
                            </a>
                            <button class="btn btn-danger px-4 shadow-sm ms-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection