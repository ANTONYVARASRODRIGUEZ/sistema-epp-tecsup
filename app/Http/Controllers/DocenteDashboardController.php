<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocenteDashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        // LÓGICA DE AUTOSERVICIO: Si no tiene departamento, mostrar selección
        if (!$user->departamento_id) {
            $departamentos = Departamento::all();
            return view('docente.seleccionar-departamento', compact('departamentos', 'user'));
        }

        // LÓGICA NORMAL DEL DASHBOARD
        $misSolicitudes = Solicitud::with('epp')
            ->where('user_id', $user->id)
            ->get();

        $eppAprobados = $misSolicitudes->where('estado', 'aprobado');
        $totalAsignados = $eppAprobados->count();

        $vencidos = $eppAprobados->filter(function ($solicitud) {
            return optional($solicitud->fecha_vencimiento)?->isPast();
        })->count();

        $proximosAVencer = $eppAprobados->filter(function ($solicitud) {
            if (!$solicitud->fecha_vencimiento) {
                return false;
            }
            return $solicitud->fecha_vencimiento->isBetween(now(), now()->copy()->addDays(30));
        })->count();

        $solicitudesPendientes = $misSolicitudes->where('estado', 'pendiente')->count();

        $ultimosEpp = $eppAprobados->sortByDesc('fecha_aprobacion')->take(3);

        return view('docente.dashboard', compact(
            'user',
            'totalAsignados',
            'proximosAVencer',
            'vencidos',
            'solicitudesPendientes',
            'ultimosEpp'
        ));
    }

    // Nuevo método para procesar la unión
    public function unirse(Request $request)
    {
        $request->validate([
            'departamento_id' => 'required|exists:departamentos,id',
            'talla_zapatos' => 'required|string',
            'talla_mandil' => 'required|string',
        ]);

        $user = Auth::user();
        $user->update([
            'departamento_id' => $request->departamento_id,
            'talla_zapatos' => $request->talla_zapatos,
            'talla_mandil' => $request->talla_mandil,
        ]);

        return redirect()->route('docente.dashboard')->with('success', 'Te has unido al departamento correctamente.');
    }
}