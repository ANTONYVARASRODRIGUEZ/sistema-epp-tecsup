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
        Schema::create('asignacions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('personal_id')->constrained('personals');
    $table->foreignId('epp_id')->constrained('epps');
    $table->integer('cantidad')->default(1);
    $table->date('fecha_entrega');
    $table->string('estado')->default('En posesión'); // 'En posesión' o 'Devuelto'
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacions');
    }
};
