<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'solicitud_id',
        'estudiante_id',
        'profesor_id',
        'propuesta_id',
        'estado',
        'fecha_inicio',
        'fecha_finalizacion',
        'fase_evaluacion',
        'fecha_limite_correccion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_finalizacion' => 'date',
            'fecha_limite_correccion' => 'date',
        ];
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }

    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesor_id');
    }

    public function propuesta()
    {
        return $this->belongsTo(Propuesta::class, 'propuesta_id');
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'proyecto_id');
    }

    public function jurados()
    {
        return $this->hasMany(Jurado::class, 'proyecto_id');
    }

    public function profesoresJurados()
    {
        return $this->belongsToMany(
            User::class,
            'jurados',
            'proyecto_id',
            'profesor_id'
        )
        ->withPivot('id', 'fecha_asignacion')
        ->withTimestamps();
    }

    public function evaluacionesDirector()
    {
        return $this->hasMany(
            EvaluacionDirector::class,
            'proyecto_id'
        );
    }
}