<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Epp extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'tipo', 'descripcion', 'vida_util_meses', 'ficha_tecnica',
        'imagen', 'frecuencia_entrega', 'codigo_logistica', 'marca_modelo',
        'precio', 'cantidad', 'stock', 'entregado', 'deteriorado',
        'departamento_id', 'categoria_id', 'subcategoria_id', 'estado', 'fecha_vencimiento', 'activo',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Lógica Automática: Antes de guardar, traduce la frecuencia a meses numéricos.
     */
    protected static function booted()
    {
        static::saving(function ($epp) {
            if ($epp->frecuencia_entrega) {
                $f = strtolower($epp->frecuencia_entrega);
                if (str_contains($f, '5 años')) $epp->vida_util_meses = 60;
                elseif (str_contains($f, '3 años')) $epp->vida_util_meses = 36;
                elseif (str_contains($f, '2 años')) $epp->vida_util_meses = 24;
                elseif (str_contains($f, '1 año'))  $epp->vida_util_meses = 12;
                elseif (str_contains($f, '6 meses')) $epp->vida_util_meses = 6;
            }
        });
    }

    /**
     * Accessor: Calcula la fecha de vencimiento real.
     * Prioridad 1: Fecha manual. Prioridad 2: created_at + vida_util_meses.
     */
    public function getVencimientoRealAttribute()
    {
        if ($this->fecha_vencimiento) {
            return $this->fecha_vencimiento;
        }

        if ($this->created_at && $this->vida_util_meses) {
            return $this->created_at->copy()->addMonths((int)$this->vida_util_meses);
        }

        return null;
    }

    // Relaciones
    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function subcategoria() { return $this->belongsTo(Subcategoria::class); }
    public function departamento() { return $this->belongsTo(Departamento::class); }
}