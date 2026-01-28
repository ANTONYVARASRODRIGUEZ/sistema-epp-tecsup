<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('categorias', function (Blueprint $table) {
        $table->id();
        $table->string('nombre'); // Ej: Guantes, Cascos
        $table->text('descripcion')->nullable();
        $table->timestamps();
    });

    // Añadimos la relación a la tabla epps
    Schema::table('epps', function (Blueprint $table) {
        $table->foreignId('categoria_id')->nullable()->constrained('categorias');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
