@extends('layouts.app')

@section('content')
<style>
    /* ── KPI CARDS ── */
    .kpi-card {
        border-radius: 14px;
        border: none;
        transition: transform .2s, box-shadow .2s;
        overflow: hidden;
    }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,.1) !important; }
    .kpi-icon {
        width: 54px; height: 54px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    /* ── CHART CARDS ── */
    .chart-card { border-radius: 14px; border: none; }
    .chart-card .card-header {
        background: transparent;
        border-bottom: 1px solid #f0f0f0;
        padding: 1rem 1.4rem;
    }
    .chart-card .card-header h6 {
        font-weight: 700; font-size: .85rem;
        letter-spacing: .04em; text-transform: uppercase;
        color: #444; margin: 0;
    }

    /* ── ALERT ITEMS ── */
    .alert-item {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 14px; border-radius: 10px;
        background: #fff8f0; border-left: 3px solid #fd7e14;
        margin-bottom: 8px;
    }
    .alert-item.vencido { background: #fff3f3; border-color: #dc3545; }
    .alert-item.stock   { background: #fffbf0; border-color: #ffc107; }

    /* ── NOTIF BELL ── */
    .notif-btn {
        position: relative; background: none; border: none; padding: 0;
        color: #555; cursor: pointer;
    }
    .notif-btn .badge-dot {
        position: absolute; top: -2px; right: -3px;
        width: 10px; height: 10px; border-radius: 50%;
        background: #dc3545; border: 2px solid #fff;
    }
    .notif-panel {
        position: fixed; top: 60px; right: 10px; z-index: 9999;
        width: min(360px, calc(100vw - 20px));
        max-height: 70vh; overflow-y: auto;
        background: #fff; border-radius: 14px;
        box-shadow: 0 12px 40px rgba(0,0,0,.18);
        display: none;
    }
    .notif-panel.open { display: block; }
    .notif-item {
        display: flex; gap: 12px; align-items: flex-start;
        padding: 12px 16px; border-bottom: 1px solid #f5f5f5;
        transition: background .15s;
    }
    .notif-item:hover { background: #fafafa; }
    .notif-item:last-child { border-bottom: none; }
    .notif-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 6px; flex-shrink: 0; }

    /* ── TABLE ── */
    .dash-table thead th {
        font-size: .72rem; font-weight: 700; letter-spacing: .06em;
        text-transform: uppercase; color: #888; border: none; padding: 10px 14px;
        white-space: nowrap;
    }
    .dash-table tbody td { font-size: .875rem; padding: 10px 14px; vertical-align: middle; }
    .dash-table tbody tr { border-bottom: 1px solid #f5f5f5; }
    .dash-table tbody tr:last-child { border: none; }

    /* ── RESPONSIVE TWEAKS ── */
    @media (max-width: 575.98px) {
        .kpi-card { padding: 0.75rem !important; }
        .kpi-icon { width: 42px; height: 42px; font-size: 1.1rem; }
        .kpi-card h4 { font-size: 1.3rem; }
        .kpi-card p, .kpi-card small { font-size: .65rem !important; }

        .chart-card .card-header { padding: 0.75rem 1rem; }
        .chart-card .card-header h6 { font-size: .78rem; }

        .alert-item { padding: 8px 10px; gap: 8px; }
        .alert-item .fw-semibold { font-size: .78rem !important; }
        .alert-item div[style*="font-size:.75rem"] { font-size: .68rem !important; }

        .dash-table thead th { font-size: .65rem; padding: 8px 10px; }
        .dash-table tbody td { font-size: .78rem; padding: 8px 10px; }

        h4.fw-bold { font-size: 1.1rem; }
    }

    @media (max-width: 767.98px) {
        .chart-card .card-body { padding: 1rem !important; }
    }

    /* Contenedor de graficos con altura garantizada */
    .chart-container {
        position: relative;
        width: 100%;
        min-height: 280px;
    }
    .chart-container-donut {
        position: relative;
        width: 100%;
        max-width: 320px;
        min-height: 280px;
        margin: 0 auto;
    }

    /* Tabla scroll horizontal en móvil */
    .table-scroll-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>

{{-- ── NOTIFICATION PANEL ── --}}
<div class="notif-panel" id="notifPanel">
    <div class="d-flex align-items-center justify-content-between px-4 py-3"
         style="border-bottom:1px solid #f0f0f0;">
        <span class="fw-bold" style="font-size:.9rem;">Notificaciones</span>
        <button onclick="document.getElementById('notifPanel').classList.remove('open')"
                style="background:none;border:none;font-size:1.1rem;color:#888;cursor:pointer;">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    @php
        $notificaciones = collect();
        // Vencidos del catálogo
        foreach(\App\Models\Epp::all() as $e) {
            if (!$e->created_at || !$e->vida_util_meses) continue;
            $venc = \Carbon\Carbon::parse($e->created_at)->addMonths($e->vida_util_meses);
            $dias = \Carbon\Carbon::today()->diffInDays($venc, false);
            if ($dias < 0)
                $notificaciones->push(['tipo'=>'vencido','texto'=>$e->nombre,'dias'=>abs((int)$dias),'label'=>'Vencido hace '.abs((int)$dias).' días']);
            elseif ($dias <= 30)
                $notificaciones->push(['tipo'=>'proximo','texto'=>$e->nombre,'dias'=>(int)$dias,'label'=>'Vence en '.(int)$dias.' día(s)']);
        }
        // Stock crítico
        foreach(\App\Models\Epp::where('stock','<=',5)->where('stock','>',0)->get() as $e)
            $notificaciones->push(['tipo'=>'stock','texto'=>$e->nombre,'dias'=>null,'label'=>'Stock crítico: '.$e->stock.' uds.']);
        // Sin stock
        foreach(\App\Models\Epp::where('stock','<=',0)->get() as $e)
            $notificaciones->push(['tipo'=>'sinstock','texto'=>$e->nombre,'dias'=>null,'label'=>'Sin stock']);
        $notificaciones = $notificaciones->sortBy('dias');
    @endphp

    @forelse($notificaciones as $n)
    <div class="notif-item">
        <div class="notif-dot" style="background:{{
            $n['tipo']==='vencido'  ? '#dc3545' :
            ($n['tipo']==='proximo' ? '#fd7e14' :
            ($n['tipo']==='stock'   ? '#ffc107' : '#6c757d'))
        }};"></div>
        <div>
            <div class="fw-semibold" style="font-size:.84rem; line-height:1.3;">{{ $n['texto'] }}</div>
            <div style="font-size:.75rem; color:{{
                $n['tipo']==='vencido'  ? '#dc3545' :
                ($n['tipo']==='proximo' ? '#fd7e14' :
                ($n['tipo']==='stock'   ? '#b08000' : '#888'))
            }};">{{ $n['label'] }}</div>
        </div>
    </div>
    @empty
    <div class="text-center py-4 text-muted small">
        <i class="bi bi-check-circle-fill text-success me-1"></i>Todo en orden, sin alertas.
    </div>
    @endforelse
