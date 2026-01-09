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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Usuario que realizó la acción
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Tipo de evento
            $table->string('evento')->comment('creacion_usuario, aprobacion_solicitud, entrega_epp, baja_epp, etc');
            $table->string('modelo')->nullable()->comment('Usuario, Epp, Solicitud, etc');
            $table->unsignedBigInteger('modelo_id')->nullable()->comment('ID del registro relacionado');
            
            // Detalles
            $table->text('descripcion')->nullable();
            $table->json('datos_anteriores')->nullable()->comment('Valores antes del cambio');
            $table->json('datos_nuevos')->nullable()->comment('Valores después del cambio');
            
            // IP y User Agent
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('user_id');
            $table->index('evento');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
