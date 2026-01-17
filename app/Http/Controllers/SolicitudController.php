<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud; // Aseguramos que el modelo esté cargado
use App\Models\Epp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class SolicitudController extends Controller
{
    /**
     * Muestra la lista de solicitudes para el Admin (Cambios de tu compañera + Datos reales)
     */
    public function index(Request $request)
    {
        $estadoFiltro = $request->get('estado', 'todos');
        $tipoFiltro = $request->get('tipo', 'todos');

        $solicitudesQuery = Solicitud::with(['user', 'epp'])->orderBy('created_at', 'desc');

        if ($estadoFiltro !== 'todos') {
            $solicitudesQuery->where('estado', $estadoFiltro);
        }

        $solicitudes = $solicitudesQuery->get();

        if ($tipoFiltro !== 'todos') {
            $solicitudes = $solicitudes->filter(function ($solicitud) use ($tipoFiltro) {
                return $tipoFiltro === 'solicitud_epp';
            })->values();
        }

        $tiposDisponibles = [
            'solicitud_epp' => 'Solicitud de EPP'
        ];

        // Contadores reales basados en tu base de datos
        $pendientes = Solicitud::where('estado', 'pendiente')->count();
        $aprobadas = Solicitud::where('estado', 'aprobado')->count();
        $rechazadas = Solicitud::where('estado', 'rechazado')->count();

        return view('solicitudes.index', compact(
            'solicitudes',
            'pendientes',
            'aprobadas',
            'rechazadas',
            'estadoFiltro',
            'tipoFiltro',
            'tiposDisponibles'
        ));
    }

    /**
     * Guarda la solicitud del docente (Tu lógica real)
     */
    public function store(Request $request)
    {
        $request->validate([
            'epp_id' => 'required|exists:epps,id',
            'motivo' => 'required|string|max:500',
            'cantidad' => 'nullable|integer|min:1|max:100'
        ]);

        Solicitud::create([
            'user_id' => Auth::id(),
            'epp_id' => $request->epp_id,
            'motivo' => $request->motivo,
            'cantidad' => $request->cantidad ?? 1,
            'estado' => 'pendiente' // Aseguramos que inicie en pendiente
        ]);

        return back()->with('success', '¡Solicitud enviada correctamente!');
    }

    /**
     * Vista del docente con sus solicitudes
     */
    public function misSolicitudes()
    {
        $misSolicitudes = Solicitud::with('epp')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('docente.mis-solicitudes', compact('misSolicitudes'));
    }

    /**
     * EPP aprobados para el docente
     */
    public function misEpps()
    {
        $misEpps = Solicitud::with('epp')
            ->where('user_id', Auth::id())
            ->where('estado', 'aprobado')
            ->orderByDesc('fecha_aprobacion')
            ->get();

        return view('docente.mis-epp', compact('misEpps'));
    }

    /**
     * Aprobar una solicitud (Lógica real añadida a la estructura de tu compañera)
     */
    public function aprobar($id)
    {
        $solicitud = Solicitud::with('epp')->findOrFail($id);
        $fechaAprobacion = Carbon::now();

        $vidaUtilMeses = $solicitud->epp->vida_util_meses ?? 12;
        $fechaVencimiento = (clone $fechaAprobacion)->addMonths($vidaUtilMeses);

        $solicitud->update([
            'estado' => 'aprobado',
            'fecha_aprobacion' => $fechaAprobacion,
            'fecha_vencimiento' => $fechaVencimiento,
        ]);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud aprobada con éxito');
    }

    /**
     * Rechazar una solicitud
     */
    public function rechazar($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->update(['estado' => 'rechazado']);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud rechazada');
    }

    // --- Métodos de estructura de tu compañera (puedes completarlos luego si los necesitan) ---
    
    public function create() { return view('solicitudes.create'); }

    public function show($id) { 
        $solicitud = Solicitud::with(['user', 'epp'])->findOrFail($id);
        return view('solicitudes.show', compact('solicitud')); 
    }

    public function edit($id) { return view('solicitudes.edit'); }

    public function destroy($id) {
        Solicitud::findOrFail($id)->delete();
        return redirect()->route('solicitudes.index')->with('success', 'Solicitud eliminada');
    }
}