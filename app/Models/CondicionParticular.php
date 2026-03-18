<?php

namespace App\Models;

use App\Services\ConfiguracionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CondicionParticular extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'condiciones_particulares';

    protected $fillable = [
        'inscripcion_id',
        'tipo',
        'titulo',
        'descripcion',
        'requiere_adecuacion',
        'adecuaciones_sugeridas',
        'adecuaciones_aprobadas',
        'documentacion_presentada',
        'archivo_documentacion',
        'fecha_inicio',
        'fecha_fin',
        'activa',
        'registrado_por',
        'aprobado_por',
        'fecha_aprobacion',
    ];

    protected $casts = [
        'requiere_adecuacion' => 'boolean',
        'documentacion_presentada' => 'boolean',
        'activa' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_aprobacion' => 'datetime',
    ];

    /**
     * Tipos de condición
     */
    const TIPO_DISCAPACIDAD = 'discapacidad';
    const TIPO_ENFERMEDAD_CRONICA = 'enfermedad_cronica';
    const TIPO_SITUACION_LABORAL = 'situacion_laboral';
    const TIPO_SITUACION_FAMILIAR = 'situacion_familiar';
    const TIPO_CONDICIONALIDAD = 'condicionalidad_academica';
    const TIPO_OTRA = 'otra';

    const TIPOS = [
        self::TIPO_DISCAPACIDAD => 'Discapacidad',
        self::TIPO_ENFERMEDAD_CRONICA => 'Enfermedad Crónica',
        self::TIPO_SITUACION_LABORAL => 'Situación Laboral',
        self::TIPO_SITUACION_FAMILIAR => 'Situación Familiar',
        self::TIPO_CONDICIONALIDAD => 'Condicionalidad Académica',
        self::TIPO_OTRA => 'Otra',
    ];

    public static function getTipos(): array
    {
        return ConfiguracionService::get('tipos_condicion', self::TIPOS);
    }

    /**
     * Relación con la inscripción
     */
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    /**
     * Usuario que registró la condición
     */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /**
     * Usuario que aprobó la condición
     */
    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    /**
     * Nombre del tipo formateado
     */
    public function getTipoNombreAttribute(): string
    {
        return static::getTipos()[$this->tipo] ?? $this->tipo;
    }

    /**
     * Verificar si está vigente
     */
    public function isVigente(): bool
    {
        if (!$this->activa) {
            return false;
        }

        $hoy = now()->startOfDay();

        if ($this->fecha_inicio && $this->fecha_inicio->gt($hoy)) {
            return false;
        }

        if ($this->fecha_fin && $this->fecha_fin->lt($hoy)) {
            return false;
        }

        return true;
    }

    /**
     * Scope para condiciones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para condiciones vigentes
     */
    public function scopeVigentes($query)
    {
        $hoy = now()->toDateString();
        return $query->where('activa', true)
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_inicio')
                    ->orWhere('fecha_inicio', '<=', $hoy);
            })
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', $hoy);
            });
    }

    /**
     * Scope por tipo
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
