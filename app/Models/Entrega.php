<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $table = 'entregas';

    protected $fillable = [
        'proyecto_id',
        'titulo',
        'descripcion',
        'numero_orden',
        'fecha_inicio',
        'fecha_limite',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_limite' => 'date',
        ];
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'entrega_id');
    }

    public function retroalimentaciones()
    {
        return $this->hasMany(
            Retroalimentacion::class,
            'entrega_id'
        );
    }
}