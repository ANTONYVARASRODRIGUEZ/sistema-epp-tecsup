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

    // Nueva función para actualizar solo el stock
    public function update(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $epp = Epp::findOrFail($id);
        $epp->stock = $request->stock;
        $epp->save();

        return redirect()->back()->with('success', 'Stock de ' . $epp->nombre . ' actualizado correctamente.');
    }
}