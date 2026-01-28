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
}