<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Epp extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'descripcion',
        'vida_util_meses',
        'ficha_tecnica',
        'imagen',
        'frecuencia_entrega',
        'codigo_logistica',
        'marca_modelo',
        'precio',
        'cantidad',
        'stock',
        'entregado',
        'deteriorado',
        'departamento_id',
        'estado',
        'fecha_vencimiento'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'datetime',
    ];

    /**
     * Relación: Un EPP pertenece a un departamento
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}
