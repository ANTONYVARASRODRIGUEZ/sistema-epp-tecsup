<?php

namespace App\Http\Controllers;

use App\Models\Epp;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index()
    {
        $epps = Epp::all();
        return view('inventario.index', compact('epps'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cantidad'     => 'required|integer|min:0',
            'tipo_ajuste_' . $id => 'required|in:sumar,fijar',
        ]);

        $epp        = Epp::findOrFail($id);
        $cantidad   = (int) $request->cantidad;
        $tipo       = $request->input('tipo_ajuste_' . $id);

        switch ($tipo) {
            case 'sumar':
                $epp->stock = ($epp->stock ?? 0) + $cantidad;
                $msg = "Se sumaron {$cantidad} unidades al stock de {$epp->nombre}. Total: {$epp->stock}";
                break;
            case 'fijar':
            default:
                $epp->stock = $cantidad;
                $msg = "Stock de {$epp->nombre} fijado en {$cantidad} unidades.";
                break;
        }

        $epp->save();

        return redirect()->back()->with('success', $msg);
    }
}