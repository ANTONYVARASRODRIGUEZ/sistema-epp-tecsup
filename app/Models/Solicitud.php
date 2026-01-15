<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    // Indicamos explícitamente el nombre de la tabla
    protected $table = 'solicitudes';

    // Permitimos la asignación masiva de estos campos
    protected $fillable = [
        'user_id',
        'epp_id',
        'motivo',
        'cantidad',
        'estado',
        'fecha_aprobacion',
        'fecha_vencimiento',
    ];

    protected $casts = [
        'fecha_aprobacion' => 'datetime',
        'fecha_vencimiento' => 'datetime',
    ];

    // Relación con el usuario (Docente)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el EPP
    public function epp()
    {
        return $this->belongsTo(Epp::class);
    }
}