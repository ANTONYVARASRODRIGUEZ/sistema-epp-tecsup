<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use App\Models\Departamento;
use Illuminate\Http\Request;

class TallerController extends Controller
{
    public function index(Request $request)
    {
        $depId = $request->get('departamento_id');
        $query = Taller::with('departamento');
        if ($depId) {
            $query->where('departamento_id', $depId);
        }
        $talleres = $query->orderBy('nombre')->paginate(15);
        $departamentos = Departamento::orderBy('nombre')->get(['id','nombre']);
        return view('talleres.index', compact('talleres','departamentos','depId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'departamento_id' => 'required|exists:departamentos,id',
            'carrera' => 'nullable|string|max:255',
        ]);
        // Campo carrera es opcional; si lo usas en la tabla tallers puedes añadirlo, si no, lo ignoramos
        $taller = Taller::create([
            'nombre' => $data['nombre'],
            'departamento_id' => $data['departamento_id'],
            'carrera_id' => null, // actualmente carrera en Personal es texto; dejamos null
            'activo' => true,
        ]);
        return back()->with('success', 'Taller/Lab creado correctamente.');
    }

    public function update(Request $request, Taller $taller)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'departamento_id' => 'required|exists:departamentos,id',
        ]);
        $taller->update([
            'nombre' => $data['nombre'],
            'departamento_id' => $data['departamento_id'],
        ]);
        return back()->with('success', 'Taller/Lab actualizado.');
    }

    public function toggle(Taller $taller)
    {
        $taller->update(['activo' => !$taller->activo]);
        return back()->with('success', 'Estado de Taller/Lab actualizado.');
    }

    public function destroy(Taller $taller)
    {
        $taller->delete();
        return back()->with('success', 'Taller/Lab eliminado.');
    }
}
