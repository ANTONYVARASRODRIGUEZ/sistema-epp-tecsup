<?php

namespace App\Http\Controllers;

use App\Models\MatrizHomologacion;
use App\Models\Departamento;
use App\Models\Epp;
use Illuminate\Http\Request;

class MatrizEppController extends Controller
{
    /**
     * Mostrar todas las matrices EPP
     */
    public function index()
    {
        $matrizEpp = MatrizHomologacion::with(['departamento', 'epp'])->get();
        $departamentos = Departamento::all();
        $epps = Epp::all();
        return view('matriz-epp.index', compact('matrizEpp', 'departamentos', 'epps'));
    }

    /**
     * Almacenar nueva matriz EPP
     */
    public function store(Request $request)
    {
        $request->validate([
            'departamento' => 'required|string',
            'tipo_puesto' => 'required|string',
            'taller' => 'required|string',
            'epp_obligatorio' => 'nullable|array',
            'epp_especifico' => 'nullable|array',
        ]);

        // Obtener el ID del departamento por nombre
        $departamento = Departamento::where('nombre', $request->departamento)->first();
        
        if (!$departamento) {
            return redirect()->back()->with('error', 'Departamento no encontrado.');
        }

        // Crear una entrada en la matriz para cada EPP obligatorio
        if ($request->epp_obligatorio) {
            foreach ($request->epp_obligatorio as $eppNombre) {
                $epp = Epp::where('nombre', $eppNombre)->first();
                
                if ($epp) {
                    MatrizHomologacion::create([
                        'departamento_id' => $departamento->id,
                        'epp_id' => $epp->id,
                        'puesto' => $request->tipo_puesto,
                        'taller' => $request->taller,
                        'tipo_requerimiento' => 'obligatorio',
                        'activo' => true,
                    ]);
                }
            }
        }

        // Crear una entrada en la matriz para cada EPP específico
        if ($request->epp_especifico) {
            foreach ($request->epp_especifico as $eppNombre) {
                $epp = Epp::where('nombre', $eppNombre)->first();
                
                if ($epp) {
                    MatrizHomologacion::create([
                        'departamento_id' => $departamento->id,
                        'epp_id' => $epp->id,
                        'puesto' => $request->tipo_puesto,
                        'taller' => $request->taller,
                        'tipo_requerimiento' => 'especifico',
                        'activo' => true,
                    ]);
                }
            }
        }

        return redirect()->route('matriz-epp.index')->with('success', 'Matriz EPP creada correctamente.');
    }

    /**
     * Mostrar detalles de una matriz EPP
     */
    public function show($id)
    {
        $matriz = MatrizHomologacion::with(['departamento', 'epp'])->findOrFail($id);
        return view('matriz-epp.show', compact('matriz'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $matriz = MatrizHomologacion::findOrFail($id);
        $departamentos = Departamento::all();
        $epps = Epp::all();
        return view('matriz-epp.edit', compact('matriz', 'departamentos', 'epps'));
    }

    /**
     * Actualizar matriz EPP
     */
    public function update(Request $request, $id)
    {
        $matriz = MatrizHomologacion::findOrFail($id);

        $request->validate([
            'departamento_id' => 'required|exists:departamentos,id',
            'epp_id' => 'required|exists:epps,id',
            'puesto' => 'nullable|string',
            'taller' => 'nullable|string',
            'tipo_requerimiento' => 'required|in:obligatorio,especifico,opcional',
        ]);

        $matriz->update($request->all());

        return redirect()->route('matriz-epp.index')->with('success', 'Matriz EPP actualizada correctamente.');
    }

    /**
     * Eliminar matriz EPP
     */
    public function destroy($id)
    {
        $matriz = MatrizHomologacion::findOrFail($id);
        $matriz->delete();

        return redirect()->route('matriz-epp.index')->with('success', 'Matriz EPP eliminada correctamente.');
    }
}
