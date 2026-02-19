<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::truncate();

        User::create([
            'name' => 'Admin Centro',
            'email' => 'admin@tecsup.edu.pe',
            'password' => Hash::make('admin123'), 
            'role' => 'Admin',
            'department' => 'Sistemas'
        ]);

        User::create([
            'name' => 'Docente Prueba',
            'email' => 'docente@tecsup.edu.pe',
            'password' => Hash::make('docente123'), 
            'role' => 'Docente',
            'department' => 'Operaciones'
        ]);
    }
}
