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
    Schema::create('personals', function (Blueprint $table) {
        $table->id();
        // Ponemos el DNI como nullable por si Jiancarlo solo sabe el nombre al inicio
        $table->string('dni')->nullable()->unique(); 
        $table->string('nombre_completo');
        
        // LA CLAVE: nullable() permite que el docente exista sin departamento al inicio
        // onDelete('set null') evita errores si borras un departamento
        $table->foreignId('departamento_id')
              ->nullable()
              ->constrained()
              ->onDelete('set null'); 
              
        $table->string('carrera')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personals');
    }
};
