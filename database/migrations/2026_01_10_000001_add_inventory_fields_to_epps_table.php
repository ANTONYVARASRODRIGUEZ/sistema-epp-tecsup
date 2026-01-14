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
            $table->integer('stock')->default(0)->after('cantidad');
            $table->integer('entregado')->default(0)->after('stock');
            $table->integer('deteriorado')->default(0)->after('entregado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('epps', function (Blueprint $table) {
            $table->dropColumn(['stock', 'entregado', 'deteriorado']);
        });
    }
};
