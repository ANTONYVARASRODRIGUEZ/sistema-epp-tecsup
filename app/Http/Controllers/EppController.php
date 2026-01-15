<?php

namespace App\Http\Controllers;

use App\Models\Epp;
use App\Models\Departamento;
use Illuminate\Http\Request;

class EppController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $epps = Epp::all();
        $departamentos = Departamento::where('activo', true)->get();
        return view('epps.index', compact('epps', 'departamentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departamentos = Departamento::all();
        return view('epps.create', compact('departamentos'));
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
            'departamento_id' => 'nullable|exists:departamentos,id',
            'ficha_tecnica' => 'nullable|mimes:pdf|max:2048',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'frecuencia_entrega' => 'nullable|string|max:255',
            'codigo_logistica' => 'nullable|string|max:255',
            'marca_modelo' => 'nullable|string|max:255',
            'precio' => 'nullable|numeric|min:0',
            'cantidad' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'entregado' => 'nullable|integer|min:0',
            'deteriorado' => 'nullable|integer|min:0'
        ]);

        // 2️⃣ Datos del formulario
        $data = $request->only([
            'nombre',
            'tipo',
            'descripcion',
            'vida_util_meses',
            'departamento_id',
            'frecuencia_entrega',
            'codigo_logistica',
            'marca_modelo',
            'precio',
            'cantidad',
            'stock',
            'entregado',
            'deteriorado',
            'estado'
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
    $epps = Epp::all();
    $departamentos = Departamento::all();

    // Si el usuario es docente (usamos el rol que es más seguro)
    if (auth()->user()->role === 'Docente' || str_contains(auth()->user()->email, 'docente')) {
        return view('docente.catalogo', compact('epps', 'departamentos'));
    }

    // Si es admin, mostramos la vista de gestión
    return view('epps.catalogo', compact('epps', 'departamentos'));
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $epp = Epp::findOrFail($id);
        return view('epps.show', compact('epp'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $epp = Epp::findOrFail($id);
        $departamentos = Departamento::all();
        return view('epps.edit', compact('epp', 'departamentos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $epp = Epp::findOrFail($id);

        // Si solo viene datos de inventario (modal simplificado)
        if ($request->has('stock') && !$request->has('nombre')) {
            $request->validate([
                'cantidad' => 'nullable|integer|min:0',
                'stock' => 'nullable|integer|min:0',
                'entregado' => 'nullable|integer|min:0',
                'deteriorado' => 'nullable|integer|min:0',
                'estado' => 'nullable|string|in:disponible,bajo_stock,agotado'
            ]);

            $epp->update($request->only([
                'cantidad',
                'stock',
                'entregado',
                'deteriorado',
                'estado'
            ]));

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Inventario actualizado correctamente']);
            }
            return redirect()->route('epps.index')->with('success', 'Inventario actualizado correctamente');
        }

        // Si viene formulario completo de edición (modal de catalogo)
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'vida_util_meses' => 'required|integer|min:1',
            'departamento_id' => 'nullable|exists:departamentos,id',
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
            'departamento_id',
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

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('epps.index')
            ->with('success', 'EPP eliminado correctamente');
    }
}
