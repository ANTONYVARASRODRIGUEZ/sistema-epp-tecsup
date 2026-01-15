<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;

class DocenteDashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

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
}
