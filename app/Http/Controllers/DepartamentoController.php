<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Personal;
use App\Models\Epp;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    /**
     * Muestra las "Cards" de los departamentos en el Panel.
     */
    public function index()
    {
        // Contamos cuántos personals (docentes) tiene cada departamento
        $departamentos = Departamento::withCount('personals')->get(); 
        
        return view('departamentos.index', compact('departamentos'));
    }

    /**
     * Guarda un nuevo departamento creado desde el modal de Jiancarlo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:departamentos,nombre|max:255',
        ]);

        Departamento::create([
            'nombre' => $request->nombre,
            'nivel_riesgo' => 'Bajo', // Valor por defecto
            // 'imagen_url' => '...' (puedes añadir una imagen predeterminada aquí)
        ]);

        return back()->with('success', '¡Departamento creado con éxito!');
    }

    /**
     * Muestra los detalles de un departamento y su lista de docentes asignados.
     */
    public function show(string $id)
    {
        $departamento = Departamento::with('personals')->findOrFail($id);
        $epps = Epp::where('stock', '>', 0)->orderBy('nombre', 'asc')->get();

        return view('departamentos.show', compact('departamento', 'epps'));
    }

    /**
     * Elimina todos los departamentos y deja a los docentes "sin asignar".
     */
    public function destroyAll()
    {
        // Primero dejamos a todos los docentes sin departamento (en lugar de borrarlos)
        // Así Jiancarlo no pierde su "Lista Maestra"
        Personal::query()->update(['departamento_id' => null]);
        
        // Luego borramos los departamentos
        Departamento::query()->delete();

        return back()->with('success', 'Departamentos eliminados. El personal ha vuelto a la Lista Maestra.');
    }

    /**
     * Elimina departamentos seleccionados.
     */
    public function destroySelected(Request $request)
    {
        if (!$request->ids) {
            return back()->with('error', 'No has seleccionado nada.');
        }

        // Desasignamos al personal de esos departamentos antes de borrar
        Personal::whereIn('departamento_id', $request->ids)->update(['departamento_id' => null]);
        
        Departamento::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'Departamentos eliminados correctamente.');
    }
}