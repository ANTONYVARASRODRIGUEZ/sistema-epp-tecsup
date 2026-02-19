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
        $departamentoSeleccionado = null;
        $personal = [];

        if ($departamentoId) {
            $departamentoSeleccionado = Departamento::find($departamentoId);
            if ($departamentoSeleccionado) {
                // Obtenemos el personal del departamento con sus asignaciones y los datos del EPP
                $personal = Personal::with(['asignaciones.epp'])
                    ->where('departamento_id', $departamentoId)
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
    public function vidaUtil()
    {
        // Obtenemos TODOS los EPPs del catálogo
        $epps = Epp::orderBy('created_at', 'desc')
            ->get()
            ->map(function ($epp) {
                // Calculamos fecha de vencimiento real basada en created_at + vida_util_meses
                $vidaUtil = $epp->vida_util_meses ?? 12;
                $fechaCreacion = Carbon::parse($epp->created_at);
                $fechaVencimiento = $fechaCreacion->copy()->addMonths($vidaUtil);
                
                $epp->fecha_vencimiento = $fechaVencimiento;
                $epp->anio_vencimiento = $fechaVencimiento->year;
                $epp->dias_restantes = now()->diffInDays($fechaVencimiento, false);
                
                return $epp;
            });

        // Agrupamos por año y ordenamos los años
        $proyeccionPorAnio = $epps->groupBy('anio_vencimiento')->sortKeys();

        return view('reportes.vida_util', compact('proyeccionPorAnio'));
    }
}