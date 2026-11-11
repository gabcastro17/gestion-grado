<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'telegram_chat_id',
    'activo',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    // propuestas creadas por un profesor
    public function propuestas()
    {
        return $this->hasMany(Propuesta::class, 'profesor_id');
    }

    // solicitudes enviadas por un estudiante
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'estudiante_id');
    }

    // proyectos en los que participa como estudiante
    public function proyectosEstudiante()
    {
        return $this->hasMany(Proyecto::class, 'estudiante_id');
    }

    // proyectos que dirige como profesor
    public function proyectosProfesor()
    {
        return $this->hasMany(Proyecto::class, 'profesor_id');
    }

    // códigos PIN de inicio de sesión
    public function codigosPin()
    {
        return $this->hasMany(CodigoPin::class, 'user_id');
    }

    // asignaciones del profesor como jurado
    public function jurados()
    {
        return $this->hasMany(Jurado::class, 'profesor_id');
    }

    // documentos enviados por el estudiante
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'estudiante_id');
    }

    // retroalimentaciones realizadas por el profesor
    public function retroalimentaciones()
    {
        return $this->hasMany(Retroalimentacion::class, 'profesor_id');
    }
}
