<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitudCambio extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'paicat';
    protected $table = 'solicitudes_cambio';

    protected $fillable = [
        'inscripcion_id',
        'tipo',
        'comision_origen_id',
        'comision_destino_id',
        'modalidad_origen',
        'modalidad_destino',
        'turno_origen',
        'turno_destino',
        'especialidad_origen_id',
        'especialidad_destino_id',
        'motivo',
        'estado',
        'solicitud_trueque_id',
        'motivo_rechazo',
        'procesado_por',
        'fecha_procesamiento',
    ];

    protected $casts = [
        'fecha_procesamiento' => 'datetime',
    ];

    /**
     * Tipos de solicitud
     */
    const TIPO_COMISION = 'comision';
    const TIPO_MODALIDAD = 'modalidad';
    const TIPO_CARRERA = 'carrera';
    const TIPO_TURNO = 'turno';

    const TIPOS = [
        self::TIPO_COMISION => 'Cambio de Comisión',
        self::TIPO_MODALIDAD => 'Cambio de Modalidad',
        self::TIPO_CARRERA => 'Cambio de Carrera',
        self::TIPO_TURNO => 'Cambio de Turno',
    ];

    /**
     * Estados de solicitud
     */
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_EN_REVISION = 'en_revision';
    const ESTADO_APROBADA = 'aprobada';
    const ESTADO_RECHAZADA = 'rechazada';
    const ESTADO_CANCELADA = 'cancelada';
    const ESTADO_TRUEQUE_DETECTADO = 'trueque_detectado';

    const ESTADOS = [
        self::ESTADO_PENDIENTE => 'Pendiente',
        self::ESTADO_EN_REVISION => 'En Revisión',
        self::ESTADO_APROBADA => 'Aprobada',
        self::ESTADO_RECHAZADA => 'Rechazada',
        self::ESTADO_CANCELADA => 'Cancelada',
        self::ESTADO_TRUEQUE_DETECTADO => 'Trueque Detectado',
    ];

    public static function getTipos(): array
    {
        return self::TIPOS;
    }

    public static function getEstados(): array
    {
        return self::ESTADOS;
    }

    /**
     * Relación con la inscripción
     */
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    /**
     * Comisión de origen
     */
    public function comisionOrigen(): BelongsTo
    {
        return $this->belongsTo(Comision::class, 'comision_origen_id');
    }

    /**
     * Comisión de destino
     */
    public function comisionDestino(): BelongsTo
    {
        return $this->belongsTo(Comision::class, 'comision_destino_id');
    }

    /**
     * Solicitud de trueque vinculada
     */
    public function solicitudTrueque(): BelongsTo
    {
        return $this->belongsTo(SolicitudCambio::class, 'solicitud_trueque_id');
    }

    /**
     * Usuario que procesó la solicitud
     */
    public function procesadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'procesado_por');
    }

    /**
     * Nombre del tipo formateado
     */
    public function getTipoNombreAttribute(): string
    {
        return static::getTipos()[$this->tipo] ?? $this->tipo;
    }

    /**
     * Nombre del estado formateado
     */
    public function getEstadoNombreAttribute(): string
    {
        return static::getEstados()[$this->estado] ?? $this->estado;
    }

    /**
     * Verificar si está pendiente
     */
    public function isPendiente(): bool
    {
        return in_array($this->estado, [self::ESTADO_PENDIENTE, self::ESTADO_EN_REVISION]);
    }

    /**
     * Verificar si puede ser procesada (pendiente, en revisión o trueque detectado)
     */
    public function puedeSerProcesada(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_PENDIENTE,
            self::ESTADO_EN_REVISION,
            self::ESTADO_TRUEQUE_DETECTADO,
        ]);
    }

    /**
     * Detectar posible trueque (RF11)
     * Busca solicitudes inversas que coincidan
     */
    public static function detectarTrueque(SolicitudCambio $solicitud): ?SolicitudCambio
    {
        if ($solicitud->tipo !== self::TIPO_COMISION) {
            return null;
        }

        return self::where('tipo', self::TIPO_COMISION)
            ->where('estado', self::ESTADO_PENDIENTE)
            ->where('comision_origen_id', $solicitud->comision_destino_id)
            ->where('comision_destino_id', $solicitud->comision_origen_id)
            ->where('id', '!=', $solicitud->id)
            ->first();
    }

    /**
     * Scope para solicitudes pendientes
     */
    public function scopePendientes($query)
    {
        return $query->whereIn('estado', [self::ESTADO_PENDIENTE, self::ESTADO_EN_REVISION]);
    }

    /**
     * Scope para solicitudes de un tipo
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
