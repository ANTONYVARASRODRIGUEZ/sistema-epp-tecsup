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
        // 1. Validación básica: Si no hay nombre de EPP (Columna B / Índice 1)
        if (!isset($row[1]) || empty(trim($row[1]))) {
            return null;
        }

        $nombreEpp = trim($row[1]);

        // 2. Lógica de Categoría Inteligente (MODIFICADO)
        $categoriaId = $this->obtenerCategoriaPorNombre($nombreEpp);

        // 3. Cantidad y Stock
        $cantidadInicial = is_numeric($row[11]) ? (int)$row[11] : 0;

        return new Epp([
            'nombre'             => $nombreEpp,
            'descripcion'        => $row[2] ?? null,
            'frecuencia_entrega' => $row[4] ?? null,
            'codigo_logistica'   => $row[5] ?? null,
            'marca_modelo'       => $row[6] ?? null,
            'precio'             => is_numeric($row[9]) ? (float)$row[9] : 0,
            'cantidad'           => $cantidadInicial,
            'stock'              => $cantidadInicial, 
            'entregado'          => 0,
            'deteriorado'        => 0,
            'tipo'               => 'Homologado',
            'vida_util_meses'    => 12,
            'categoria_id'       => $categoriaId, 
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