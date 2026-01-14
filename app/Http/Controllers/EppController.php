<?php

namespace App\Http\Controllers;

use App\Models\Epp;
use Illuminate\Http\Request;

class EppController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $epps = Epp::all();
        return view('epps.index', compact('epps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('epps.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // 1️⃣ Validación
    $request->validate([
        'nombre' => 'required|string|max:255',
        'tipo' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'vida_util_meses' => 'required|integer|min:1',
        'ficha_tecnica' => 'nullable|mimes:pdf|max:2048',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'frecuencia_entrega' => 'nullable|string|max:255',
        'codigo_logistica' => 'nullable|string|max:255',
        'marca_modelo' => 'nullable|string|max:255',
        'precio' => 'nullable|numeric|min:0',
        'cantidad' => 'nullable|integer|min:0'
    ]);

    // 2️⃣ Datos del formulario
    $data = $request->only([
        'nombre',
        'tipo',
        'descripcion',
        'vida_util_meses',
        'frecuencia_entrega',
        'codigo_logistica',
        'marca_modelo',
        'precio',
        'cantidad'
    ]);

    // 3️⃣ Guardar archivo PDF si existe
    if ($request->hasFile('ficha_tecnica')) {
        $data['ficha_tecnica'] = $request->file('ficha_tecnica')
            ->store('fichas_tecnicas', 'public');
    }

    // 4️⃣ Guardar imagen si existe
    if ($request->hasFile('imagen')) {
        $data['imagen'] = $request->file('imagen')
            ->store('epps', 'public');
    }

    // 5️⃣ Crear EPP
    Epp::create($data);

    // 6️⃣ Redireccionar
    return redirect()->route('epps.index')
        ->with('success', 'EPP registrado correctamente');
}


    public function catalogo()
{
    // Obtenemos los mismos datos para ambos
    $epps = \App\Models\Epp::all();

    // Si el usuario es docente, mostramos la vista de solicitudes
    if (str_contains(auth()->user()->email, 'docente')) {
        return view('docente.catalogo', compact('epps'));
    }

    // Si es admin, mostramos la vista original de gestión
    return view('epps.catalogo', compact('epps'));
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $epp = Epp::findOrFail($id);
        return view('epps.edit', compact('epp'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $epp = Epp::findOrFail($id);

        // Validación
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'vida_util_meses' => 'required|integer|min:1',
            'ficha_tecnica' => 'nullable|mimes:pdf|max:2048',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'frecuencia_entrega' => 'nullable|string|max:255',
            'codigo_logistica' => 'nullable|string|max:255',
            'marca_modelo' => 'nullable|string|max:255',
            'precio' => 'nullable|numeric|min:0',
            'cantidad' => 'nullable|integer|min:0'
        ]);

        // Datos del formulario
        $data = $request->only([
            'nombre',
            'tipo',
            'descripcion',
            'vida_util_meses',
            'frecuencia_entrega',
            'codigo_logistica',
            'marca_modelo',
            'precio',
            'cantidad'
        ]);

        // Guardar archivo PDF si existe
        if ($request->hasFile('ficha_tecnica')) {
            $data['ficha_tecnica'] = $request->file('ficha_tecnica')
                ->store('fichas_tecnicas', 'public');
        }

        // Guardar imagen si existe
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')
                ->store('epps', 'public');
        }

        // Actualizar EPP
        $epp->update($data);

        return redirect()->route('epps.catalogo')
            ->with('success', 'EPP actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $epp = Epp::findOrFail($id);
        $epp->delete();

        return redirect()->route('epps.catalogo')
            ->with('success', 'EPP eliminado correctamente');
    }
}
