<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\Departamento;
use App\Models\Epp;
use App\Models\Asignacion;
use Illuminate\Http\Request;
use App\Imports\PersonalImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class PersonalController extends Controller
{
    public function index()
    {
        $personals = Personal::with([
            'departamento',
            'asignaciones' => fn($q) => $q->where('estado', 'Entregado')
        ])->orderBy('nombre_completo')->get();

        // Mapa de EPPs vinculados por departamento (para el organizador / asignaciones)
        $epps = Epp::orderBy('nombre')->get();

        $eppsVinculados = DB::table('departamento_epp')
            ->select('departamento_id', 'epp_id')
            ->get()
            ->groupBy('departamento_id')
            ->map(fn($items) => $items->pluck('epp_id'));

        return view('personals.index', compact('personals', 'epps', 'eppsVinculados'));
    }

    /**
     * Crear personal manualmente — solo nombre y tipo
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'tipo_contrato'   => 'required|string|in:Docente TC,Docente TP,Administrativo',
        ]);

        Personal::create([
            'nombre_completo' => trim($request->nombre_completo),
            'tipo_contrato'   => $request->tipo_contrato,
            'departamento_id' => null,
            // dni y carrera pueden quedar null — vienen del Excel si aplica
        ]);

        return back()->with('success', 'Personal registrado correctamente.');
    }

    public function show($id)
    {
        $departamento = Departamento::with('personals')->findOrFail($id);
        $epps = Epp::where('stock', '>', 0)->orderBy('nombre')->get();
        return view('departamentos.show', compact('departamento', 'epps'));
    }

    /**
     * Actualizar — solo nombre y tipo (sin dni ni carrera)
     */
    public function update(Request $request, $id)
    {
        $personal = Personal::findOrFail($id);

        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'tipo_contrato'   => 'nullable|string|in:Docente TC,Docente TP,Administrativo',
        ]);

        $personal->update([
            'nombre_completo' => trim($request->nombre_completo),
            'tipo_contrato'   => $request->tipo_contrato,
        ]);

        return back()->with('success', 'Datos actualizados correctamente.');
    }

    public function destroy($id)
    {
        Personal::findOrFail($id)->delete();
        return back()->with('success', 'Personal eliminado de la base de datos.');
    }

    /**
     * Eliminar seleccionados (IDs separados por coma)
     */
    public function deleteMultiple(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));

        if (empty($ids)) {
            return back()->with('error', 'Selecciona al menos un registro para eliminar.');
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Asignacion::whereIn('personal_id', $ids)->delete();
            Personal::whereIn('id', $ids)->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return back()->with('success', count($ids) . ' registro(s) y sus asignaciones eliminados correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Vaciar toda la base de personal
     */
    public function deleteAll()
    {
        try {
            $count = Personal::count();
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Asignacion::truncate();
            Personal::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return back()->with('success', "Se eliminaron {$count} registro(s) y todas sus asignaciones.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al vaciar: ' . $e->getMessage());
        }
    }

    /**
     * Importar desde Excel (sin cambios — sigue funcionando igual)
     */
    public function importExcel(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);

        try {
            \Log::info('INICIANDO IMPORTACIÓN: ' . $request->file('file')->getClientOriginalName());

            Excel::import(new PersonalImport, $request->file('file'));

            // Vinculación automática EPPs ↔ Departamentos
            $departamentos = Departamento::all();
            foreach (Epp::whereNotNull('departamento_texto')->get() as $epp) {
                $ids = [];
                foreach ($departamentos as $d) {
                    if (str_contains(strtolower($epp->departamento_texto), strtolower($d->nombre))) {
                        $ids[] = $d->id;
                    }
                }
                $epp->departamentos()->sync($ids);
            }

            $total = Personal::count();
            \Log::info("IMPORTACIÓN COMPLETADA — Total en BD: {$total}");

            return redirect()->route('personals.index')
                ->with('success', "Personal importado correctamente. Total en BD: {$total}");
        } catch (\Exception $e) {
            \Log::error('ERROR IMPORTACIÓN: ' . $e->getMessage());
            return redirect()->route('personals.index')
                ->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}