<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->timestamp('fecha_aprobacion')->nullable()->after('estado');
            $table->timestamp('fecha_vencimiento')->nullable()->after('fecha_aprobacion');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropColumn(['fecha_aprobacion', 'fecha_vencimiento']);
        });
    }
};
