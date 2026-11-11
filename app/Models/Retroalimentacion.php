<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retroalimentacion extends Model
{
    protected $table = 'retroalimentaciones';

    protected $fillable = [
        'entrega_id',
        'profesor_id',
        'comentario',
        'estado',
    ];

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'entrega_id');
    }

    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesor_id');
    }
}