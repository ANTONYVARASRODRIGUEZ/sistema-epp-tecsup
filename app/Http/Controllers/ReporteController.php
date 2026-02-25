<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Epp;
use App\Models\Departamento;
use App\Models\Personal;
use App\Models\Asignacion;
use Carbon\Carbon;

class ReporteController extends Controller
{
    /**
     * Muestra el menú principal de reportes.
     */
    public function index()
    {
        return view('reportes.index');
    }

    /**
     * Genera el reporte de stock actual de todos los EPPs.
     */
    public function stock()
    {
        $epps = Epp::orderBy('nombre')->get();
        return view('reportes.stock', compact('epps'));
    }

    /**
     * Genera el reporte de asignaciones filtrado por departamento.
     */
    public function porDepartamento(Request $request)
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $departamentoId = $request->input('departamento_id');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $departamentoSeleccionado = null;
        $personal = [];

        if ($departamentoId) {
            $departamentoSeleccionado = Departamento::find($departamentoId);
            if ($departamentoSeleccionado) {
                // Obtenemos el personal del departamento con sus asignaciones y los datos del EPP,
                // aplicando también el filtro de fecha si está presente.
                $personal = Personal::with(['asignaciones.epp'])
                    ->where('departamento_id', $departamentoId)
                    ->when($fechaInicio, function ($query, $fechaInicio) {
                        return $query->whereHas('asignaciones', function ($q) use ($fechaInicio) {
                            $q->where('fecha_entrega', '>=', $fechaInicio);
                        });
                    })
                    ->when($fechaFin, function ($query, $fechaFin) {
                        return $query->whereHas('asignaciones', function ($q) use ($fechaFin) {
                            $q->where('fecha_entrega', '<=', $fechaFin);
                        });
                    })
                    ->orderBy('nombre_completo')
                    ->get();
            }
        }

        return view('reportes.departamento', compact('departamentos', 'personal', 'departamentoSeleccionado'));
    }

    /**
     * Genera el reporte de EPPs dados de baja (dañados, perdidos, etc.).
     */
    public function incidencias()
    {
        $incidencias = Asignacion::with(['personal.departamento', 'epp'])
            ->whereIn('estado', ['Dañado', 'Perdido', 'Baja'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('reportes.incidencias', compact('incidencias'));
    }

    /**
     * Genera la proyección de vencimientos basada en la vida útil.
     * Muestra todos los EPPs del catálogo, no solo los asignados.
     */
    public function vidaUtil(Request $request)
    {
        $search = $request->input('search');

        // Obtenemos las ASIGNACIONES activas (Entregadas) para proyectar su renovación
        $query = Asignacion::with(['epp', 'personal.departamento'])
            ->where('estado', 'Entregado');

        if ($search) {
            $query->whereHas('personal', fn($q) => $q->where('nombre_completo', 'like', "%$search%")->orWhere('dni', 'like', "%$search%"));
        }

        $asignaciones = $query
            ->get()
            ->map(function ($asignacion) {
                // Calculamos fecha de vencimiento real: Fecha Entrega + Vida Útil del EPP
                $vidaUtil = $asignacion->epp->vida_util_meses ?? 12;
                $fechaEntrega = Carbon::parse($asignacion->fecha_entrega);
                $fechaVencimiento = $fechaEntrega->copy()->addMonths($vidaUtil);
                
                $asignacion->fecha_vencimiento = $fechaVencimiento;
                $asignacion->anio_vencimiento = $fechaVencimiento->year;
                $asignacion->dias_restantes = now()->diffInDays($fechaVencimiento, false);
                
                return $asignacion;
            });

        // Agrupamos por año y ordenamos los años
        $proyeccionPorAnio = $asignaciones->groupBy('anio_vencimiento')->sortKeys();

        return view('reportes.vida_util', compact('proyeccionPorAnio', 'search'));
    }
}