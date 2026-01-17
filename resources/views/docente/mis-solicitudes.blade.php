@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Mis Solicitudes</h2>
        <p class="text-muted mb-0">Seguimiento de todas tus solicitudes de EPP</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-0">
            @forelse($misSolicitudes as $solicitud)
                <x-docente.solicitud-item :solicitud="$solicitud" />
            @empty
                <div class="py-5 text-center">
                    <p class="text-muted mb-4">No tienes solicitudes registradas</p>
                    <a href="{{ route('epps.catalogo') }}" class="btn fw-bold text-white px-5 py-3" style="background-color: #003da5; border-radius: 999px;">
                        Realizar tu primera solicitud
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
