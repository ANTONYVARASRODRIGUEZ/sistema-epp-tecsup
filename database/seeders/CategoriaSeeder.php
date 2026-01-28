<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $nombres = [
        'Protección Craneal', 'Protección Visual', 
        'Protección Manual', 'Protección de Pies', 
        'Protección Auditiva', 'Ropa de Trabajo'
    ];

    foreach ($nombres as $nombre) {
        \App\Models\Categoria::create(['nombre' => $nombre]);
    }
}
}
