<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // <--- ESTA ES LA LÍNEA QUE FALTA

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Borramos los usuarios mal creados para limpiar la tabla
        User::truncate();

        User::create([
            'name' => 'Admin Centro',
            'email' => 'admin@tecsup.edu.pe',
            'password' => Hash::make('admin123'), // Forzamos Bcrypt aquí
            'role' => 'Admin',
            'department' => 'Sistemas'
        ]);

        User::create([
            'name' => 'Docente Prueba',
            'email' => 'docente@tecsup.edu.pe',
            'password' => Hash::make('docente123'), // Forzamos Bcrypt aquí
            'role' => 'Docente',
            'department' => 'Operaciones'
        ]);
    }
}
