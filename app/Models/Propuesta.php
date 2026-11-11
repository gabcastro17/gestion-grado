<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propuesta extends Model
{
    protected $table = 'propuestas';

    protected $fillable = [
        'profesor_id',
        'titulo',
        'descripcion',
        'introduccion',
        'objetivos',
        'estado',
    ];

    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesor_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'propuesta_id');
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'propuesta_id');
    }
}