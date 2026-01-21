<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trayectoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'trayectorias';

    protected $fillable = [
        'inscripcion_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'motivo',
        'es_voluntaria',
        'registrado_por',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'es_voluntaria' => 'boolean',
    ];

    /**
     * Estados disponibles
     */
    const ESTADO_ACTIVO = 'activo';
    const ESTADO_PAUSADO = 'pausado';
    const ESTADO_LIBRE = 'libre';
    const ESTADO_BAJA = 'baja';
    const ESTADO_REINCORPORADO = 'reincorporado';
    const ESTADO_APROBADO = 'aprobado';
    const ESTADO_DESAPROBADO = 'desaprobado';

    const ESTADOS = [
        self::ESTADO_ACTIVO => 'Activo',
        self::ESTADO_PAUSADO => 'Pausado',
        self::ESTADO_LIBRE => 'Libre',
        self::ESTADO_BAJA => 'Baja',
        self::ESTADO_REINCORPORADO => 'Reincorporado',
        self::ESTADO_APROBADO => 'Aprobado',
        self::ESTADO_DESAPROBADO => 'Desaprobado',
    ];

    /**
     * Relación con la inscripción
     */
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    /**
     * Usuario que registró el cambio
     */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /**
     * Nombre del estado formateado
     */
    public function getEstadoNombreAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    /**
     * Verificar si está activo
     */
    public function isActivo(): bool
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }

    /**
     * Verificar si es una baja
     */
    public function isBaja(): bool
    {
        return $this->estado === self::ESTADO_BAJA;
    }

    /**
     * Scope para trayectorias activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    /**
     * Scope para trayectorias vigentes (sin fecha_fin)
     */
    public function scopeVigentes($query)
    {
        return $query->whereNull('fecha_fin');
    }
}
