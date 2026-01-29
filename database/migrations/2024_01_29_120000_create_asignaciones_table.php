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
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            
            // Relación con el personal (Docentes)
            $table->foreignId('personal_id')->constrained('personals')->onDelete('cascade');
            
            // Relación con el equipo (EPP)
            $table->foreignId('epp_id')->constrained('epps')->onDelete('cascade');
            
            $table->integer('cantidad')->default(1);
            $table->dateTime('fecha_entrega')->useCurrent();
            $table->string('estado')->default('Entregado'); // Ej: Entregado, Devuelto, Dañado
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};