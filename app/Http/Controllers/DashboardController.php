<?php

namespace App\Http\Controllers;

use App\Models\Epp;
use App\Models\Entrega;
use App\Models\Departamento;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Stock Disponible
        $stockDisponible = Epp::sum('cantidad') ?? 0;

        // EPP Entregados
        $eppEntregados = Entrega::count() ?? 0;

        // EPP Próximos a Vencer (30 días)
        $proximosVencer = Epp::whereBetween('fecha_vencimiento', [now(), now()->addDays(30)])->count() ?? 0;

        // EPP Deteriorados/Baja
        $deteriorados = Epp::whereIn('estado', ['deteriorado', 'baja'])->count() ?? 0;

        // EPP Vencidos
        $vencidos = Epp::where('fecha_vencimiento', '<', now())->count() ?? 0;

        // Alertas: EPP Vencidos
        $alertasVencidos = Epp::where('fecha_vencimiento', '<', now())->get();

        // Alertas: Stock Crítico (menos de 20 unidades)
        $alertasStockCritico = Epp::where('cantidad', '<', 20)->where('cantidad', '>', 0)->get();

        // Datos por Estado
        $estadisticasEstado = [
            'enAlmacen' => $stockDisponible,
            'entregados' => $eppEntregados,
            'porVencer' => $proximosVencer,
            'vencidos' => $vencidos,
            'deteriorados' => $deteriorados,
        ];

        // EPP por Departamento
        $eppPorDepartamento = Departamento::with('epps')->get();
        $departamentosData = $eppPorDepartamento->map(function($dept) {
            return [
                'nombre' => $dept->nombre,
                'cantidad' => $dept->epps->count()
            ];
        })->toArray();

        // Si está vacío, usar datos ficticios
        if(empty($departamentosData)) {
            $departamentosData = [
                ['nombre' => 'Mecánica', 'cantidad' => 0],
                ['nombre' => 'Topografía', 'cantidad' => 0],
                ['nombre' => 'Tecnología Digital', 'cantidad' => 0],
                ['nombre' => 'Construcción', 'cantidad' => 0],
            ];
        }

        // Últimas entregas
        $ultimasEntregas = Entrega::with(['user', 'epp'])
            ->latest()
            ->take(5)
            ->get();

        // EPP dados de baja (estado deteriorado o baja)
        $eppBaja = Epp::whereIn('estado', ['deteriorado', 'baja'])
            ->latest()
            ->take(5)
            ->get();

        // Renovaciones proyectadas por mes (próximos 6 meses)
        $renovacionesPorMes = [];
        for ($i = 0; $i < 6; $i++) {
            $mes = now()->addMonths($i);
            $count = Epp::whereBetween('fecha_vencimiento', [
                $mes->startOfMonth(),
                $mes->endOfMonth()
            ])->count();
            $renovacionesPorMes[] = [
                'mes' => $mes->format('M'),
                'cantidad' => $count
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
