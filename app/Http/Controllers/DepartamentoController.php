<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\User; 
use App\Imports\DocentesImport;
use Maatwebsite\Excel\Facades\Excel;

class DepartamentoController extends Controller
{
    /**
     * Muestra las "Cards" de los departamentos.
     */
    public function index()
    {
        // CAMBIO: Ahora contamos 'docentes' (la relación filtrada) en lugar de 'usuarios'
        // Esto ignora automáticamente a los Administradores en el contador de la Card.
        $departamentos = Departamento::withCount('docentes')->get(); 
        
        return view('departamentos.index', compact('departamentos'));
    }

    /**
     * Muestra los docentes de un departamento específico.
     */
    public function show(string $id)
    {
        $departamento = Departamento::findOrFail($id);

        // Buscamos solo usuarios con el rol 'Docente'
        $docentes = User::where('departamento_id', $id)
                        ->where('role', 'Docente')
                        ->get();

        return view('departamentos.show', compact('departamento', 'docentes'));
    }

    /**
     * Importación General (Sincronización Total)
     */
    public function importarGeneral(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls'
        ], [
            'excel_file.required' => 'Selecciona la Matriz General.',
            'excel_file.mimes' => 'El archivo debe ser un Excel (.xlsx o .xls).'
        ]);

        try {
            // OPCIONAL: Limpiar docentes antiguos antes de la nueva carga 
            // para evitar duplicados si los nombres cambiaron en el Excel.
            // User::where('role', 'Docente')->delete();

            Excel::import(new DocentesImport, $request->file('excel_file'));

            return back()->with('success', 'Matriz General procesada. Los docentes han sido mapeados a sus áreas correspondientes.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error en Matriz General: ' . $e->getMessage());
        }
    }

    /**
     * Importación por Departamento Específico
     */
    public function importar(Request $request, $id)
{
    $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls'
    ]);

    try {
        // Pasamos el $id del departamento directamente al constructor del Importador
        Excel::import(new DocentesImport($id), $request->file('excel_file'));

        return back()->with('success', 'Docentes importados correctamente en este departamento.');
    } catch (\Exception $e) {
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}
}