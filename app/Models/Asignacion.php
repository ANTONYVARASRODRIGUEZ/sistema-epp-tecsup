<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    protected $table = 'asignaciones';

    protected $fillable = [
        'personal_id',
        'epp_id',
        'cantidad',
        'fecha_entrega',
        'estado',       // Ej: 'Entregado', 'Devuelto', 'Dañado'
        'observaciones'
    ];

    protected $casts = [
        'fecha_entrega' => 'datetime',
    ];

    /**
     * Relación: Una asignación pertenece a una persona (Docente/Admin)
     */
    public function personal()
    {
        return $this->belongsTo(Personal::class);
    }

    /**
     * Relación: Una asignación es de un EPP específico
     */
    public function epp()
    {
        return $this->belongsTo(Epp::class);
    }
}
