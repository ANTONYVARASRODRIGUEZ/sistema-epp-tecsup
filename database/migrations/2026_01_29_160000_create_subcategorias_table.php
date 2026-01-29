<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subcategorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Si la tabla epps ya tiene columna subcategoria (string), la reemplazamos por FK opcional
        if (Schema::hasTable('epps')) {
            Schema::table('epps', function (Blueprint $table) {
                if (Schema::hasColumn('epps', 'subcategoria')) {
                    $table->dropColumn('subcategoria');
                }
                if (!Schema::hasColumn('epps', 'subcategoria_id')) {
                    $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('epps')) {
            Schema::table('epps', function (Blueprint $table) {
                if (Schema::hasColumn('epps', 'subcategoria_id')) {
                    $table->dropConstrainedForeignId('subcategoria_id');
                }
            });
        }
        Schema::dropIfExists('subcategorias');
    }
};
