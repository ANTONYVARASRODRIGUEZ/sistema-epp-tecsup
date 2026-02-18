<?php

namespace App\Http\Controllers;

use App\Models\Epp;
use App\Models\Departamento;
use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Imports\EppImport;
use Maatwebsite\Excel\Facades\Excel;

class EppController extends Controller
{
    public function index()
    {
        $epps = Epp::with('categoria')->get();
        $categorias = Categoria::all();
        return view('epps.index', compact('epps', 'categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'cantidad' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();

        // Valores por defecto inteligentes
        $data['tipo'] = $request->tipo ?? 'Protección de seguridad';
        $data['stock'] = $request->cantidad ?? 0;
        $data['entregado'] = 0;
        $data['deteriorado'] = 0;

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('epps', 'public');
        }

        Epp::create($data);

        return redirect()->route('epps.index')->with('success', 'EPP registrado correctamente');
    }

    public function update(Request $request, $id)
    {
        $epp = Epp::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('epps', 'public');
        }

        $epp->update($data);
        return redirect()->route('epps.index')->with('success', 'EPP actualizado');
    }

    public function destroy($id)
    {
        Epp::findOrFail($id)->delete();
        return redirect()->route('epps.index')->with('success', 'EPP eliminado');
    }

    public function import(Request $request) 
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new EppImport, $request->file('file'));
            return back()->with('success', '¡Matriz importada!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function clearAll()
{
    // Obtenemos todos los EPP que tienen imagen
    $eppsConImagen = Epp::whereNotNull('imagen')->get();
    
    foreach ($eppsConImagen as $epp) {
        \Storage::disk('public')->delete($epp->imagen);
    }

    // Ahora sí, vaciamos la tabla
    Epp::query()->delete(); 
    
    return back()->with('success', 'Inventario y archivos vaciados correctamente');
}
}