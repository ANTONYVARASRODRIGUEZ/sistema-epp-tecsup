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
        // Cargamos el departamento y sus docentes, pero ORDENADOS por Carrera y luego por Nombre
        $departamento = Departamento::with(['personals' => function($query) {
            $query->orderBy('carrera', 'asc')
                  ->orderBy('nombre_completo', 'asc');
        }, 'personals.asignaciones.epp'])->findOrFail($id);

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

    /**
     * Asigna un EPP a todo el personal del departamento.
     */
    public function asignarMasivo(Request $request, $id)
    {
        $request->validate([
            'epp_id' => 'required|exists:epps,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $departamento = Departamento::with('personals')->findOrFail($id);
        $epp = Epp::findOrFail($request->epp_id);
        
        $cantidadPorPersona = $request->cantidad;
        $totalPersonas = $departamento->personals->count();
        
        if ($totalPersonas === 0) {
            return back()->with('error', 'No hay personal en este departamento para asignar.');
        }

        $totalRequerido = $cantidadPorPersona * $totalPersonas;

        if ($epp->stock < $totalRequerido) {
            return back()->with('error', "Stock insuficiente. Se requieren {$totalRequerido} unidades para los {$totalPersonas} docentes (Stock actual: {$epp->stock}).");
        }

        // Crear asignación para cada docente
        foreach ($departamento->personals as $personal) {
            \App\Models\Asignacion::create([
                'personal_id' => $personal->id,
                'epp_id'      => $epp->id,
                'cantidad'    => $cantidadPorPersona,
                'fecha_entrega' => now(),
            ]);
        }

        // Descontar stock total
        $epp->decrement('stock', $totalRequerido);

        return back()->with('success', "¡Éxito! Se asignaron {$totalRequerido} unidades de '{$epp->nombre}' a todo el departamento.");
    }
}