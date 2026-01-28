<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personals', function (Blueprint $table) {
            // Esto cambia la columna para que acepte nulos
            $table->foreignId('departamento_id')->nullable()->change();
            
            // También aprovechemos de hacer el DNI nullable por si acaso
            $table->string('dni')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('personals', function (Blueprint $table) {
            $table->foreignId('departamento_id')->nullable(false)->change();
            $table->string('dni')->nullable(false)->change();
        });
    }
};