<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregamos el DNI y la relación con departamentos
            if (!Schema::hasColumn('users', 'dni')) {
                $table->string('dni')->nullable()->after('email');
            }
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['departamento_id']);
            $table->dropColumn(['departamento_id', 'dni']);
        });
    }
};