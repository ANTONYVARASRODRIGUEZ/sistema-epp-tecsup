<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class PersonalImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        \Log::info("=== INICIANDO IMPORTACIÓN DE EXCEL ===");
        
        return [
            'matriz x docente' => new PersonalDataImport(),
            'Matriz x docente' => new PersonalDataImport(),
            'Matriz x Docente' => new PersonalDataImport(),
            'MATRIZ X DOCENTE' => new PersonalDataImport(),
            'matriz x docente ' => new PersonalDataImport(),
            'Matriz X Docente' => new PersonalDataImport(),
            'matrizxdocente' => new PersonalDataImport(),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        \Log::info("⚠️  Hoja ignorada: '$sheetName'");
    }
}
