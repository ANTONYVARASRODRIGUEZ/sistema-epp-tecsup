<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional, si tu tabla se llama 'personals')
    protected $table = 'personals';

    /**
     * CAMPOS QUE SE PUEDEN LLENAR DESDE EL EXCEL
     * Deben coincidir exactamente con los nombres en tu base de datos
     */
    protected $fillable = [
        'nombre_completo', // <--- Este es el que te pedía el error
        'dni',
        'carrera',
        'departamento_id',
        'tipo_contrato'
    ];

    /**
     * Relación: Un Personal pertenece a un Departamento
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    /**
     * Relación: Un Personal tiene muchas asignaciones de EPP
     */
    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }
}