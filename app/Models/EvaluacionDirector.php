<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionDirector extends Model
{
    protected $table = 'evaluaciones_director';

    protected $fillable = [
        'proyecto_id',
        'profesor_id',
        'nota',
        'observaciones',
        'nueva_fecha_limite',
        'numero_intento',
    ];

    protected function casts(): array
    {
        return [
            'nota' => 'decimal:2',
            'nueva_fecha_limite' => 'date',
        ];
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesor_id');
    }
}