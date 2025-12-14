<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * Relación con asistencias
     */
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'inscripcion_comision_id');
    }

    /**
     * Calcular porcentaje de asistencia
     * Considera:
     * - presente = 1
     * - tardanza = 0.5
     * - justificado = 1
     */
    public function calcularPorcentajeAsistencia(): float
    {
        $asistencias = $this->asistencias()->get();
        $total = $asistencias->count();
        if ($total === 0) return 100.0;

        $puntos = 0;
        foreach ($asistencias as $a) {
            if ($a->estado === 'presente' || $a->estado === 'justificado') {
                $puntos += 1;
            } elseif ($a->estado === 'tardanza') {
                $puntos += 0.5;
            }
        }

        return round(($puntos / $total) * 100, 2);
    }

    /**
     * Determinar si el alumno está en riesgo
     */
    public function estaEnRiesgo(): bool
    {
        $minimo = config('paicat.porcentaje_asistencia_minimo', 75);
        return $this->calcularPorcentajeAsistencia() < $minimo;
    }

    /**
     * Acceso rápido al usuario del alumno
     */
    public function getAlumnoAttribute()
    {
        return $this->academicoDato->user ?? null;
    }
}
