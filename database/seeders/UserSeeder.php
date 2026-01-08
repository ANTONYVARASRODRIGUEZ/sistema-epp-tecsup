<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $users = [
        ['name' => 'Admin Centro', 'email' => 'admin@tecsup.edu.pe', 'password' => bcrypt('admin123'), 'role' => 'Admin', 'department' => null],
        ['name' => 'Coordinador', 'email' => 'coord@tecsup.edu.pe', 'password' => bcrypt('coord123'), 'role' => 'Coordinador', 'department' => 'Operaciones'],
        ['name' => 'Docente', 'email' => 'docente@tecsup.edu.pe', 'password' => bcrypt('docente123'), 'role' => 'Docente', 'department' => 'Operaciones'],
    ];

    foreach ($users as $user) {
        \App\Models\User::create($user);
    }
}
}
