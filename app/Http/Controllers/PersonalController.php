<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\Departamento;
use App\Models\Epp;
use App\Models\Taller;
use Illuminate\Http\Request;
use App\Imports\PersonalImport;
use Maatwebsite\Excel\Facades\Excel;

class PersonalController extends Controller
{
    public function index()
    {
        $personals = Personal::with(['departamento', 'asignaciones' => function($query) {
            $query->where('estado', 'Entregado');
        }])->orderBy('nombre_completo', 'asc')->get();
        return view('personals.index', compact('personals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'dni' => 'nullable|string|unique:personals,dni',
            'carrera' => 'nullable|string|max:255',
            'tipo_contrato' => 'nullable|string',
        ]);

        Personal::create([
            'nombre_completo' => $request->nombre_completo,
            'dni' => $request->dni,
            'departamento_id' => null, 
            'carrera' => $request->carrera ?? 'Sin carrera',
            'tipo_contrato' => $request->tipo_contrato ?? 'Docente TC',
        ]);

        return back()->with('success', 'Docente registrado correctamente.');
    }

    public function show($id)
    {
        $departamento = Departamento::with('personals')->findOrFail($id);
        $epps = Epp::where('stock', '>', 0)->orderBy('nombre', 'asc')->get();
        return view('departamentos.show', compact('departamento', 'epps'));
    }

    /**
     * Actualizar datos del personal (Carrera, DNI, Nombre)
     */
    public function update(Request $request, $id)
    {
        $personal = Personal::findOrFail($id);

        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'dni' => 'nullable|string|max:20|unique:personals,dni,' . $id,
            'carrera' => 'nullable|string|max:255',
            'taller_nombre' => 'nullable|string|max:255',
        ]);

        $personal->update($request->except(['taller_nombre', 'taller_id']));

        if ($request->filled('taller_nombre')) {
            // Busca el taller por nombre o lo crea si no existe (asignándolo al mismo departamento)
            $taller = Taller::firstOrCreate(
                [
                    'nombre' => trim($request->taller_nombre),
                    'departamento_id' => $personal->departamento_id
                ],
                ['activo' => true]
            );
            
            $personal->talleres()->sync([$taller->id]);
        } else {
            $personal->talleres()->detach();
        }

        return back()->with('success', 'Datos del docente actualizados.');
    }

    // NUEVO: Para borrar personal de la lista maestra
    public function destroy($id)
    {
        $personal = Personal::findOrFail($id);
        $personal->delete();
        return back()->with('success', 'Docente eliminado de la base de datos.');
    }

    public function import(Request $request)
    {
        $request->validate(['excel_file' => 'required|mimes:xlsx,xls']);
        try {
            Excel::import(new PersonalImport, $request->file('excel_file'));
            return back()->with('success', '¡Personal importado correctamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}