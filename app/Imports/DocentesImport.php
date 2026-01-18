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
    private $idDepartamentoPagina; // El ID de la página donde estás
    private $nombreDepartamentoPagina; // El nombre de la página donde estás
    private $empezarAImportar = false; // El "interruptor"

    public function __construct($id)
    {
        $this->idDepartamentoPagina = $id;
        // Buscamos el nombre real (ej: "Mecánica") para saber qué buscar en el Excel
        $depto = Departamento::find($id);
        $this->nombreDepartamentoPagina = $this->normalizar($depto->nombre);
    }

    public function headingRow(): int { return 5; }

    public function model(array $row)
    {
        $valorCelda = $row['nombre_del_docente'] ?? $row[0] ?? null;
        if (!$valorCelda) return null;

        $textoLimpio = $this->normalizar($valorCelda);

        // --- LÓGICA DEL INTERRUPTOR ---
        
        // 1. ¿Esta celda es un título de departamento?
        // (Ej: Si la celda dice "DEPARTAMENTO DE MECANICA" y estamos en la página de Mecánica)
        if (str_contains($textoLimpio, 'DEPARTAMENTO') || str_contains($textoLimpio, 'AREA')) {
            
            if (str_contains($textoLimpio, $this->nombreDepartamentoPagina)) {
                // ¡Encontramos nuestra sección! Encendemos el interruptor
                $this->empezarAImportar = true;
                return null; 
            } else {
                // Es el título de OTRO departamento. Apagamos el interruptor
                $this->empezarAImportar = false;
                return null;
            }
        }

        // 2. Si el interruptor está apagado, ignoramos la fila
        if (!$this->empezarAImportar) {
            return null;
        }

        // 3. Si el interruptor está encendido, validamos que sea un nombre real
        // (Mínimo 2 palabras y que no sea basura como "TOTAL")
        $palabras = explode(' ', trim($valorCelda));
        if (count($palabras) < 2 || str_contains($textoLimpio, 'TOTAL')) {
            return null;
        }

        // 4. GUARDAR SOLO LOS DOCENTES DE ESTA SECCIÓN
        return User::updateOrCreate(
            ['email' => Str::slug($valorCelda) . '@tecsup.edu.pe'],
            [
                'name'            => trim($valorCelda),
                'password'        => Hash::make('tecsup2025'),
                'departamento_id' => $this->idDepartamentoPagina,
                'role'            => 'Docente',
                'talla_zapatos'   => $row['talla_zapatos'] ?? 'N/A',
                'talla_mandil'    => $row['talla_chaleco_mandil'] ?? $row['carrera'] ?? 'N/A',
            ]
        );
    }

    private function normalizar($texto) {
        $reemplazos = ['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N'];
        return strtr(strtoupper($texto), $reemplazos);
    }
}