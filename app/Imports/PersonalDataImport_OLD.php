<?php
// Respaldo de la versión anterior
namespace App\Imports;

use App\Models\Personal;
use App\Models\Taller;
use App\Models\Departamento;
use App\Models\Epp;
use App\Models\Asignacion;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

// Esta es la versión antigua - ver PersonalDataImport.php para la nueva versión
