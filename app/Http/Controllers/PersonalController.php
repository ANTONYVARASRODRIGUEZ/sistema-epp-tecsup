<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\Departamento;
use App\Models\Epp;
use Illuminate\Http\Request;
use App\Imports\PersonalImport;
use Maatwebsite\Excel\Facades\Excel;

class PersonalController extends Controller
{
    public function index()
    {
        $personals = Personal::with('departamento')->orderBy('nombre_completo', 'asc')->get();
        return view('personals.index', compact('personals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'dni' => 'nullable|string|unique:personals,dni',
        ]);

        Personal::create([
            'nombre_completo' => $request->nombre_completo,
            'dni' => $request->dni,
            'departamento_id' => null, 
        ]);

        return back()->with('success', 'Docente registrado correctamente.');
    }

    public function show($id)
    {
        $departamento = Departamento::with('personals')->findOrFail($id);
        $epps = Epp::where('stock', '>', 0)->orderBy('nombre', 'asc')->get();
        return view('departamentos.show', compact('departamento', 'epps'));
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