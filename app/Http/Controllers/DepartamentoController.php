<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Personal;
use App\Models\Epp;
use App\Models\Taller;
use App\Models\MatrizHomologacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartamentoController extends Controller
{
    /**
     * Mapa de imágenes por palabras clave del nombre del departamento.
     * Se recorre en orden: la primera coincidencia gana.
     */
    private function resolverImagenAutomatica(string $nombre): ?string
    {
        $nombreLower = strtolower($nombre);

        $mapa = [
            'mecánica'   => 'https://iesgraupiura.edu.pe/wp-content/uploads/2023/09/mecanico-industrial.jpg',
            'mecanica'   => 'https://iesgraupiura.edu.pe/wp-content/uploads/2023/09/mecanico-industrial.jpg',
            'minería'    => 'https://www.tecsup.edu.pe/wp-content/uploads/2024/09/WEB-DEPARTAMENTOS_DPTO.-MINERIA-Y-PROCESOS-QUIMICOS-Y-METALURGICOS-10.jpg',
            'mineria'    => 'https://www.tecsup.edu.pe/wp-content/uploads/2024/09/WEB-DEPARTAMENTOS_DPTO.-MINERIA-Y-PROCESOS-QUIMICOS-Y-METALURGICOS-10.jpg',
            'estudios generales' => 'https://www.businessempresarial.com.pe/wp-content/uploads/2023/04/img-390x220.png',
            'tecnología digital' => 'https://www.tecsup.edu.pe/wp-content/uploads/2024/04/banner-3_Mesa-de-trabajo-1.png',
            'tecnologia digital' => 'https://www.tecsup.edu.pe/wp-content/uploads/2024/04/banner-3_Mesa-de-trabajo-1.png',
        ];

        foreach ($mapa as $clave => $url) {
            if (str_contains($nombreLower, $clave)) {
                return $url;
            }
        }

        return null; // Sin imagen predefinida → el blade usará el fallback de Unsplash
    }

    /**
     * Muestra las "Cards" de los departamentos en el Panel.
     */
    public function index()
    {
        $departamentos = Departamento::withCount('personals')->get();
        return view('departamentos.index', compact('departamentos'));
    }

    /**
     * Guarda un nuevo departamento creado desde el modal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'          => 'required|string|unique:departamentos,nombre|max:255',
            'imagen'          => 'nullable|image|max:2048',
            'imagen_url_text' => 'nullable|url',
        ]);

        $imagenUrl = null;

        // Prioridad 1: archivo subido manualmente
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('departamentos', 'public');
            $imagenUrl = 'storage/' . $path;
        }
        // Prioridad 2: URL pegada manualmente
        elseif ($request->filled('imagen_url_text')) {
            $imagenUrl = $request->imagen_url_text;
        }
        // Prioridad 3: imagen automática por nombre
        else {
            $imagenUrl = $this->resolverImagenAutomatica($request->nombre);
        }

        $departamento = new Departamento();
        $departamento->nombre      = $request->nombre;
        $departamento->nivel_riesgo = 'Bajo';
        $departamento->imagen_url  = $imagenUrl;
        $departamento->save();

        return back()->with('success', '¡Departamento creado con éxito!');
    }

    /**
     * Actualiza un departamento existente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'          => 'required|string|max:255|unique:departamentos,nombre,' . $id,
            'imagen'          => 'nullable|image|max:2048',
            'imagen_url_text' => 'nullable|url',
        ]);

        $departamento = Departamento::findOrFail($id);
        $departamento->nombre = $request->nombre;

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('departamentos', 'public');
            $departamento->imagen_url = 'storage/' . $path;
        } elseif ($request->filled('imagen_url_text')) {
            $departamento->imagen_url = $request->imagen_url_text;
        }
        // Si no se sube nada nuevo, se conserva la imagen actual

        $departamento->save();

        return back()->with('success', 'Departamento actualizado correctamente.');
    }

    /**
     * Asigna imágenes automáticas a todos los departamentos que no tienen imagen.
     * Ruta: GET /departamentos/asignar-imagenes  (útil para los ya importados)
     */
    public function asignarImagenesAutomaticas()
    {
        $actualizados = 0;

        Departamento::whereNull('imagen_url')
            ->orWhere('imagen_url', '')
            ->each(function ($depto) use (&$actualizados) {
                $url = $this->resolverImagenAutomatica($depto->nombre);
                if ($url) {
                    $depto->imagen_url = $url;
                    $depto->save();
                    $actualizados++;
                }
            });

        return back()->with('success', "Imágenes asignadas a {$actualizados} departamento(s).");
    }

    /**
     * Muestra los detalles de un departamento y su lista de docentes asignados.
     */
    public function show(string $id)
    {
        $departamento = Departamento::with(['personals' => function ($query) {
            $query->orderBy('carrera', 'asc')->orderBy('nombre_completo', 'asc');
        }, 'personals.asignaciones.epp', 'personals.talleres'])->findOrFail($id);

        $epps     = Epp::where('stock', '>', 0)->orderBy('nombre', 'asc')->get();
        $talleres = Taller::where('departamento_id', $id)->where('activo', true)->orderBy('nombre')->get();
        $matriz   = MatrizHomologacion::where('departamento_id', $id)->where('activo', true)->get();

        return view('departamentos.show', compact('departamento', 'epps', 'talleres', 'matriz'));
    }

    /**
     * Elimina un departamento específico.
     */
    public function destroy(string $id)
    {
        Personal::where('departamento_id', $id)->update(['departamento_id' => null]);
        Taller::where('departamento_id', $id)->update(['departamento_id' => null]);

        Departamento::findOrFail($id)->delete();

        return back()->with('success', 'Departamento eliminado correctamente.');
    }

    /**
     * Elimina todos los departamentos.
     */
    public function destroyAll()
    {
        Personal::query()->update(['departamento_id' => null]);
        Taller::query()->update(['departamento_id' => null]);
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

        Taller::whereIn('departamento_id', $request->ids)->update(['departamento_id' => null]);
        Personal::whereIn('departamento_id', $request->ids)->update(['departamento_id' => null]);
        Departamento::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'Departamentos eliminados correctamente.');
    }

    /**
     * Asigna un EPP a todo el personal del departamento.
     */
    public function asignarMasivo(Request $request, $id)
    {
        $request->validate(['epps' => 'required|array']);

        $departamento  = Departamento::with('personals')->findOrFail($id);
        $totalPersonas = $departamento->personals->count();

        if ($totalPersonas === 0) {
            return back()->with('error', 'No hay personal en este departamento para asignar.');
        }

        $seleccionados = collect($request->epps)->filter(fn($item) => isset($item['checked']));

        if ($seleccionados->isEmpty()) {
            return back()->with('error', 'No seleccionaste ningún EPP.');
        }

        try {
            DB::beginTransaction();
            $nombresAsignados = [];

            foreach ($seleccionados as $eppId => $data) {
                $epp = Epp::lockForUpdate()->find($eppId);
                if (!$epp) continue;

                $cantidad       = max(1, intval($data['cantidad']));
                $totalRequerido = $cantidad * $totalPersonas;

                if ($epp->stock < $totalRequerido) {
                    throw new \Exception("Stock insuficiente para '{$epp->nombre}'. Se requieren {$totalRequerido} (Stock: {$epp->stock}).");
                }

                foreach ($departamento->personals as $personal) {
                    \App\Models\Asignacion::create([
                        'personal_id'   => $personal->id,
                        'epp_id'        => $epp->id,
                        'cantidad'      => $cantidad,
                        'fecha_entrega' => now(),
                        'estado'        => 'Entregado',
                    ]);
                }

                $epp->decrement('stock', $totalRequerido);
                $nombresAsignados[] = $epp->nombre;
            }

            DB::commit();
            return back()->with('success', 'Asignación masiva completada para: ' . implode(', ', $nombresAsignados));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}