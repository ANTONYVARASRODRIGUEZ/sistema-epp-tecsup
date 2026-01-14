<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    protected $table = 'configuracion_sistemas';

    protected $fillable = [
        'nombre_sistema',
        'sede',
        'logo_url',
        'anio_academico',
        'tiempo_renovacion_dias',
        'umbral_stock_bajo',
        'alertas_vencimiento',
        'alertas_stock_bajo',
        'alertas_solicitudes_pendientes',
        'auditoria_activa',
        'dias_retencion_logs',
    ];

    protected $casts = [
        'alertas_vencimiento' => 'boolean',
        'alertas_stock_bajo' => 'boolean',
        'alertas_solicitudes_pendientes' => 'boolean',
        'auditoria_activa' => 'boolean',
    ];

    /**
     * Obtener la configuración actual del sistema
     */
    public static function obtener()
    {
        return self::first() ?? self::create([
            'nombre_sistema' => 'Sistema EPP TECSUP',
            'sede' => 'Tecsup Norte',
            'anio_academico' => '2026',
        ]);
    }
}
