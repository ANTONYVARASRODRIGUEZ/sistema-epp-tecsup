<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{

// Crear el usuario administrador
    \App\Models\User::create([
        'name' => 'Administrador',
        'email' => 'admin@tecsup.edu.pe',
        'password' => Hash::make('admin123'), // La contraseña será admin123
        'role' => 'Administrador',
    ]);
    $areas = [
        [
            'nombre' => 'Tecnología Digital',
            'nivel_riesgo' => 'Bajo',
            'imagen_url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=500',
            'descripcion' => 'Área de laboratorios de cómputo y redes.'
        ],
        [
            'nombre' => 'Mecánica',
            'nivel_riesgo' => 'Alto',
            'imagen_url' => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=500',
            'descripcion' => 'Talleres de soldadura y mantenimiento industrial.'
        ],
        [
            'nombre' => 'Electricidad',
            'nivel_riesgo' => 'Alto',
            'imagen_url' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=500',
            'descripcion' => 'Área de alta tensión y control eléctrico.'
        ]
    ];

    foreach ($areas as $area) {
        \App\Models\Departamento::create($area);
    }
}
}
