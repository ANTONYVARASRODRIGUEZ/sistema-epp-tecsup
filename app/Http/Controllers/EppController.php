<?php

namespace App\Http\Controllers;

use App\Models\Epp;
use App\Models\Departamento;
use App\Models\Categoria; // <--- IMPORTANTE: Añadimos esto
use Illuminate\Http\Request;
use App\Imports\EppImport;
use Maatwebsite\Excel\Facades\Excel;

class EppController extends Controller
{
    /**
     * Display a listing of the resource (CATÁLOGO PRINCIPAL).
     */
    public function index()
{
    $epps = Epp::with('categoria')->get();
    $categorias = Categoria::all(); // Esto es vital para los botones de filtro
    return view('epps.index', compact('epps', 'categorias'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departamentos = Departamento::all();
        $categorias = Categoria::all();
        return view('epps.create', compact('departamentos', 'categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id', // Validamos la categoría
            'descripcion' => 'nullable|string',
            'vida_util_meses' => 'required|integer|min:1',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'ficha_tecnica' => 'nullable|mimes:pdf|max:2048',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'precio' => 'nullable|numeric|min:0',
            'cantidad' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();

        // Guardar archivo PDF si existe
        if ($request->hasFile('ficha_tecnica')) {
            $data['ficha_tecnica'] = $request->file('ficha_tecnica')->store('fichas_tecnicas', 'public');
        }

        // Guardar imagen si existe
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('epps', 'public');
        }

        Epp::create($data);

        return redirect()->route('epps.index')->with('success', 'EPP registrado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $epp = Epp::with('categoria')->findOrFail($id);
        return view('epps.show', compact('epp'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $epp = Epp::findOrFail($id);
        $departamentos = Departamento::all();
        $categorias = Categoria::all();
        return view('epps.edit', compact('epp', 'departamentos', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $epp = Epp::findOrFail($id);

        $data = $request->all();

        if ($request->hasFile('ficha_tecnica')) {
            $data['ficha_tecnica'] = $request->file('ficha_tecnica')->store('fichas_tecnicas', 'public');
        }

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('epps', 'public');
        }

        $epp->update($data);

        return redirect()->route('epps.index')->with('success', 'EPP actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $epp = Epp::findOrFail($id);
        $epp->delete();

        return redirect()->route('epps.index')->with('success', 'EPP eliminado correctamente');
    }

    /**
     * Import masivo desde Excel.
     */
    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new EppImport, $request->file('file'));
            return back()->with('success', '¡Matriz de EPP importada correctamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error en el formato del archivo: ' . $e->getMessage());
        }
    }


    public function clearAll()
{
    try {
        // Opción A: Vacía la tabla y reinicia el contador de IDs (Recomendado)
        \App\Models\Epp::truncate(); 
        
        return back()->with('success', '¡Se han eliminado todos los registros del inventario!');
    } catch (\Exception $e) {
        return back()->with('error', 'No se pudo vaciar la tabla: ' . $e->getMessage());
    }
}
}