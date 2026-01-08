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
        Schema::create('epps', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->string('tipo');
    $table->text('descripcion')->nullable();
    $table->integer('vida_util_meses');
    $table->string('ficha_tecnica')->nullable();
    $table->string('imagen')->nullable();
    $table->string('frecuencia_entrega')->nullable();
    $table->string('codigo_logistica')->nullable();
    $table->string('marca_modelo')->nullable();
    $table->decimal('precio', 8, 2)->nullable();
    $table->integer('cantidad')->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epps');
    }
};
