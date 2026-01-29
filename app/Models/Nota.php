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

    /**
     * Nota mínima para aprobar en curso de ingreso UTN
     * Puede configurarse en config/paicat.php o .env con NOTA_APROBACION=6
     */
    public static function notaAprobacion(): float
    {
        return (float) config('paicat.nota_aprobacion', 6);
    }

    protected $fillable = [
        'inscripcion_id',
        'evaluacion_id',
        'inscripcion_comision_id',
        'cursada_id',
        'nota',
        'fecha_carga',
        'cargado_por',
        'observaciones',
    ];

    protected $casts = [
        'nota' => 'decimal:2',
        'fecha_carga' => 'datetime',
    ];

    /**
     * Relación con la inscripción (alumno + año)
     */
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    /**
     * Relación con la evaluación
     */
    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class);
    }

    /**
     * Relación con la inscripción de comisión (para saber en qué comisión estaba)
     */
    public function inscripcionComision(): BelongsTo
    {
        return $this->belongsTo(InscripcionComision::class);
    }

    /**
     * Relación con la cursada (historial específico)
     */
    public function cursada(): BelongsTo
    {
        return $this->belongsTo(Cursada::class);
    }

    /**
     * Usuario que cargó la nota
     */
    public function cargadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cargado_por');
    }

    /**
     * Verificar si está aprobado (nota >= 6 por defecto en UTN)
     */
    public function estaAprobado(): bool
    {
        return $this->nota !== null && $this->nota >= self::notaAprobacion();
    }

    /**
     * Verificar si está desaprobado
     */
    public function estaDesaprobado(): bool
    {
        return $this->nota !== null && $this->nota < self::notaAprobacion();
    }

    /**
     * Verificar si puede rendir recuperatorio
     * (Desaprobó el parcial y existe recuperatorio para esa evaluación)
     */
    public function puedeRecuperatorio(): bool
    {
        if ($this->estaAprobado()) {
            return false;
        }

        // Verificar si existe un recuperatorio asociado a este parcial
        $evaluacion = $this->evaluacion;
        if (!$evaluacion || $evaluacion->tipo !== 'parcial') {
            return false;
        }

        // Buscar recuperatorio de la misma materia/comisión e instancia
        return Evaluacion::where('tipo', 'recuperatorio')
            ->where('materia_id', $evaluacion->materia_id)
            ->where('comision_id', $evaluacion->comision_id)
            ->where('instancia', $evaluacion->instancia)
            ->exists();
    }

    /**
     * Obtener la nota del recuperatorio (si existe)
     */
    public function notaRecuperatorio(): ?Nota
    {
        $evaluacion = $this->evaluacion;
        if (!$evaluacion) {
            return null;
        }

        // Buscar el recuperatorio correspondiente
        $recuperatorio = Evaluacion::where('tipo', 'recuperatorio')
            ->where('materia_id', $evaluacion->materia_id)
            ->where('comision_id', $evaluacion->comision_id)
            ->where('instancia', $evaluacion->instancia)
            ->first();

        if (!$recuperatorio) {
            return null;
        }

        return Nota::where('inscripcion_id', $this->inscripcion_id)
            ->where('evaluacion_id', $recuperatorio->id)
            ->first();
    }

    /**
     * Obtener nota final considerando recuperatorio
     * Si aprobó el parcial, devuelve la nota del parcial
     * Si rindió recuperatorio, devuelve la mayor nota
     */
    public function notaFinal(): ?float
    {
        if ($this->estaAprobado()) {
            return $this->nota;
        }

        $notaRecuperatorio = $this->notaRecuperatorio();
        if ($notaRecuperatorio && $notaRecuperatorio->nota !== null) {
            // Si aprobó el recuperatorio, usa esa nota
            if ($notaRecuperatorio->estaAprobado()) {
                return $notaRecuperatorio->nota;
            }
            // Si no aprobó, devuelve la mayor de las dos
            return max($this->nota ?? 0, $notaRecuperatorio->nota);
        }

        return $this->nota;
    }

    /**
     * Verificar si aprobó considerando recuperatorio
     */
    public function aproboConRecuperatorio(): bool
    {
        if ($this->estaAprobado()) {
            return true;
        }

        $notaRecuperatorio = $this->notaRecuperatorio();
        return $notaRecuperatorio && $notaRecuperatorio->estaAprobado();
    }

    /**
     * Scope para notas de una inscripción
     */
    public function scopeDeInscripcion($query, int $inscripcionId)
    {
        return $query->where('inscripcion_id', $inscripcionId);
    }

    /**
     * Scope para notas de una cursada
     */
    public function scopeDeCursada($query, int $cursadaId)
    {
        return $query->where('cursada_id', $cursadaId);
    }

    /**
     * Scope para notas aprobadas (usando la nota configurable)
     */
    public function scopeAprobadas($query)
    {
        return $query->where('nota', '>=', self::notaAprobacion());
    }

    /**
     * Scope para notas desaprobadas
     */
    public function scopeDesaprobadas($query)
    {
        return $query->where('nota', '<', self::notaAprobacion());
    }
}
