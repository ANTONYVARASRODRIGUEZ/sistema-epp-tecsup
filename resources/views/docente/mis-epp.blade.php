@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Mis EPP Asignados</h2>
        <p class="text-muted mb-0">Control y seguimiento de tus equipos de protección personal</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 18px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background: #f4f4f6;">
                        <tr class="text-muted text-uppercase small">
                            <th class="ps-4" style="width: 26%">EPP</th>
                            <th style="width: 14%">Fecha Entrega</th>
                            <th style="width: 14%">Vencimiento</th>
                            <th style="width: 14%">Días Restantes</th>
                            <th style="width: 12%">Estado</th>
                            <th style="width: 20%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($misEpps as $solicitud)
                            @php
                                $epp = $solicitud->epp;
                                $fechaEntrega = optional($solicitud->fecha_aprobacion)->format('Y-m-d') ?? '—';
                                $fechaVencimiento = optional($solicitud->fecha_vencimiento)->format('Y-m-d') ?? '—';
                                $diasRestantes = $solicitud->fecha_vencimiento
                                    ? now()->diffInDays($solicitud->fecha_vencimiento, false)
                                    : null;
                                $estaVencido = !is_null($diasRestantes) && $diasRestantes < 0;
                                $estadoTexto = $estaVencido ? 'Vencido' : 'Vigente';
                                $diasTexto = is_null($diasRestantes)
                                    ? '—'
                                    : ($estaVencido ? 'Vencido' : $diasRestantes . ' días');
                                $imagen = $epp?->imagen ? asset('storage/' . $epp->imagen) : 'https://via.placeholder.com/80?text=EPP';
                            @endphp
                            <tr class="border-bottom">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-3 me-3" style="width: 56px; height: 56px; overflow: hidden; background: #f8f9fb;">
                                            <img src="{{ $imagen }}" alt="{{ $epp?->nombre ?? 'EPP' }}" class="w-100 h-100" style="object-fit: cover;">
                                        </div>
                                        <div>
                                            <p class="fw-semibold mb-0">{{ $epp?->nombre ?? 'Equipo' }}</p>
                                            <span class="text-muted small">Equipo asignado</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted fw-semibold">{{ $fechaEntrega }}</td>
                                <td class="text-muted fw-semibold">{{ $fechaVencimiento }}</td>
                                <td class="fw-semibold {{ $estaVencido ? 'text-danger' : 'text-success' }}">
                                    {{ $diasTexto }}
                                </td>
                                <td>
                                    <span class="badge px-3 py-2 rounded-pill {{ $estaVencido ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                                        {{ $estadoTexto }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-light border fw-semibold px-4" style="border-radius: 999px;">
                                            Renovar
                                        </button>
                                        <button class="btn btn-outline-secondary border-0" style="border-radius: 50%; width: 40px; height: 40px;" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p class="text-muted mb-0">No tienes EPP asignados actualmente.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
