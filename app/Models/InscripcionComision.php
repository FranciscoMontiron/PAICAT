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
     * Relación con notas
     */
    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class, 'inscripcion_comision_id');
    }

    /**
     * Calcular promedio ponderado de notas
     * Usa el peso_porcentual de cada evaluación
     */
    public function calcularPromedioPonderado(): ?float
    {
        $notas = $this->notas()->with('evaluacion')->get();
        
        if ($notas->isEmpty()) {
            return null;
        }

        $sumaPonderada = 0;
        $sumaPesos = 0;

        foreach ($notas as $nota) {
            $peso = $nota->evaluacion->peso_porcentual ?? 100;
            $sumaPonderada += $nota->nota * $peso;
            $sumaPesos += $peso;
        }

        if ($sumaPesos === 0) {
            return null;
        }

        return round($sumaPonderada / $sumaPesos, 2);
    }

    /**
     * Calcular promedio simple de notas
     */
    public function calcularPromedioSimple(): ?float
    {
        $notas = $this->notas()->pluck('nota');
        
        if ($notas->isEmpty()) {
            return null;
        }

        return round($notas->avg(), 2);
    }

    /**
     * Determinar condición final del alumno
     * @return array ['condicion' => string, 'color' => string, 'descripcion' => string]
     */
    public function determinarCondicion(): array
    {
        $promedio = $this->calcularPromedioPonderado();
        $porcentajeAsistencia = $this->calcularPorcentajeAsistencia();
        $minimoAsistencia = config('paicat.porcentaje_asistencia_minimo', 75);
        
        // Sin notas cargadas
        if ($promedio === null) {
            return [
                'condicion' => 'Sin evaluar',
                'color' => 'gray',
                'descripcion' => 'No hay notas cargadas',
            ];
        }

        // Verificar asistencia
        $cumpleAsistencia = $porcentajeAsistencia >= $minimoAsistencia;

        // Lógica de condición
        if ($promedio >= 6 && $cumpleAsistencia) {
            return [
                'condicion' => 'Promocionado',
                'color' => 'green',
                'descripcion' => "Promedio: $promedio - Asistencia: {$porcentajeAsistencia}%",
            ];
        } elseif ($promedio >= 4 && $cumpleAsistencia) {
            return [
                'condicion' => 'Regular',
                'color' => 'blue',
                'descripcion' => "Promedio: $promedio - Debe rendir final",
            ];
        } elseif ($promedio >= 4 && !$cumpleAsistencia) {
            return [
                'condicion' => 'Libre por asistencia',
                'color' => 'yellow',
                'descripcion' => "Promedio: $promedio - Asistencia: {$porcentajeAsistencia}% (mínimo: {$minimoAsistencia}%)",
            ];
        } else {
            return [
                'condicion' => 'Desaprobado',
                'color' => 'red',
                'descripcion' => "Promedio: $promedio - Debe recursar",
            ];
        }
    }

    /**
     * Obtener nota de una evaluación específica
     */
    public function getNotaDeEvaluacion(int $evaluacionId): ?Nota
    {
        return $this->notas()->where('evaluacion_id', $evaluacionId)->first();
    }

    /**
     * Verificar si puede rendir recuperatorio
     */
    public function puedeRendirRecuperatorio(): bool
    {
        $promedio = $this->calcularPromedioPonderado();
        return $promedio !== null && $promedio < 6;
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
