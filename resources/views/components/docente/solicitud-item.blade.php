@props(['solicitud'])
@php
    $statusColor = match($solicitud->estado) {
        'aprobado' => 'bg-success-subtle text-success',
        'rechazado' => 'bg-danger-subtle text-danger',
        default => 'bg-warning-subtle text-warning',
    };
    $collapseId = 'solicitud'.$solicitud->id;
@endphp
<div class="list-group-item py-4 px-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <p class="fw-bold mb-1">Solicitud de EPP</p>
            <span class="text-muted">{{ $solicitud->epp->nombre ?? 'Equipo' }}</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">{{ $solicitud->created_at->format('Y-m-d') }}</span>
            <span class="badge rounded-pill px-3 py-2 {{ $statusColor }}">
                {{ ucfirst($solicitud->estado) }}
            </span>
            <button class="btn btn-sm btn-link text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
    </div>
    <div class="collapse mt-4" id="{{ $collapseId }}">
        <div class="p-3" style="background-color: #f7f7f9; border-radius: 12px;">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="text-muted small mb-1">Motivo</p>
                    <p class="fw-semibold mb-0 text-capitalize">{{ $solicitud->motivo }}</p>
                </div>
                <div class="col-md-3">
                    <p class="text-muted small mb-1">Cantidad</p>
                    <p class="fw-semibold mb-0">{{ $solicitud->cantidad }}</p>
                </div>
                <div class="col-md-3">
                    <p class="text-muted small mb-1">Estado</p>
                    <p class="fw-semibold mb-0">{{ ucfirst($solicitud->estado) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
