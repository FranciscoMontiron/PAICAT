<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Difusion extends Model
{
    protected $connection = 'paicat';
    protected $table = 'difusiones';

    protected $fillable = [
        'asunto',
        'mensaje',
        'modalidad',
        'usuario_id',
        'enviado_a',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el usuario que creó la difusión
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
