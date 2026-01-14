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
        Schema::table('epps', function (Blueprint $table) {
            $table->foreignId('departamento_id')->nullable()->after('cantidad')->constrained('departamentos')->onDelete('set null');
            $table->string('estado')->default('disponible')->after('departamento_id');
            $table->dateTime('fecha_vencimiento')->nullable()->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('epps', function (Blueprint $table) {
            $table->dropForeignKey(['departamento_id']);
            $table->dropColumn(['departamento_id', 'estado', 'fecha_vencimiento']);
        });
    }
};
