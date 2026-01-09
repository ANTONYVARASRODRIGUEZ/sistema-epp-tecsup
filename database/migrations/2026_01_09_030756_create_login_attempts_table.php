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
        // La tabla login_attempts ya existe desde la migración anterior
        // Si no tiene las columnas, las agregamos
        if (Schema::hasTable('login_attempts') && !Schema::hasColumn('login_attempts', 'user_id')) {
            Schema::table('login_attempts', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->boolean('exitoso')->default(true);
                $table->string('razon_fallo')->nullable();
                $table->index('user_id');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
