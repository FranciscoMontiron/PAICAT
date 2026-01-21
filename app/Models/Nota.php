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
        'inscripcion_id',
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

    /**
     * Relación con la inscripción (alumno + año) - NUEVA RELACIÓN PRINCIPAL
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
     * Mantenida por compatibilidad y trazabilidad
     */
    public function inscripcionComision(): BelongsTo
    {
        return $this->belongsTo(InscripcionComision::class);
    }

    /**
     * Usuario que cargó la nota
     */
    public function cargadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cargado_por');
    }

    /**
     * Verificar si está aprobado (nota >= 4)
     */
    public function estaAprobado(): bool
    {
        return $this->nota >= 4;
    }

    /**
     * Scope para notas de una inscripción
     */
    public function scopeDeInscripcion($query, int $inscripcionId)
    {
        return $query->where('inscripcion_id', $inscripcionId);
    }

    /**
     * Scope para notas aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('nota', '>=', 4);
    }

    /**
     * Scope para notas desaprobadas
     */
    public function scopeDesaprobadas($query)
    {
        return $query->where('nota', '<', 4);
    }
}
