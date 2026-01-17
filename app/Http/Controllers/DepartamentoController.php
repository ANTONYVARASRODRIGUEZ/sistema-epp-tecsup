<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\User; 
// --- NUEVOS IMPORTS PARA EL EXCEL ---
use App\Imports\DocentesImport;
use Maatwebsite\Excel\Facades\Excel;

class DepartamentoController extends Controller
{
    /**
     * Muestra las "Cards" de los departamentos.
     */
    public function index()
    {
        // Traemos los departamentos contando sus usuarios vinculados
        $departamentos = Departamento::withCount('usuarios')->get(); 
        
        return view('departamentos.index', compact('departamentos'));
    }

    /**
     * Muestra los docentes de un departamento específico.
     */
    public function show(string $id)
    {
        $departamento = Departamento::findOrFail($id);

        // Buscamos los usuarios vinculados por departamento_id
        $docentes = User::where('departamento_id', $id)
                        ->where('role', 'Docente')
                        ->get();

        return view('departamentos.show', compact('departamento', 'docentes'));
    }

    /**
     * MÉTODO NUEVO: Procesa el archivo Excel cargado desde el Modal
     */
    public function importar(Request $request, $id)
    {
        // 1. Validamos que el archivo sea un Excel
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls'
        ], [
            'excel_file.required' => 'Por favor, selecciona un archivo.',
            'excel_file.mimes' => 'El archivo debe ser un Excel (.xlsx o .xls).'
        ]);

        try {
            // 2. Ejecutamos la importación
            // El DocentesImport se encargará de crear los registros
            Excel::import(new DocentesImport, $request->file('excel_file'));

            // 3. Vinculamos los usuarios recién creados que no tengan departamento
            // (Esto asume que el importador los crea y nosotros los asignamos al depto actual)
            User::whereNull('departamento_id')->update(['departamento_id' => $id]);

            return back()->with('success', '¡Excelente! Los docentes se han sincronizado con la Matriz de Consistencia.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Hubo un error al procesar el Excel: ' . $e->getMessage());
        }
    }


    /**
 * MÉTODO NUEVO: Importación General (Mapeo automático por columna 'departamento')
 * Se usa desde el index para cargar a todos los docentes de una vez.
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
        // Ejecutamos la importación masiva
        // Nota: El archivo DocentesImport debe estar preparado para leer la columna 'departamento'
        Excel::import(new DocentesImport, $request->file('excel_file'));

        return back()->with('success', 'Matriz General procesada. Los docentes han sido mapeados a sus áreas correspondientes.');
        
    } catch (\Exception $e) {
        return back()->with('error', 'Error en Matriz General: ' . $e->getMessage());
    }
}

    // ... (Mantén aquí tus métodos create, store, edit, update y destroy)
}