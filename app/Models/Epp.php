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
        'categoria_id', // <--- Agregamos esto para que permita guardar la categoría
        'subcategoria', // <--- NUEVO: Para cumplir con el punto 2A
        'estado',
        'fecha_vencimiento'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'datetime',
    ];

    /**
     * Relación: Un EPP pertenece a una categoría (NUEVO)
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación: Un EPP pertenece a un departamento
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}