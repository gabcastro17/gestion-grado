<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoPin extends Model
{
    protected $table = 'codigos_pin';

    protected $fillable = [
        'user_id',
        'codigo',
        'expiracion',
        'usado',
    ];

    protected $hidden = [
        'codigo',
    ];

    protected function casts(): array
    {
        return [
            'expiracion' => 'datetime',
            'usado' => 'boolean',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}