<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoImagenSeeder extends Seeder
{
    /**
     * Picsum Photos — IDs fijos, sin API key, sin hotlink blocking.
     * Usa DB::table directamente para evitar cualquier caché de Eloquent.
     * Siempre sobreescribe URLs externas (no toca imágenes en storage/).
     */
    public function run(): void
    {
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
            'sistemas'           => 'https://picsum.photos/id/180/800/600',
            'computac'           => 'https://picsum.photos/id/180/800/600',
            'software'           => 'https://picsum.photos/id/180/800/600',
            'digital'            => 'https://picsum.photos/id/180/800/600',
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
            'humanidad'          => 'https://picsum.photos/id/501/800/600',
            'general'            => 'https://picsum.photos/id/501/800/600',
            'ciencias'           => 'https://picsum.photos/id/366/800/600',

            // Energía / Petróleo
            'energía'            => 'https://picsum.photos/id/146/800/600',
            'energia'            => 'https://picsum.photos/id/146/800/600',
            'petróleo'           => 'https://picsum.photos/id/162/800/600',
            'petroleo'           => 'https://picsum.photos/id/162/800/600',

            // Salud / Seguridad
            'salud'              => 'https://picsum.photos/id/356/800/600',
            'seguridad'          => 'https://picsum.photos/id/1/800/600',
            'sst'                => 'https://picsum.photos/id/1/800/600',
        ];

        $fallback = 'https://picsum.photos/id/96/800/600';

        $departamentos = DB::table('departamentos')->get();

        foreach ($departamentos as $depto) {
            // Respetar SOLO imágenes subidas manualmente al storage del proyecto
            if (!empty($depto->imagen_url) && str_starts_with($depto->imagen_url, 'storage/')) {
                continue;
            }

            $lower    = strtolower($depto->nombre);
            $urlFinal = $fallback;

            foreach ($mapa as $clave => $url) {
                if (str_contains($lower, $clave)) {
                    $urlFinal = $url;
                    break;
                }
            }

            // Actualización directa con query builder — sin caché de Eloquent
            DB::table('departamentos')
                ->where('id', $depto->id)
                ->update(['imagen_url' => $urlFinal]);
        }
    }
}