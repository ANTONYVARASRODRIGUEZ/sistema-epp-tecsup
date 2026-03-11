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
    Schema::create('departamento_epp', function (Blueprint $table) {
        $table->id();
        // Relación con EPPs
        $table->foreignId('epp_id')->constrained('epps')->onDelete('cascade');
        // Relación con Departamentos
        $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamento_epp');
    }
};
