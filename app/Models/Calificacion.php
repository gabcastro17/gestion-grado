<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';

    protected $fillable = [
        'jurado_id',
        'nota',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'nota' => 'decimal:2',
        ];
    }

    public function jurado()
    {
        return $this->belongsTo(Jurado::class, 'jurado_id');
    }
}