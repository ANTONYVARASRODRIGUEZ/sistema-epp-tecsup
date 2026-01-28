<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizadorController extends Controller
{
    /**
     * Muestra la interfaz de organización visual.
     */
    public function index()
    {
        // 1. Obtenemos los docentes que no pertenecen a ningún departamento (Lista Maestra)
        $sinAsignar = Personal::whereNull('departamento_id')
            ->orderBy('nombre_completo', 'asc')
            ->get();

        // 2. Traemos los departamentos con el conteo de sus docentes actuales
        $departamentos = Departamento::withCount('personals')
            ->orderBy('nombre', 'asc')
            ->get();

        return view('organizador.index', compact('sinAsignar', 'departamentos'));
    }

    /**
     * Procesa la asignación masiva mediante AJAX.
     */
    public function asignarMasivo(Request $request)
    {
        // Validamos que vengan IDs de docentes y un ID de departamento válido
        $request->validate([
            'docente_ids' => 'required|array',
            'docente_ids.*' => 'exists:personals,id',
            'departamento_id' => 'required|exists:departamentos,id'
        ]);

        try {
            DB::beginTransaction();

            // Realizamos la actualización masiva en una sola consulta SQL
            Personal::whereIn('id', $request->docente_ids)
                ->update(['departamento_id' => $request->departamento_id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '¡Personal asignado con éxito!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permite devolver docentes a la lista maestra (quitar asignación).
     */
    public function desasignarMasivo(Request $request)
    {
        $request->validate([
            'docente_ids' => 'required|array'
        ]);

        Personal::whereIn('id', $request->docente_ids)
            ->update(['departamento_id' => null]);

        return response()->json(['success' => true]);
    }
}