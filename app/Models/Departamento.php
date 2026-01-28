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
     * Relación General: Trae a todos los usuarios vinculados.
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'departamento_id');
    }

    /**
     * NUEVA RELACIÓN: Trae solo a los usuarios con rol 'Docente'.
     * Úsalo para los contadores de la vista principal.
     */
    public function docentes()
    {
        return $this->hasMany(User::class, 'departamento_id')->where('role', 'Docente');
    }

    /**
     * Relación: Un departamento tiene muchos EPPs
     */
    public function epps()
    {
        return $this->hasMany(Epp::class);
    }

    public function personals()
{
    return $this->hasMany(Personal::class, 'departamento_id');
}
}