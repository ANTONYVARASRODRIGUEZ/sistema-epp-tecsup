<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Departamento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Crear o actualizar usuario administrador
        User::updateOrCreate(
            ['email' => 'admin@tecsup.edu.pe'],
            [
                'name'       => 'Administrador',
                'password'   => Hash::make('admin123'),
                'role'       => 'Admin',
                'created_at' => now()->subDays(1),
                'updated_at' => now(),
            ]
        );

        // Llamar a los seeders en orden
        $this->call([
            // EppSeeder::class,
            MatrizDinamicaSeeder::class,
            DepartamentoImagenSeeder::class,
        ]);
    }
}