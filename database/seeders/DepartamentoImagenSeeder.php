<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;

class DepartamentoImagenSeeder extends Seeder
{
    public function run(): void
    {
        $mapa = [
            'mecánica'           => 'https://iesgraupiura.edu.pe/wp-content/uploads/2023/09/mecanico-industrial.jpg',
            'mecanica'           => 'https://iesgraupiura.edu.pe/wp-content/uploads/2023/09/mecanico-industrial.jpg',
            'minería'            => 'https://www.tecsup.edu.pe/wp-content/uploads/2024/09/WEB-DEPARTAMENTOS_DPTO.-MINERIA-Y-PROCESOS-QUIMICOS-Y-METALURGICOS-10.jpg',
            'mineria'            => 'https://www.tecsup.edu.pe/wp-content/uploads/2024/09/WEB-DEPARTAMENTOS_DPTO.-MINERIA-Y-PROCESOS-QUIMICOS-Y-METALURGICOS-10.jpg',
            'topograf'           => 'https://www.tecsup.edu.pe/wp-content/uploads/2024/09/WEB-DEPARTAMENTOS_DPTO.-MINERIA-Y-PROCESOS-QUIMICOS-Y-METALURGICOS-10.jpg',
            'estudios generales' => 'https://www.businessempresarial.com.pe/wp-content/uploads/2023/04/img-390x220.png',
            'tecnología digital' => 'https://www.tecsup.edu.pe/wp-content/uploads/2024/04/banner-3_Mesa-de-trabajo-1.png',
            'tecnologia digital' => 'https://www.tecsup.edu.pe/wp-content/uploads/2024/04/banner-3_Mesa-de-trabajo-1.png',
        ];

        foreach (Departamento::all() as $depto) {
            $lower = strtolower($depto->nombre);
            foreach ($mapa as $clave => $url) {
                if (str_contains($lower, $clave)) {
                    $depto->imagen_url = $url;
                    $depto->save();
                    break;
                }
            }
        }
    }
}