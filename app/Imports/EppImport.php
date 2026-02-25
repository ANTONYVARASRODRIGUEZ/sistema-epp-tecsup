<?php

namespace App\Imports;

use App\Models\Epp;
use App\Models\Categoria;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class EppImport implements ToModel, WithStartRow, SkipsEmptyRows
{
    public function startRow(): int
    {
        return 3; // Salta título y encabezados
    }

    public function model(array $row)
    {
        if (!isset($row[1]) || empty(trim($row[1]))) {
            return null;
        }

        $nombreEpp = trim($row[1]);



        // Limpiamos el nombre para la búsqueda (quitamos espacios extra y caracteres raros)
    // Agregamos "safety" o "industrial" para que la búsqueda sea más precisa
    $terminoBusqueda = urlencode($nombreEpp . ' safety equipment');


    // Usaremos un servicio de imágenes aleatorias que busque por palabra clave
    // Source Unsplash permite buscar por términos
    $urlImagenAutomatica = "https://source.unsplash.com/featured/400x400?{$terminoBusqueda}";


        // --- NUEVO FILTRO: IGNORAR EL ENCABEZADO ESPECÍFICO ---
    // Si el nombre contiene el título del reporte, lo saltamos
    if (str_contains(strtoupper($nombreEpp), 'EQUIPOS DE PROTECCIÓN COLECTIVO')) {
        return null;
    }


        $categoriaId = $this->obtenerCategoriaPorNombre($nombreEpp);
        $cantidadInicial = is_numeric($row[11]) ? (int)$row[11] : 0;

        // --- LÓGICA INTELIGENTE DE FECHA DE VENCIMIENTO ---
        $frecuenciaTexto = strtolower($row[4] ?? ''); 
        $vidaUtilMeses = 12; // Valor por defecto si no encuentra nada

        // 1. Buscamos el número
        if (preg_match('/\d+/', $frecuenciaTexto, $matches)) {
            $numero = (int)$matches[0];

            // 2. Si el texto contiene "año" o "año", multiplicamos por 12
            if (str_contains($frecuenciaTexto, 'año') || str_contains($frecuenciaTexto, 'ano')) {
                $vidaUtilMeses = $numero * 12;
            } else {
                // Si no, asumimos que el número ya son meses
                $vidaUtilMeses = $numero;
            }
        }

        // 3. Calculamos la fecha real (ej: si son 3 años, sumará 36 meses)
        $fechaVencimiento = now()->addMonths($vidaUtilMeses);
        // --------------------------------------------------

        return new Epp([
            'nombre'             => $nombreEpp,
            'imagen'            => $urlImagenAutomatica, // Guardamos la URL externa
            'descripcion'        => $row[2] ?? null,
            'frecuencia_entrega' => $row[4] ?? null,
            'codigo_logistica'   => $row[5] ?? null,
            'marca_modelo'       => $row[6] ?? null,
            'precio'             => is_numeric($row[9]) ? (float)$row[9] : 0,
            'cantidad'           => $cantidadInicial,
            'stock'              => $cantidadInicial, 
            'entregado'          => 0,
            'deteriorado'        => 0,
            'tipo'               => 'Protección de seguridad',
            'vida_util_meses'    => $vidaUtilMeses, 
            'fecha_vencimiento'  => $fechaVencimiento, 
            'categoria_id'       => $categoriaId, 
            'estado'             => 'disponible',
        ]);
    }

    /**
     * Busca palabras clave y SI NO EXISTE la categoría, la CREA.
     */
    private function obtenerCategoriaPorNombre($nombre)
    {
        $nombreLower = strtolower($nombre);
        $nombreCategoria = 'Otros'; // Por defecto

        if (str_contains($nombreLower, 'casco') || str_contains($nombreLower, 'craneal')) {
            $nombreCategoria = 'Protección Craneal';
        } elseif (str_contains($nombreLower, 'lente') || str_contains($nombreLower, 'gafa') || str_contains($nombreLower, 'careta')) {
            $nombreCategoria = 'Protección Visual';
        } elseif (str_contains($nombreLower, 'guante')) {
            $nombreCategoria = 'Protección Manual';
        } elseif (str_contains($nombreLower, 'bota') || str_contains($nombreLower, 'zapato') || str_contains($nombreLower, 'calzado')) {
            $nombreCategoria = 'Protección de Pies';
        } elseif (str_contains($nombreLower, 'tapon') || str_contains($nombreLower, 'orejera') || str_contains($nombreLower, 'auditivo')) {
            $nombreCategoria = 'Protección Auditiva';
        } elseif (str_contains($nombreLower, 'chaleco') || str_contains($nombreLower, 'mameluco') || str_contains($nombreLower, 'ropa')) {
            $nombreCategoria = 'Ropa de Trabajo';
        }

        // Esta es la magia: Si no existe, la crea. Si existe, la trae.
        $categoria = Categoria::firstOrCreate(
            ['nombre' => $nombreCategoria],
            ['descripcion' => 'Categoría generada automáticamente por importación']
        );

        return $categoria->id;
    }
}