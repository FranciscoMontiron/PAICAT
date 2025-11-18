<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nota extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notas';

    protected $fillable = [
        'evaluacion_id',
        'inscripcion_comision_id',
        'nota',
        'fecha_carga',
        'cargado_por',
        'observaciones',
    ];

    protected $casts = [
        'nota' => 'decimal:2',
        'fecha_carga' => 'datetime',
    ];

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class);
    }

    public function inscripcionComision(): BelongsTo
    {
        return $this->belongsTo(InscripcionComision::class);
    }

    public function cargadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cargado_por');
    }
}

