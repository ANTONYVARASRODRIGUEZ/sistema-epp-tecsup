<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Epp;
use App\Models\Personal;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    // ESTE ES EL MÉTODO QUE FALTA
    public function index()
{
    $asignaciones = Asignacion::with(['personal', 'epp'])->orderBy('fecha_entrega', 'desc')->get();
    
    // Cambiamos 'asignaciones.index' por la ruta donde lo guardaste:
    return view('epps.asignaciones', compact('asignaciones')); 
}

    public function store(Request $request)
    {
        // ... (aquí va el código de guardar que ya tenías) ...
        $request->validate([
            'personal_id' => 'required|exists:personals,id',
            'epp_id' => 'required|exists:epps,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $epp = Epp::findOrFail($request->epp_id);

        if ($epp->stock < $request->cantidad) {
            return back()->with('error', 'No hay stock suficiente.');
        }

        Asignacion::create([
            'personal_id' => $request->personal_id,
            'epp_id' => $request->epp_id,
            'cantidad' => $request->cantidad,
            'fecha_entrega' => now(),
            'estado' => 'Posee'
        ]);

        $epp->decrement('stock', $request->cantidad);

        return back()->with('success', 'EPP entregado correctamente.');
    }

    /**
     * Marcar como DEVUELTO (El docente entrega el EPP en buen estado)
     */
    public function devolver($id)
    {
        $asignacion = Asignacion::findOrFail($id);
        
        if ($asignacion->estado == 'Posee') {
            $asignacion->update(['estado' => 'Devuelto']);
            
            // Opcional: Si es devuelto en buen estado, ¿regresa al stock?
            // Para EPPs como cascos sí, para guantes usados quizás no.
            // Por simplicidad y control, lo sumamos al stock.
            $asignacion->epp->increment('stock', $asignacion->cantidad);
        }

        return back()->with('success', 'Equipo marcado como devuelto. Stock actualizado.');
    }

    /**
     * Marcar como DAÑADO/PERDIDO (Baja)
     */
    public function reportarIncidencia($id, Request $request)
    {
        $asignacion = Asignacion::findOrFail($id);
        
        // Si estaba en posesión, cambiamos estado pero NO sumamos al stock (se pierde)
        if ($asignacion->estado == 'Posee') {
            $estado = $request->input('estado', 'Dañado'); // Dañado o Perdido
            $asignacion->update(['estado' => $estado]);
            
            // Incrementamos el contador de deteriorados/bajas en el inventario global
            $asignacion->epp->increment('deteriorado', $asignacion->cantidad);
        }

        return back()->with('warning', 'Equipo marcado como ' . $request->input('estado') . '.');
    }
}