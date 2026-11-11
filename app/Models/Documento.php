<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'entrega_id',
        'estudiante_id',
        'nombre_archivo',
        'ruta_archivo',
        'enlace',
        'tipo_archivo',
    ];

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'entrega_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }
}