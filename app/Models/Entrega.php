<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $fillable = [
        'epp_id', 
        'departamento_id', 
        'responsable', 
        'fecha_entrega', 
        'fecha_renovacion', 
        'estado', 
        'observaciones'
    ];

    // Relación para facilitar consultas después
    public function epp() {
        return $this->belongsTo(Epp::class);
    }
}