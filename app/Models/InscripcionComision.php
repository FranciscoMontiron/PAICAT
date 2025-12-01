<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InscripcionComision extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inscripcion_comisiones';

    protected $fillable = [
        'academico_dato_id',
        'comision_id',
        'fecha_inscripcion',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inscripcion' => 'datetime',
    ];

    /**
     * Datos académicos del alumno
     */
    public function academicoDato(): BelongsTo
    {
        return $this->belongsTo(AcademicoDato::class, 'academico_dato_id');
    }

    /**
     * Comisión a la que está inscrito
     */
    public function comision(): BelongsTo
    {
        return $this->belongsTo(Comision::class, 'comision_id');
    }
}

