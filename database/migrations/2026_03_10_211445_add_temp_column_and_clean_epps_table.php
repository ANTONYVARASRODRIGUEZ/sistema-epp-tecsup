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
    Schema::table('epps', function (Blueprint $table) {
        // 1. Añadimos la columna temporal (usando 'nombre' como referencia de posición)
        if (!Schema::hasColumn('epps', 'departamento_texto')) {
            $table->text('departamento_texto')->nullable()->after('nombre');
        }

        // 2. Rompemos la llave foránea y luego borramos la columna
        if (Schema::hasColumn('epps', 'departamento_id')) {
            // Es vital usar un array ['departamento_id'] para que Laravel 
            // encuentre el nombre correcto del índice automáticamente
            $table->dropForeign(['departamento_id']); 
            $table->dropColumn('departamento_id');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
