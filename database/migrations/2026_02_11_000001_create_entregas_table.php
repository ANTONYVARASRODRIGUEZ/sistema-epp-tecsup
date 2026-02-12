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
        Schema::create('entregas', function (Blueprint $table) {
    $table->id();

    $table->foreignId('epp_id')->constrained('epps');
    $table->foreignId('departamento_id')->constrained('departamentos');

    $table->string('responsable'); // docente o encargado
    $table->date('fecha_entrega');
    $table->date('fecha_renovacion');

    $table->enum('estado', ['vigente', 'por_vencer', 'vencido'])->default('vigente');

    $table->text('observaciones')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
