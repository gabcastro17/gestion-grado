<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurado extends Model
{
    protected $table = 'jurados';

    protected $fillable = [
        'proyecto_id',
        'profesor_id',
        'fecha_asignacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
        ];
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesor_id');
    }

    public function calificacion()
    {
        return $this->hasOne(Calificacion::class, 'jurado_id');
    }
}