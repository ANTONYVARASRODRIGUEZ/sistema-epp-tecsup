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
        'vida_util_meses' => 'required|integer|min:1',
        'ficha_tecnica' => 'nullable|mimes:pdf|max:2048'
    ]);

    // 2️⃣ Datos del formulario
    $data = $request->only([
        'nombre',
        'tipo',
        'vida_util_meses'
    ]);

    // 3️⃣ Guardar archivo PDF si existe
    if ($request->hasFile('ficha_tecnica')) {
        $data['ficha_tecnica'] = $request->file('ficha_tecnica')
            ->store('fichas_tecnicas', 'public');
    }

    // 4️⃣ Crear EPP
    Epp::create($data);

    // 5️⃣ Redireccionar
    return redirect()->route('epps.index')
        ->with('success', 'EPP registrado correctamente');
}


    public function catalogo()
{
    $epps = Epp::all(); // O puedes usar paginación: Epp::paginate(6);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
