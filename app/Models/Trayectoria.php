<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
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
    const ESTADO_CANCELADO = 'cancelado';

    const ESTADOS = [
        self::ESTADO_ACTIVO => 'Activo',
        self::ESTADO_PAUSADO => 'Pausado',
        self::ESTADO_LIBRE => 'Libre',
        self::ESTADO_BAJA => 'Baja',
        self::ESTADO_REINCORPORADO => 'Reincorporado',
        self::ESTADO_APROBADO => 'Aprobado',
        self::ESTADO_DESAPROBADO => 'Desaprobado',
        self::ESTADO_CANCELADO => 'Cancelado',
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
     * Verificar si es una cancelación
     */
    public function isCancelado(): bool
    {
        return $this->estado === self::ESTADO_CANCELADO;
    }

    /**
     * Registrar un nuevo evento en la trayectoria
     */
    public static function registrarEvento(
        int $inscripcionId,
        string $estado,
        string $motivo = null,
        bool $esVoluntaria = null,
        int $registradoPor = null
    ): self {
        return DB::transaction(function () use ($inscripcionId, $estado, $motivo, $esVoluntaria, $registradoPor) {
            // Cerrar trayectoria anterior vigente usando DB::table con lockForUpdate
            // para evitar error MariaDB 1020 "Record has changed since last read"
            $trayectoriasPrevias = DB::table('trayectorias')
                ->where('inscripcion_id', $inscripcionId)
                ->whereNull('fecha_fin')
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->get();

            if ($trayectoriasPrevias->isNotEmpty()) {
                DB::table('trayectorias')
                    ->where('inscripcion_id', $inscripcionId)
                    ->whereNull('fecha_fin')
                    ->whereNull('deleted_at')
                    ->update(['fecha_fin' => now(), 'updated_at' => now()]);
            }

            // Crear nueva trayectoria
            return self::create([
                'inscripcion_id' => $inscripcionId,
                'estado' => $estado,
                'fecha_inicio' => now(),
                'fecha_fin' => null,
                'motivo' => $motivo,
                'es_voluntaria' => $esVoluntaria,
                'registrado_por' => $registradoPor ?? auth()->id(),
            ]);
        });
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
