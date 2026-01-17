<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Departamento;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DocentesImport implements ToModel, WithHeadingRow
{
    private $currentDeptoId = null;
    private $encontradoDocenteEnSeccion = false;

    // Indicamos que los títulos están en la fila 5
    public function headingRow(): int
    {
        return 5;
    }

    public function model(array $row)
    {
        // 1. Nombre del docente (Columna A)
        $valorCelda = isset($row['nombre_del_docente']) ? trim($row['nombre_del_docente']) : '';

        // 2. Detector de Departamentos
        if (Str::contains($valorCelda, ['Departamento', 'Diseño', 'Minería'])) {
            $depto = Departamento::where('nombre', 'LIKE', '%' . $valorCelda . '%')->first();
            if ($depto) {
                $this->currentDeptoId = $depto->id;
                $this->encontradoDocenteEnSeccion = false;
            }
            return null; 
        }

        // 3. Lógica de interrupción si hay celda vacía
        if ($this->currentDeptoId && empty($valorCelda) && $this->encontradoDocenteEnSeccion) {
            $this->currentDeptoId = null;
            return null;
        }

        // 4. Salto de encabezados
        if (empty($valorCelda) || Str::contains($valorCelda, ['TC/TP', 'Puesto', 'Total'])) {
            return null;
        }

        // 5. Creación/Actualización del Docente con Tallas
        if ($this->currentDeptoId) {
            $this->encontradoDocenteEnSeccion = true;

            // Extraemos las tallas de las columnas exactas de tu imagen
            $tallaZ = $row['talla_zapatos'] ?? null;
            // Para "Talla Chaleco / Mandil", Laravel genera 'talla_chaleco_mandil'
            $tallaM = $row['talla_chaleco_mandil'] ?? null;

            return User::updateOrCreate(
                ['email' => Str::slug($valorCelda) . '@tecsup.edu.pe'],
                [
                    'name'            => $valorCelda,
                    'password'        => Hash::make('tecsup2025'),
                    'departamento_id' => $this->currentDeptoId,
                    'role'            => 'Docente',
                    'workshop'        => $row['taller_lab'] ?? null,
                    'talla_zapatos'   => ($tallaZ && $tallaZ != '-') ? $tallaZ : 'N/A',
                    'talla_mandil'    => ($tallaM && $tallaM != '-') ? $tallaM : 'N/A',
                ]
            );
        }

        return null;
    }
}