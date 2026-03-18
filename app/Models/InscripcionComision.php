<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class InscripcionComision extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'paicat';
    protected $table = 'inscripcion_comisiones';

    protected $fillable = [
        'inscripcion_id',
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
     * Inscripción al curso de ingreso
     */
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'inscripcion_id');
    }

    /**
     * Datos académicos del alumno (opcional, puede ser null)
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

    public function alumno(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,                    // Modelo final que queremos obtener
            AcademicoDato::class,           // Modelo intermedio
            'id',                           // Foreign key en academico_datos (relaciona con academico_dato_id)
            'id',                           // Foreign key en users (relaciona con academicoDato->user_id)
            'academico_dato_id',            // Local key en inscripcion_comisiones
            'user_id'                       // Local key en academico_datos
        );
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
        $minimoAsistencia = \App\Services\ConfiguracionService::get('asistencia_minima', 75);
        
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
        $notaAprobacion = \App\Services\ConfiguracionService::get('nota_aprobacion', 6);
        $notaMinimaRegular = \App\Services\ConfiguracionService::get('nota_minima_regular', 4);

        if ($promedio >= $notaAprobacion && $cumpleAsistencia) {
            return [
                'condicion' => 'Promocionado',
                'color' => 'green',
                'descripcion' => "Promedio: $promedio - Asistencia: {$porcentajeAsistencia}%",
            ];
        } elseif ($promedio >= $notaMinimaRegular && $cumpleAsistencia) {
            return [
                'condicion' => 'Regular',
                'color' => 'blue',
                'descripcion' => "Promedio: $promedio - Debe rendir final",
            ];
        } elseif ($promedio >= $notaMinimaRegular && !$cumpleAsistencia) {
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
        $notaAprobacion = \App\Services\ConfiguracionService::get('nota_aprobacion', 6);
        return $promedio !== null && $promedio < $notaAprobacion;
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
        $minimo = \App\Services\ConfiguracionService::get('asistencia_minima', 75);
        return $this->calcularPorcentajeAsistencia() < $minimo;
    }

    /**
     * Acceso rápido al usuario del alumno (ACCESSOR MANTENIDO)
     * 
     * Este accessor proporciona un fallback cuando la relación alumno() no está disponible
     * (por ejemplo, cuando no existe academico_dato_id).
     * 
     * Intenta obtenerlo desde academicoDato, si no desde inscripcion->person
     */
    public function getAlumnoAttribute()
    {
        // Si la relación alumno() está cargada, usarla
        if ($this->relationLoaded('alumno') && $this->alumno) {
            return $this->alumno;
        }

        // Primero intentar desde academico_dato
        if ($this->academicoDato && $this->academicoDato->user) {
            return $this->academicoDato->user;
        }

        // Si no, obtener desde inscripcion->person
        if ($this->inscripcion) {
            return $this->inscripcion->getPerson();
        }

        return null;
    }
}