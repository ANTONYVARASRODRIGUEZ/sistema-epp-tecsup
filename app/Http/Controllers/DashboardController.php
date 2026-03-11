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
        $hoy   = Carbon::today();
        $depId = $request->get('departamento_id');

        // ── 1. STOCK (almacén) ──────────────────────────────────────────
        $eppQuery = Epp::query();
        if ($depId) $eppQuery->where('departamento_id', $depId);

        $stockDisponible = (clone $eppQuery)->sum('stock') ?? 0;

        // ── 2. EPP ENTREGADOS (asignaciones activas) ────────────────────
        $asignBase = Asignacion::with(['personal.departamento', 'epp'])
            ->where('estado', 'Entregado')
            ->when($depId, fn($q) => $q->whereHas('epp', fn($qq) => $qq->where('departamento_id', $depId)));

        $eppEntregados = (clone $asignBase)->sum('cantidad');

        // ── 3. VENCIMIENTO BASADO EN CATÁLOGO ──────────────────────────
        // La fecha de vencimiento = created_at + vida_util_meses  (igual que en las tarjetas)
        $todosEpps = (clone $eppQuery)->get();

        $proximosVencerCatalogo = 0;
        $vencidosCatalogo       = 0;

        foreach ($todosEpps as $epp) {
            if (!$epp->created_at || !$epp->vida_util_meses) continue;

            $fechaVenc  = Carbon::parse($epp->created_at)->addMonths($epp->vida_util_meses);
            $diasHastaVenc = $hoy->diffInDays($fechaVenc, false); // negativo = ya venció

            if ($diasHastaVenc < 0) {
                $vencidosCatalogo++;
            } elseif ($diasHastaVenc <= 30) {
                $proximosVencerCatalogo++;
            }
        }

        // ── 4. DETERIORADOS ────────────────────────────────────────────
        $deteriorados = (clone $eppQuery)->sum('deteriorado') ?? 0;

        // ── 5. ALERTAS STOCK CRÍTICO (1–5 uds) ────────────────────────
        $alertasStockCritico = (clone $eppQuery)
            ->whereBetween('stock', [1, 5])
            ->orderBy('stock')
            ->take(6)
            ->get(['id', 'nombre', 'stock']);

        $alertasVencidos = collect(); // mantenido por compatibilidad con la vista

        // ── 6. ESTADÍSTICAS ESTADO para gráfica ────────────────────────
        // Contamos EPPs del catálogo en cada estado
        $enAlmacen   = 0;
        $porVencerGraf = 0;
        $vencidosGraf  = 0;

        foreach ($todosEpps as $epp) {
            if (!$epp->created_at || !$epp->vida_util_meses) { $enAlmacen++; continue; }
            $venc = Carbon::parse($epp->created_at)->addMonths($epp->vida_util_meses);
            $dias = $hoy->diffInDays($venc, false);
            if ($dias < 0)       $vencidosGraf++;
            elseif ($dias <= 30) $porVencerGraf++;
            else                 $enAlmacen++;
        }

        $estadisticasEstado = [
            'enAlmacen'   => $enAlmacen,
            'entregados'  => $eppEntregados,
            'porVencer'   => $porVencerGraf,
            'vencidos'    => $vencidosGraf,
            'deteriorados'=> $deteriorados,
        ];

        // ── 7. EPP POR DEPARTAMENTO ────────────────────────────────────
        $departamentosData = Departamento::with(['personals.asignaciones' => fn($q) => $q->where('estado', 'Entregado')])
            ->get()
            ->map(fn($d) => [
                'nombre'   => $d->nombre,
                'cantidad' => $d->personals->sum(fn($p) => $p->asignaciones->sum('cantidad')),
            ])
            ->toArray();

        // ── 8. ÚLTIMAS ENTREGAS ────────────────────────────────────────
        $ultimasEntregas = (clone $asignBase)
            ->orderByDesc('fecha_entrega')
            ->take(5)
            ->get();

        // ── 9. EPP EN BAJA ─────────────────────────────────────────────
        $eppBaja = (clone $eppQuery)
            ->where(fn($q) => $q->where('deteriorado', '>', 0)
                ->orWhereIn('estado', ['deteriorado', 'baja', 'perdido', 'Deteriorado', 'Baja', 'Perdido']))
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        // ── 10. RENOVACIONES POR MES (proyección asignaciones) ─────────
        $asignaciones = (clone $asignBase)->get()->map(function ($a) {
            $vida  = $a->epp->vida_util_meses ?? 12;
            $venc  = Carbon::parse($a->fecha_entrega)->addMonths($vida);
            $a->fecha_vencimiento = $venc;
            return $a;
        });

        $renovacionesPorMes = [];
        for ($i = 0; $i < 12; $i++) {
            $mes = now()->addMonths($i);
            $renovacionesPorMes[] = [
                'mes'      => $mes->locale('es')->isoFormat('MMM'),
                'cantidad' => $asignaciones->filter(
                    fn($a) => $a->fecha_vencimiento->format('Y-m') === $mes->format('Y-m')
                )->sum('cantidad'),
            ];
        }

        return view('dashboard', compact(
            'stockDisponible',
            'eppEntregados',
            'proximosVencerCatalogo',
            'vencidosCatalogo',
            'deteriorados',
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