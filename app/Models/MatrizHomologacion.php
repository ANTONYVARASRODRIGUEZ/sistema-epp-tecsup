<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatrizHomologacion extends Model
{
    protected $table = 'matriz_homologacions';

    protected $fillable = [
        'departamento_id',
        'epp_id',
        'puesto',
        'taller',
        'tipo_requerimiento',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function epp()
    {
        return $this->belongsTo(Epp::class);
    }
}
