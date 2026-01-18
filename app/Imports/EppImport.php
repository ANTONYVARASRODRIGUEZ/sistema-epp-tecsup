<?php

namespace App\Imports;

use App\Models\Epp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class EppImport implements ToModel, WithStartRow, SkipsEmptyRows
{
    /**
     * Indicamos que empiece a leer desde la fila 3 
     * (Saltando el título y los encabezados)
     */
    public function startRow(): int
    {
        return 3;
    }

    public function model(array $row)
    {
        /**
         * Según tu archivo, las columnas son:
         * Index 0: N° (ID)
         * Index 1: EPP (Nombre)
         * Index 2: Descripción
         * Index 4: Frecuencia de Entrega
         * Index 5: Código de logística
         * Index 6: Marca / Modelo
         * Index 9: Precio Soles
         * Index 11: Cantidad
         */

        // Si la columna del nombre del EPP está vacía, saltamos la fila
        if (!isset($row[1]) || empty(trim($row[1]))) {
            return null;
        }

        return new Epp([
            'nombre'             => $row[1],
            'descripcion'        => $row[2] ?? null,
            'frecuencia_entrega' => $row[4] ?? null,
            'codigo_logistica'   => $row[5] ?? null,
            'marca_modelo'       => $row[6] ?? null,
            'precio'             => is_numeric($row[9]) ? (float)$row[9] : 0,
            'cantidad'           => is_numeric($row[11]) ? (int)$row[11] : 0,
            'tipo'               => 'Homologado',
            'vida_util_meses'    => 12, // Ajustar según necesidad
        ]);
    }
}