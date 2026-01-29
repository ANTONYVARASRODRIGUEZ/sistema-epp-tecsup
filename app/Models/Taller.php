<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taller extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'carrera_id',
        'departamento_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function personals()
    {
        return $this->belongsToMany(Personal::class, 'personal_taller')->withTimestamps();
    }
}
