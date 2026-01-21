<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'evaluaciones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'fecha',
        'peso_porcentual',
        'comision_id',
        'materia_id',
        'anio',
    ];

    protected $casts = [
        'fecha' => 'date',
        'peso_porcentual' => 'decimal:2',
        'anio' => 'integer',
    ];

    /**
     * Tipos disponibles
     */
    const TIPO_PARCIAL = 'parcial';
    const TIPO_RECUPERATORIO = 'recuperatorio';
    const TIPO_EXAMEN_FINAL = 'examen_final';
    const TIPO_TRABAJO_PRACTICO = 'trabajo_practico';

    const TIPOS = [
        self::TIPO_PARCIAL => 'Parcial',
        self::TIPO_RECUPERATORIO => 'Recuperatorio',
        self::TIPO_EXAMEN_FINAL => 'Examen Final',
        self::TIPO_TRABAJO_PRACTICO => 'Trabajo Práctico',
    ];

    /**
     * Relación con la materia
     */
    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    /**
     * Relación con la comisión (opcional - para evaluaciones específicas de una comisión)
     */
    public function comision(): BelongsTo
    {
        return $this->belongsTo(Comision::class);
    }

    /**
     * Notas de esta evaluación
     */
    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }

    /**
     * Scope para filtrar por materia
     */
    public function scopeDeMateria($query, int $materiaId)
    {
        return $query->where('materia_id', $materiaId);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Obtener nombre del tipo formateado
     */
    public function getTipoNombreAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }
}
