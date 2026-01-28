<?php

namespace App\Imports;

use App\Models\Personal;
use App\Models\Departamento;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PersonalImport implements ToModel, WithStartRow
{
    private $currentDepartamentoId = null;

    public function startRow(): int
    {
        return 5; // Empezamos en la fila 5 donde inician los datos en tu Excel
    }

    public function model(array $row)
    {
        // 1. Detectar si la fila es un título de Departamento
        // En tu excel, los departamentos aparecen en la primera columna y las demás están vacías
        if (!empty($row[0]) && empty($row[1]) && empty($row[3])) {
            $depto = Departamento::firstOrCreate(['nombre' => trim($row[0])]);
            $this->currentDepartamentoId = $depto->id;
            return null; // No creamos un personal, solo actualizamos el departamento actual
        }

        // 2. Si es una fila de docente (Tiene nombre y no es el encabezado "Nombre del Docente")
        if (!empty($row[0]) && $row[0] !== 'Nombre del Docente' && $row[0] !== 'Total') {
            
            // Si por alguna razón no ha detectado departamento, le asignamos uno genérico
            if (!$this->currentDepartamentoId) {
                $depto = Departamento::firstOrCreate(['nombre' => 'General']);
                $this->currentDepartamentoId = $depto->id;
            }

            return new Personal([
                'nombre_completo' => $row[0],
                'dni'             => $row[4] ?? '00000000', // Tu excel usa la columna E para códigos/carrera, usaremos eso o un default
                'departamento_id' => $this->currentDepartamentoId,
                'carrera'         => $row[4] ?? 'Sin carrera', // Columna E en tu excel
            ]);
        }

        return null;
    }
}