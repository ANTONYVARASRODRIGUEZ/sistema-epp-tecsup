<?php

namespace App\Http\Controllers;

use App\Models\Epp;
use App\Models\Solicitud;
use App\Models\Departamento;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas base de inventario
        $stockDisponible = Epp::sum('stock') ?? 0;
        $totalDeteriorado = Epp::sum('deteriorado') ?? 0;
        $deteriorados = $totalDeteriorado;

        // Flujo de solicitudes aprobadas (entregas reales)
        $solicitudesAprobadasBase = Solicitud::with(['user', 'epp.departamento'])
            ->where('estado', 'aprobado');

        $eppEntregados = (clone $solicitudesAprobadasBase)->count();
        $proximosVencer = (clone $solicitudesAprobadasBase)
            ->whereBetween('fecha_vencimiento', [now(), now()->addDays(30)])
            ->count();
        $vencidos = (clone $solicitudesAprobadasBase)
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', now())
            ->count();

        $alertasVencidos = (clone $solicitudesAprobadasBase)
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', now())
            ->orderBy('fecha_vencimiento')
            ->take(5)
            ->get();

        $alertasStockCritico = Epp::whereBetween('stock', [1, 10])
            ->orderBy('stock')
            ->take(5)
            ->get();

        // Datos por Estado
        $estadisticasEstado = [
            'enAlmacen' => $stockDisponible,
            'entregados' => $eppEntregados,
            'porVencer' => $proximosVencer,
            'vencidos' => $vencidos,
            'deteriorados' => $totalDeteriorado,
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
        $ultimasEntregas = (clone $solicitudesAprobadasBase)
            ->orderByDesc('fecha_aprobacion')
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
