<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'evento',
        'modelo',
        'modelo_id',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'created_at' => 'datetime',
    ];

    // Relación
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Registrar un evento de auditoría
     */
    public static function registrar($evento, $modelo, $modelo_id = null, $descripcion = null, $datos_anteriores = null, $datos_nuevos = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'evento' => $evento,
            'modelo' => $modelo,
            'modelo_id' => $modelo_id,
            'descripcion' => $descripcion,
            'datos_anteriores' => $datos_anteriores,
            'datos_nuevos' => $datos_nuevos,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
