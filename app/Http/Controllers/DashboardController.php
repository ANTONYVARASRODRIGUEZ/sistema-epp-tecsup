<?php

namespace App\Http\Controllers;

use App\Models\Epp;
use App\Models\Asignacion;
use App\Models\Departamento;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $depId = $request->get('departamento_id');

        // Base EPP filtrada opcionalmente por departamento
        $eppBase = Epp::query();
        if ($depId) {
            $eppBase->where('departamento_id', $depId);
        }

        // Stock disponible: estados disponibles o fallback por (cantidad - entregado - deteriorado)
        $noDisponibles = ['Asignado','asignado','Baja','baja','Perdido','perdido','Deteriorado','deteriorado'];
        $disponiblesQuery = (clone $eppBase)
            ->where(function ($q) use ($noDisponibles) {
                $q->whereNull('estado')
                  ->orWhereIn('estado', ['En almacén','en almacén','Disponible','disponible'])
                  ->orWhereNotIn('estado', $noDisponibles);
            });
        $stockDisponible = (clone $disponiblesQuery)->sum('stock');
        if ((int)$stockDisponible === 0) {
            $stockDisponible = (clone $disponiblesQuery)
                ->get(['cantidad','entregado','deteriorado'])
                ->reduce(function ($carry, $e) {
                    $cant = (int)($e->cantidad ?? 0);
                    $ent = (int)($e->entregado ?? 0);
                    $det = (int)($e->deteriorado ?? 0);
                    return $carry + max($cant - $ent - $det, 0);
                }, 0);
        }

        // Asignaciones (entregados) - sumamos cantidad para reflejar unidades entregadas
        $asignacionesBase = Asignacion::with(['personal.departamento', 'epp'])
            ->when($depId, function ($q) use ($depId) {
                $q->whereHas('epp', function ($qq) use ($depId) {
                    $qq->where('departamento_id', $depId);
                });
            });
        $eppEntregados = (clone $asignacionesBase)->sum('cantidad');

        // Vencimientos
        $proximosVencer = (clone $eppBase)
            ->whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [now(), now()->addDays(30)])
            ->count();

        $vencidos = (clone $eppBase)
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', now())
            ->count();

        // Deteriorados: suma de unidades deterioradas
        $totalDeteriorado = (clone $eppBase)->sum('deteriorado') ?? 0;
        $deteriorados = $totalDeteriorado;

        // Alertas
        $alertasVencidos = (clone $eppBase)
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', now())
            ->orderBy('fecha_vencimiento')
            ->take(5)
            ->get(['id', 'nombre', 'fecha_vencimiento']);

        $alertasStockCritico = (clone $eppBase)
            ->whereBetween('stock', [1, 10])
            ->orderBy('stock')
            ->take(5)
            ->get(['id', 'nombre', 'stock']);

        // Datos por Estado para gráfica
        $estadisticasEstado = [
            'enAlmacen' => $stockDisponible,
            'entregados' => $eppEntregados,
            'porVencer' => $proximosVencer,
            'vencidos' => $vencidos,
            'deteriorados' => $totalDeteriorado,
        ];

        // EPP por Departamento (cantidad de EPPs asignados/en uso)
        $departamentosData = Departamento::with(['personals.asignaciones' => function($q) {
                $q->where('estado', 'Entregado');
            }])
            ->get()
            ->map(function($d) {
                $cantidad = $d->personals->sum(function($p) {
                    return $p->asignaciones->sum('cantidad');
                });
                return ['nombre' => $d->nombre, 'cantidad' => $cantidad];
            })
            ->toArray();

        // Últimas entregas reales (Asignaciones)
        $ultimasEntregas = (clone $asignacionesBase)
            ->orderByDesc('fecha_entrega')
            ->take(5)
            ->get();

        // EPP dados de baja/deteriorado/perdido
        $eppBaja = (clone $eppBase)
            ->whereIn('estado', ['deteriorado', 'baja', 'perdido', 'Deteriorado', 'Baja', 'Perdido'])
            ->latest()
            ->take(5)
            ->get();

        // Vencimientos proyectados (próximos 6 meses)
        $renovacionesPorMes = [];
        for ($i = 0; $i < 6; $i++) {
            $mes = now()->addMonths($i);
            $count = (clone $eppBase)
                ->whereBetween('fecha_vencimiento', [
                    $mes->copy()->startOfMonth(),
                    $mes->copy()->endOfMonth(),
                ])->count();
            $renovacionesPorMes[] = [
                'mes' => $mes->format('M'),
                'cantidad' => $count,
            ];
        }

        return view('dashboard', compact(
            'stockDisponible',
            'eppEntregados',
            'proximosVencer',
            'deteriorados',
            'vencidos',
            'estadisticasEstado',
            'departamentosData',
            'ultimasEntregas',
            'eppBaja',
            'renovacionesPorMes',
            'alertasVencidos',
            'alertasStockCritico'
        ));
    }
}
