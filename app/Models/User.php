<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    // Los atributos que pueden ser asignados masivamente
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'activation_token',
        'is_active',
    ];
    // Relación con el modelo Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    // Función para verificar si el usuario tiene un rol específico
    public function hasRole($role)
    {
        return $this->role->name === $role;
    }
}
