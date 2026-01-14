<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'exitoso',
        'razon_fallo',
    ];

    protected $casts = [
        'exitoso' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relación
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Registrar un intento de acceso
     */
    public static function registrar($user_id, $exitoso = true, $razon_fallo = null)
    {
        return self::create([
            'user_id' => $user_id,
            'exitoso' => $exitoso,
            'razon_fallo' => $razon_fallo,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
