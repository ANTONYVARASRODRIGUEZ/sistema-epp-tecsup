<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuracion_sistemas', function (Blueprint $table) {
            $table->id();
            
            // Información General
            $table->string('nombre_sistema')->default('Sistema EPP TECSUP');
            $table->string('sede')->default('Tecsup Norte');
            $table->string('logo_url')->nullable();
            $table->string('anio_academico')->default('2026');
            
            // Parámetros de EPP
            $table->integer('tiempo_renovacion_dias')->default(30);
            $table->integer('umbral_stock_bajo')->default(10);
            
            // Configuración de Notificaciones
            $table->boolean('alertas_vencimiento')->default(true);
            $table->boolean('alertas_stock_bajo')->default(true);
            $table->boolean('alertas_solicitudes_pendientes')->default(true);
            
            // Configuración de Auditoría
            $table->boolean('auditoria_activa')->default(true);
            $table->integer('dias_retencion_logs')->default(365);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_sistemas');
    }
};
