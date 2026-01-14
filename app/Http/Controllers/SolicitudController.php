<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud; // Aseguramos que el modelo esté cargado
use App\Models\Epp;
use Illuminate\Support\Facades\Auth;

class SolicitudController extends Controller
{
    /**
     * Muestra la lista de solicitudes para el Admin (Cambios de tu compañera + Datos reales)
     */
    public function index()
    {
        // Traemos las solicitudes reales de la base de datos
        // Usamos with(['user', 'epp']) para cargar los nombres del docente y el equipo
        $solicitudes = Solicitud::with(['user', 'epp'])->orderBy('created_at', 'desc')->get();

        // Contadores reales basados en tu base de datos
        $pendientes = Solicitud::where('estado', 'pendiente')->count();
        $aprobadas = Solicitud::where('estado', 'aprobado')->count();
        $rechazadas = Solicitud::where('estado', 'rechazado')->count();

        return view('solicitudes.index', compact('solicitudes', 'pendientes', 'aprobadas', 'rechazadas'));
    }

    /**
     * Guarda la solicitud del docente (Tu lógica real)
     */
    public function store(Request $request)
    {
        $request->validate([
            'epp_id' => 'required|exists:epps,id',
            'motivo' => 'required|string|max:500'
        ]);

        Solicitud::create([
            'user_id' => Auth::id(),
            'epp_id' => $request->epp_id,
            'motivo' => $request->motivo,
            'estado' => 'pendiente' // Aseguramos que inicie en pendiente
        ]);

        return back()->with('success', '¡Solicitud enviada correctamente!');
    }

    /**
     * Aprobar una solicitud (Lógica real añadida a la estructura de tu compañera)
     */
    public function aprobar($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->update(['estado' => 'aprobado']);

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