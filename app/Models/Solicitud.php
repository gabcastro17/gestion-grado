<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    protected $fillable = [
        'estudiante_id',
        'propuesta_id',
        'estado',
        'mensaje',
        'fecha_solicitud',
        'fecha_respuesta',
    ];

    protected function casts(): array
    {
        return [
            'fecha_solicitud' => 'datetime',
            'fecha_respuesta' => 'datetime',
        ];
    }

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }

    public function propuesta()
    {
        return $this->belongsTo(Propuesta::class, 'propuesta_id');
    }

    public function proyecto()
    {
        return $this->hasOne(Proyecto::class, 'solicitud_id');
    }
}