<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Importamos el modelo Departamento para la relación
use App\Models\Departamento;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
    'name',
    'dni',
    'email',
    'password',
    'role',
    'departamento_id',
    'workshop',
    'talla_zapatos', 
    'talla_mandil',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * RELACIÓN: Un usuario pertenece a un departamento.
     * Esto permite hacer: $user->departamento->nombre
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
}