</div>

<div class="container-fluid pb-4 px-3 px-md-4">

    {{-- ── HEADER ── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111;">Control y Trazabilidad de EPP</h4>
            <p class="text-muted small mb-0">Tecsup Norte — Centro de Seguridad</p>
        </div>
        {{-- Campana de notificaciones --}}
        <button class="notif-btn" id="notifBtn" title="Notificaciones">
            <i class="bi bi-bell-fill" style="font-size:1.4rem;"></i>
            @if($notificaciones->count())
                <span class="badge-dot"></span>
            @endif
        </button>
    </div>

    {{-- ── KPIs ── --}}
    <div class="row g-3 mb-4">

        {{-- Stock Disponible --}}
        <div class="col-6 col-lg-3">
            <div class="card kpi-card shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#e8f5e9;">
                        <i class="bi bi-box2-heart" style="color:#28a745;"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-muted mb-0" style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Stock Disponible</p>
                        <h4 class="fw-bold mb-0" style="color:#28a745;">{{ number_format($stockDisponible) }}</h4>
                        <small class="text-muted" style="font-size:.72rem;">unidades en almacén</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- EPP Entregados --}}
        <div class="col-6 col-lg-3">
            <div class="card kpi-card shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#e8eef7;">
                        <i class="bi bi-hand-thumbs-up" style="color:#003366;"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-muted mb-0" style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">EPP Entregados</p>
                        <h4 class="fw-bold mb-0" style="color:#003366;">{{ number_format($eppEntregados) }}</h4>
                        <small class="text-muted" style="font-size:.72rem;">asignados a personal</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Próximos a Vencer --}}
        <div class="col-6 col-lg-3">
            <div class="card kpi-card shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#fff8e1;">
                        <i class="bi bi-clock-history" style="color:#ffa000;"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-muted mb-0" style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Próximos a Vencer</p>
                        <h4 class="fw-bold mb-0" style="color:#ffa000;">{{ number_format($proximosVencerCatalogo) }}</h4>
                        <small class="text-muted" style="font-size:.72rem;">EPPs vencen en ≤30 días</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Atención Requerida --}}
        <div class="col-6 col-lg-3">
            <div class="card kpi-card shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#fdecea;">
                        <i class="bi bi-exclamation-triangle" style="color:#dc3545;"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-muted mb-0" style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Atención Requerida</p>
                        <h4 class="fw-bold mb-0" style="color:#dc3545;">{{ number_format($vencidosCatalogo + $deteriorados) }}</h4>
                        <small class="text-muted" style="font-size:.72rem;">vencidos + deteriorados</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── CHARTS ROW ── --}}
    <div class="row g-3 mb-4">

        {{-- Dona: EPP por Estado --}}
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card chart-card shadow-sm h-100">
                <div class="card-header">
                    <h6>Estado del Inventario</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="padding:1.5rem;">
                    <div class="chart-container-donut">
                        <canvas id="chartEstado"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barras horizontales: EPP por Departamento --}}
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card chart-card shadow-sm h-100">
                <div class="card-header">
                    <h6>EPP Asignados por Área</h6>
                </div>
                <div class="card-body" style="padding:1.5rem;">
                    <div class="chart-container">
                        <canvas id="chartDepartamento"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Línea: Proyección renovaciones --}}
        <div class="col-12 col-lg-4">
            <div class="card chart-card shadow-sm h-100">
                <div class="card-header">
                    <h6>Proyección Renovaciones (12 meses)</h6>
                </div>
                <div class="card-body" style="padding:1.5rem;">
                    <div class="chart-container">
                        <canvas id="chartRenovaciones"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── ALERTAS CRÍTICAS ── --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card chart-card shadow-sm">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-bell-fill" style="color:#dc3545;"></i>
                    <h6 class="mb-0">Alertas Críticas</h6>
                </div>
                <div class="card-body p-3">

                    @php
                        $eppVencidosCat = \App\Models\Epp::all()->filter(function($e){
                            if(!$e->created_at || !$e->vida_util_meses) return false;
                            return \Carbon\Carbon::parse($e->created_at)->addMonths($e->vida_util_meses)->lt(\Carbon\Carbon::today());
                        });
                        $eppProximosCat = \App\Models\Epp::all()->filter(function($e){
                            if(!$e->created_at || !$e->vida_util_meses) return false;
                            $venc = \Carbon\Carbon::parse($e->created_at)->addMonths($e->vida_util_meses);
                            $dias = \Carbon\Carbon::today()->diffInDays($venc, false);
                            return $dias >= 0 && $dias <= 30;
                        });
                    @endphp

                    @if($eppVencidosCat->isEmpty() && $eppProximosCat->isEmpty() && $alertasStockCritico->isEmpty())
                        <div class="alert alert-success border-0 mb-0">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>¡Todo en orden!</strong> No hay alertas críticas en este momento.
                        </div>
                    @else
                        <div class="row g-2">

                            {{-- EPPs vencidos --}}
                            @foreach($eppVencidosCat->take(4) as $e)
                            <div class="col-12 col-md-6">
                                <div class="alert-item vencido">
                                    <i class="bi bi-x-circle-fill" style="color:#dc3545; font-size:1.1rem; flex-shrink:0;"></i>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-truncate" style="font-size:.84rem;">{{ $e->nombre }}</div>
                                        <div style="font-size:.75rem; color:#dc3545;">
                                            EPP Vencido —
                                            venció el {{ \Carbon\Carbon::parse($e->created_at)->addMonths($e->vida_util_meses)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            {{-- EPPs próximos a vencer --}}
                            @foreach($eppProximosCat->take(4) as $e)
                            @php $diasLeft = (int)\Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($e->created_at)->addMonths($e->vida_util_meses), false); @endphp
                            <div class="col-12 col-md-6">
                                <div class="alert-item">
                                    <i class="bi bi-clock-history" style="color:#fd7e14; font-size:1.1rem; flex-shrink:0;"></i>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-truncate" style="font-size:.84rem;">{{ $e->nombre }}</div>
                                        <div style="font-size:.75rem; color:#fd7e14;">Vence en {{ $diasLeft }} día(s) — {{ \Carbon\Carbon::parse($e->created_at)->addMonths($e->vida_util_meses)->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            {{-- Stock crítico --}}
                            @foreach($alertasStockCritico as $alerta)
                            <div class="col-12 col-md-6">
                                <div class="alert-item stock">
                                    <i class="bi bi-exclamation-triangle-fill" style="color:#ffc107; font-size:1.1rem; flex-shrink:0;"></i>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-truncate" style="font-size:.84rem;">{{ $alerta->nombre }}</div>
                                        <div style="font-size:.75rem; color:#b08000;">Stock crítico — {{ $alerta->stock }} unidades disponibles</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── TABLAS ── --}}
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card chart-card shadow-sm">
                <div class="card-header">
                    <h6>Últimas Entregas</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-scroll-wrapper">
                        <table class="table dash-table mb-0">
                            <thead>
                                <tr>
                                    <th>Personal</th>
                                    <th>EPP</th>
                                    <th>Fecha</th>
                                    <th>Área</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimasEntregas as $entrega)
                                <tr>
                                    <td class="fw-semibold">{{ $entrega->personal->nombre_completo ?? 'N/A' }}</td>
                                    <td>{{ $entrega->epp->nombre ?? 'N/A' }}</td>
                                    <td style="white-space:nowrap;">{{ optional($entrega->fecha_entrega ?? $entrega->created_at)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge rounded-pill" style="background:#e8eef7; color:#003366; font-size:.72rem; white-space:nowrap;">
                                            {{ $entrega->personal->departamento->nombre ?? $entrega->personal->carrera ?? 'Sin área' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-4 fst-italic">Sin registros</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card chart-card shadow-sm">
                <div class="card-header">
                    <h6>EPP en Baja / Deteriorados</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-scroll-wrapper">
                        <table class="table dash-table mb-0">
                            <thead>
                                <tr>
                                    <th>EPP</th>
                                    <th>Cantidad</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($eppBaja as $epp)
                                <tr>
                                    <td class="fw-semibold">{{ $epp->nombre }}</td>
                                    <td>
                                        <span class="badge rounded-pill" style="background:#fdecea; color:#dc3545;">
                                            {{ $epp->deteriorado ?? $epp->cantidad }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(($epp->deteriorado ?? 0) > 0)
                                            <span class="badge rounded-pill" style="background:#fff3cd; color:#664d03;">Deteriorado</span>
                                        @else
                                            <span class="badge rounded-pill" style="background:#e2e3e5; color:#383d41;">{{ ucfirst($epp->estado) }}</span>
                                        @endif
                                    </td>
                                    <td style="white-space:nowrap;">{{ $epp->updated_at ? $epp->updated_at->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-4 fst-italic">Sin registros</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Campana ──
    const btn   = document.getElementById('notifBtn');
    const panel = document.getElementById('notifPanel');
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        panel.classList.toggle('open');
    });
    document.addEventListener('click', function(e) {
        if (!panel.contains(e.target)) panel.classList.remove('open');
    });

    // ── Paleta ──
    const navy   = '#003366';
    const green  = '#28a745';
    const amber  = '#ffc107';
    const red    = '#dc3545';
    const grey   = '#adb5bd';
    const orange = '#fd7e14';

    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#666';

    // ── 1. Dona: Estado ──
    const estadisticas = @json($estadisticasEstado);
    new Chart(document.getElementById('chartEstado'), {
        type: 'doughnut',
        data: {
            labels: ['En Almacén','Entregados','Por Vencer','Vencidos','Deteriorados'],
            datasets: [{
                data: [estadisticas.enAlmacen, estadisticas.entregados, estadisticas.porVencer, estadisticas.vencidos, estadisticas.deteriorados],
                backgroundColor: [green, navy, amber, red, grey],
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 8,
            }]
        },
        options: {
            cutout: '68%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, padding: 14, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed} uds`
                    }
                }
            }
        }
    });

    // ── 2. Barras horizontales: Departamento ──
    const deptData = @json($departamentosData);
    new Chart(document.getElementById('chartDepartamento'), {
        type: 'bar',
        data: {
            labels: deptData.map(d => d.nombre.length > 18 ? d.nombre.substring(0,18)+'…' : d.nombre),
            datasets: [{
                label: 'EPPs asignados',
                data: deptData.map(d => d.cantidad),
                backgroundColor: deptData.map((_, i) => [navy,'#0056b3','#1a73c8',orange,amber][i % 5]),
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.x} EPPs` } }
            },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f0f0f0' }, border: { display: false } },
                y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });

    // ── 3. Área suavizada: Renovaciones ──
    const renovData = @json($renovacionesPorMes);
    new Chart(document.getElementById('chartRenovaciones'), {
        type: 'line',
        data: {
            labels: renovData.map(r => r.mes),
            datasets: [{
                label: 'Renovaciones',
                data: renovData.map(r => r.cantidad),
                borderColor: orange,
                backgroundColor: 'rgba(253,126,20,.12)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.45,
                pointBackgroundColor: orange,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} renovaciones` } }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { beginAtZero: true, grid: { color: '#f0f0f0' }, border: { display: false } }
            }
        }
    });
});
</script>
@endsection