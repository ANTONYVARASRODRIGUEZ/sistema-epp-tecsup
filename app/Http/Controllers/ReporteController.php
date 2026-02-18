<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Epp;
use App\Models\Departamento;
use App\Models\Personal;
use App\Models\Asignacion;

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
     * Genera el reporte de planificación de vida útil a largo plazo.
     */
    public function vidaUtil()
    {
        // 1. Obtenemos solo los EPP que tienen fecha de vencimiento calculada
        $eppsPorAnio = Epp::whereNotNull('fecha_vencimiento')
            ->orderBy('fecha_vencimiento', 'asc')
            ->get()
            ->groupBy(function($epp) {
                // 2. Agrupamos por el AÑO de vencimiento (ej: "2026", "2027")
                return \Carbon\Carbon::parse($epp->fecha_vencimiento)->format('Y');
            });

        // 3. Enviamos los datos a la vista
        return view('reportes.vida_util', compact('eppsPorAnio'));
    }
}