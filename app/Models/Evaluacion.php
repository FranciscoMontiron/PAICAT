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
        'instancia',
        'cuenta_promedio',
        'evaluacion_padre_id',
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
        'instancia' => 'integer',
        'cuenta_promedio' => 'boolean',
    ];

    /**
     * Tipos disponibles
     */
    const TIPO_PARCIAL = 'parcial';
    const TIPO_RECUPERATORIO = 'recuperatorio';
    const TIPO_EXAMEN_FINAL = 'examen_final';
    const TIPO_TRABAJO_PRACTICO = 'trabajo_practico';
    const TIPO_INTEGRADOR = 'integrador';

    const TIPOS = [
        self::TIPO_PARCIAL => 'Parcial',
        self::TIPO_RECUPERATORIO => 'Recuperatorio',
        self::TIPO_EXAMEN_FINAL => 'Examen Final',
        self::TIPO_TRABAJO_PRACTICO => 'Trabajo Práctico',
        self::TIPO_INTEGRADOR => 'Integrador',
    ];

    /**
     * Instancias de evaluación disponibles (desde config)
     */
    public static function instanciasDisponibles(): array
    {
        return config('paicat.instancias_evaluacion', [
            1 => 'Primera instancia (1er Parcial)',
            2 => 'Segunda instancia (2do Parcial)',
            3 => 'Tercera instancia (3er Parcial)',
        ]);
    }

    /**
     * Tipos de evaluación disponibles (desde config)
     */
    public static function tiposDisponibles(): array
    {
        return config('paicat.tipos_evaluacion', self::TIPOS);
    }

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
     * Evaluación padre (para recuperatorios que recuperan un parcial específico)
     */
    public function evaluacionPadre(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_padre_id');
    }

    /**
     * Recuperatorios de esta evaluación
     */
    public function recuperatorios(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'evaluacion_padre_id');
    }

    /**
     * Verificar si es un recuperatorio
     */
    public function esRecuperatorio(): bool
    {
        return $this->tipo === self::TIPO_RECUPERATORIO;
    }

    /**
     * Verificar si es un parcial
     */
    public function esParcial(): bool
    {
        return $this->tipo === self::TIPO_PARCIAL;
    }

    /**
     * Verificar si tiene recuperatorios asociados
     */
    public function tieneRecuperatorios(): bool
    {
        return $this->recuperatorios()->exists();
    }

    /**
     * Obtener el nombre de la instancia
     */
    public function getInstanciaNombreAttribute(): ?string
    {
        if (!$this->instancia) {
            return null;
        }

        $instancias = self::instanciasDisponibles();
        return $instancias[$this->instancia] ?? "Instancia {$this->instancia}";
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
