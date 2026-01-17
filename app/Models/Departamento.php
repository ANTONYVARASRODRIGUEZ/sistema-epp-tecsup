<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'talleres',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación: Un departamento tiene muchos usuarios (Docentes/Admin)
     * Esto permite hacer Departamento::withCount('usuarios')
     */
    public function usuarios()
    {
        // Importante: 'departamento_id' debe ser el nombre de la columna 
        // en tu tabla 'users' que conecta con esta tabla
        return $this->hasMany(User::class, 'departamento_id');
    }

    /**
     * Relación: Un departamento tiene muchos EPPs
     */
    public function epps()
    {
        return $this->hasMany(Epp::class);
    }
}