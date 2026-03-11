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
     * Mapa de imágenes por palabras clave.
     * Usa Picsum Photos — IDs fijos, sin API key, sin hotlink blocking.
     * Se recorre en orden: la primera coincidencia gana.
     */
    private function resolverImagenAutomatica(string $nombre): string
    {
        $lower = strtolower($nombre);

        $mapa = [
            // Mecánica / Mecatrónica / Mantenimiento
            'mecatrónica'        => 'https://picsum.photos/id/137/800/600',
            'mecatronica'        => 'https://picsum.photos/id/137/800/600',
            'mecánica'           => 'https://picsum.photos/id/137/800/600',
            'mecanica'           => 'https://picsum.photos/id/137/800/600',
            'mantenimiento'      => 'https://picsum.photos/id/137/800/600',

            // Minería / Metalúrgica / Topografía / Geomática
            'minería'            => 'https://picsum.photos/id/162/800/600',
            'mineria'            => 'https://picsum.photos/id/162/800/600',
            'metalúrg'           => 'https://picsum.photos/id/162/800/600',
            'metalurg'           => 'https://picsum.photos/id/162/800/600',
            'topograf'           => 'https://picsum.photos/id/218/800/600',
            'geomát'             => 'https://picsum.photos/id/218/800/600',
            'geomat'             => 'https://picsum.photos/id/218/800/600',

            // Química / Procesos
            'químico'            => 'https://picsum.photos/id/366/800/600',
            'quimico'            => 'https://picsum.photos/id/366/800/600',
            'proceso'            => 'https://picsum.photos/id/366/800/600',

            // Tecnología Digital / Sistemas / Computación / Software / Redes
            'tecnología digital' => 'https://picsum.photos/id/180/800/600',
            'tecnologia digital' => 'https://picsum.photos/id/180/800/600',
            'digital'            => 'https://picsum.photos/id/180/800/600',
            'sistemas'           => 'https://picsum.photos/id/180/800/600',
            'computac'           => 'https://picsum.photos/id/180/800/600',
            'software'           => 'https://picsum.photos/id/180/800/600',
            'redes'              => 'https://picsum.photos/id/0/800/600',

            // Electricidad / Electrónica / Automatización
            'eléctric'           => 'https://picsum.photos/id/146/800/600',
            'electric'           => 'https://picsum.photos/id/146/800/600',
            'electrónic'         => 'https://picsum.photos/id/180/800/600',
            'electronic'         => 'https://picsum.photos/id/180/800/600',
            'automatiz'          => 'https://picsum.photos/id/96/800/600',

            // Civil / Construcción / Arquitectura
            'civil'              => 'https://picsum.photos/id/453/800/600',
            'construcción'       => 'https://picsum.photos/id/453/800/600',
            'construccion'       => 'https://picsum.photos/id/453/800/600',
            'arquitect'          => 'https://picsum.photos/id/453/800/600',

            // Logística / Administración / Gestión
            'logística'          => 'https://picsum.photos/id/375/800/600',
            'logistica'          => 'https://picsum.photos/id/375/800/600',
            'administrac'        => 'https://picsum.photos/id/370/800/600',
            'gestión'            => 'https://picsum.photos/id/370/800/600',
            'gestion'            => 'https://picsum.photos/id/370/800/600',

            // Estudios Generales / Humanidades / Ciencias
            'estudios generales' => 'https://picsum.photos/id/501/800/600',
            'estudios'           => 'https://picsum.photos/id/501/800/600',
            'general'            => 'https://picsum.photos/id/501/800/600',
            'humanidad'          => 'https://picsum.photos/id/501/800/600',
            'ciencias'           => 'https://picsum.photos/id/366/800/600',

            // Energía / Petróleo
            'energía'            => 'https://picsum.photos/id/146/800/600',
            'energia'            => 'https://picsum.photos/id/146/800/600',
            'petróleo'           => 'https://picsum.photos/id/162/800/600',
            'petroleo'           => 'https://picsum.photos/id/162/800/600',

            // Salud / Seguridad / SST
            'salud'              => 'https://picsum.photos/id/356/800/600',
            'seguridad'          => 'https://picsum.photos/id/1/800/600',
            'sst'                => 'https://picsum.photos/id/1/800/600',
        ];

        foreach ($mapa as $clave => $url) {
            if (str_contains($lower, $clave)) {
                return $url;
            }
        }

        // Fallback industrial genérico — nunca retorna null
        return 'https://picsum.photos/id/96/800/600';
    }

    public function index()
    {
        $departamentos = Departamento::withCount('personals')->get();
        return view('departamentos.index', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'          => 'required|string|unique:departamentos,nombre|max:255',
            'imagen'          => 'nullable|image|max:2048',
            'imagen_url_text' => 'nullable|url',
        ]);

        $imagenUrl = null;

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('departamentos', 'public');
            $imagenUrl = 'storage/' . $path;
        } elseif ($request->filled('imagen_url_text')) {
            $imagenUrl = $request->imagen_url_text;
        } else {
            // Siempre asigna Picsum — nunca queda null
            $imagenUrl = $this->resolverImagenAutomatica($request->nombre);
        }

        $departamento = new Departamento();
        $departamento->nombre       = $request->nombre;
        $departamento->nivel_riesgo = 'Bajo';
        $departamento->imagen_url   = $imagenUrl;
        $departamento->save();

        return back()->with('success', '¡Departamento creado con éxito!');
    }

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

        $departamento->save();

        return back()->with('success', 'Departamento actualizado correctamente.');
    }

    /**
     * Asigna imágenes Picsum a todos los departamentos sin imagen (o con imagen rota).
     * Ruta: GET /departamentos/asignar-imagenes
     */
    public function asignarImagenesAutomaticas()
    {
        $actualizados = 0;

        // Actualiza TODOS los que no tengan imagen subida al storage
        $departamentos = DB::table('departamentos')->get();

        foreach ($departamentos as $depto) {
            if (!empty($depto->imagen_url) && str_starts_with($depto->imagen_url, 'storage/')) {
                continue;
            }

            $url = $this->resolverImagenAutomatica($depto->nombre);

            DB::table('departamentos')
                ->where('id', $depto->id)
                ->update(['imagen_url' => $url]);

            $actualizados++;
        }

        return back()->with('success', "Imágenes asignadas a {$actualizados} departamento(s).");
    }

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

    public function destroy(string $id)
    {
        Personal::where('departamento_id', $id)->update(['departamento_id' => null]);
        Taller::where('departamento_id', $id)->update(['departamento_id' => null]);
        Departamento::findOrFail($id)->delete();

        return back()->with('success', 'Departamento eliminado correctamente.');
    }

    public function destroyAll()
    {
        Personal::query()->update(['departamento_id' => null]);
        Taller::query()->update(['departamento_id' => null]);
        Departamento::query()->delete();

        return back()->with('success', 'Departamentos eliminados. El personal ha vuelto a la Lista Maestra.');
    }

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