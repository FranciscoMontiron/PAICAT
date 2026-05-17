<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CronogramaActividad extends Model
{
    use HasFactory;

    protected $table = 'cronograma_actividades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'comision_id',
        'anio',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'anio' => 'integer',
    ];

    public function comision(): BelongsTo
    {
        return $this->belongsTo(Comision::class);
    }
}

