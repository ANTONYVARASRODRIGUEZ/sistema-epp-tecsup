<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tallers', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('carrera_id')->nullable()->constrained('carreras');
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('personal_taller', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personals')->cascadeOnDelete();
            $table->foreignId('taller_id')->constrained('tallers')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['personal_id','taller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_taller');
        Schema::dropIfExists('tallers');
    }
};
