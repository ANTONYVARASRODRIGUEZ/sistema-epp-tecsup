<?php

namespace App\Http\Controllers;

use App\Models\Epp;
use App\Models\Asignacion;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $depId = $request->get('departamento_id');

        // 1. ANÁLISIS DE INVENTARIO (ALMACÉN)
        // Obtenemos EPPs con stock positivo para verificar si están vencidos o vigentes
        $eppBase = Epp::query();
        if ($depId) {
            $eppBase->where('departamento_id', $depId);
        }

        // Traemos todo el stock > 0 para clasificarlo
        $eppsEnAlmacen = (clone $eppBase)->where('stock', '>', 0)->get();
        
        $stockDisponibleReal = 0;
        $stockVencidoEnAlmacen = 0;
        $stockPorVencerEnAlmacen = 0;
        $hoy = Carbon::now()->startOfDay(); // Usamos startOfDay para comparar fechas puras

        foreach ($eppsEnAlmacen as $epp) {
            $fechaVenc = $epp->fecha_vencimiento ? Carbon::parse($epp->fecha_vencimiento)->startOfDay() : null;
            
            if ($fechaVenc) {
                if ($fechaVenc->lt($hoy)) {
                    $stockVencidoEnAlmacen += $epp->stock;
                } elseif ($fechaVenc->diffInDays($hoy) <= 30) {
                    $stockPorVencerEnAlmacen += $epp->stock;
                } else {
                    $stockDisponibleReal += $epp->stock;
                }
            } else {
                $stockDisponibleReal += $epp->stock;
            }
        }

        // Variable para la vista (Stock Vigente)
        $stockDisponible = $stockDisponibleReal;

        // 2. ANÁLISIS DE ASIGNACIONES (EN USO POR PERSONAL)
        $asignacionesBase = Asignacion::with(['personal.departamento', 'epp'])
            ->where('estado', 'Entregado') // Solo contamos los activos
            ->when($depId, function ($q) use ($depId) {
                $q->whereHas('epp', function ($qq) use ($depId) {
                    $qq->where('departamento_id', $depId);
                });
            });
            
        $eppEntregados = (clone $asignacionesBase)->sum('cantidad');

        // 3. CÁLCULO DE VENCIMIENTOS DE ASIGNACIONES (RENOVACIONES)
        // Obtenemos las asignaciones para procesar sus fechas
        $asignacionesParaVencimiento = (clone $asignacionesBase)->get();
        
        $asignacionesProcesadas = $asignacionesParaVencimiento->map(function($asignacion) {
            // Vida útil: Si no existe en BD, asumimos 12 meses (1 año)
            // Si tus zapatos duran 3 años, asegúrate que en la BD el EPP tenga vida_util_meses = 36
            $vidaUtil = $asignacion->epp->vida_util_meses ?? 12; 
            
            $fechaEntrega = Carbon::parse($asignacion->fecha_entrega);
            $fechaVencimiento = $fechaEntrega->copy()->addMonths($vidaUtil);
            
            $asignacion->fecha_vencimiento = $fechaVencimiento;
            // diffInDays con false devuelve negativo si ya pasó la fecha
            $asignacion->dias_restantes = Carbon::now()->startOfDay()->diffInDays($fechaVencimiento, false); 
            
            return $asignacion;
        });

        // KPI: Próximos a vencer (Próximos 30 días)
        $asignacionesPorVencer = $asignacionesProcesadas->filter(function($a) {
            return $a->dias_restantes >= 0 && $a->dias_restantes <= 30;
        })->sum('cantidad');

        // KPI TOTAL POR VENCER (Stock + Asignaciones)
        $proximosVencer = $asignacionesPorVencer + $stockPorVencerEnAlmacen;

        // KPI: Asignaciones Vencidas (Ya expiró su vida útil)
        $asignacionesVencidas = $asignacionesProcesadas->filter(function($a) {
            return $a->dias_restantes < 0;
        })->sum('cantidad');

        // KPI TOTAL VENCIDOS: (Stock Vencido en Almacén + Asignaciones Vencidas en Campo)
        $vencidos = $stockVencidoEnAlmacen + $asignacionesVencidas;

        // KPI: Deteriorados (Stock)
        $totalDeteriorado = (clone $eppBase)->sum('deteriorado') ?? 0;
        $deteriorados = $totalDeteriorado;

        // Alertas de Stock Crítico (Mantenemos esto)
        $alertasStockCritico = (clone $eppBase)
            ->whereBetween('stock', [1, 10])
            ->orderBy('stock')
            ->take(5)
            ->get(['id', 'nombre', 'stock']);
            
        // Alertas Vencidos (Ahora usamos la lista detallada, dejamos esto vacío para no duplicar lógica visual antigua)
        $alertasVencidos = collect(); 

        // Cálculo para la gráfica: Entregados Vigentes (Total Entregado - Asignaciones Vencidas - Asignaciones Por Vencer)
        // Esto evita que se dupliquen datos en la gráfica de pastel
        $entregadosVigentes = max(0, $eppEntregados - $asignacionesVencidas - $asignacionesPorVencer);

        // Datos por Estado para gráfica
        $estadisticasEstado = [
            'enAlmacen' => $stockDisponible,
            'entregados' => $entregadosVigentes,
            'porVencer' => $proximosVencer,
            'vencidos' => $vencidos,
            'deteriorados' => $totalDeteriorado,
        ];

        // Para la tarjeta de "Stock Disponible", el valor debe incluir el stock que está por vencer, ya que aún se puede asignar.
        $stockDisponible = $stockDisponibleReal + $stockPorVencerEnAlmacen;

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

        // EPP Baja (Incluye items con conteo de deteriorados > 0 o estado de baja)
        $eppBaja = (clone $eppBase)
            ->where(function($q) {
                $q->where('deteriorado', '>', 0)
                  ->orWhereIn('estado', ['deteriorado', 'baja', 'perdido', 'Deteriorado', 'Baja', 'Perdido']);
            })
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        // 4. Gráfica: Proyección de Renovaciones (Próximos 12 meses)
        // Se extiende la proyección a 12 meses para una mejor planificación a largo plazo.
        $renovacionesPorMes = [];
        // Cambiamos de 6 a 12 para tener una vista anual
        for ($i = 0; $i < 12; $i++) {
            $mes = now()->addMonths($i);
            $cantidad = $asignacionesProcesadas->filter(function($a) use ($mes) {
                return $a->fecha_vencimiento->format('Y-m') === $mes->format('Y-m');
            })->sum('cantidad');
            
            $renovacionesPorMes[] = [
                'mes' => $mes->format('M'),
                'cantidad' => $cantidad,
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
