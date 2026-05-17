<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';

    protected $fillable = [
        'inscripcion_id',
        'materia_id',
        'inscripcion_comision_id',
        'fecha',
        'estado',
        'observaciones',
        'registrado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Estados disponibles
     */
    const ESTADO_PRESENTE = 'presente';
    const ESTADO_AUSENTE = 'ausente';
    const ESTADO_TARDANZA = 'tardanza';
    const ESTADO_JUSTIFICADO = 'justificado';

    const ESTADOS = [
        self::ESTADO_PRESENTE => 'Presente',
        self::ESTADO_AUSENTE => 'Ausente',
        self::ESTADO_TARDANZA => 'Tardanza',
        self::ESTADO_JUSTIFICADO => 'Justificado',
    ];

    public static function getEstados(): array
    {
        return self::ESTADOS;
    }

    /**
     * Relación con la inscripción (alumno + año) - NUEVA RELACIÓN PRINCIPAL
     */
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    /**
     * Relación con la materia - NUEVA RELACIÓN
     */
    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
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
     * Usuario que registró la asistencia
     */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /**
     * Obtener nombre del estado formateado
     */
    public function getEstadoNombreAttribute(): string
    {
        return static::getEstados()[$this->estado] ?? $this->estado;
    }

    /**
     * Verificar si es una inasistencia (ausente o tardanza no justificada)
     */
    public function esInasistencia(): bool
    {
        return in_array($this->estado, [self::ESTADO_AUSENTE, self::ESTADO_TARDANZA]);
    }

    // Scopes útiles
    public function scopePresentes($query)
    {
        return $query->where('estado', self::ESTADO_PRESENTE);
    }

    public function scopeAusentes($query)
    {
        return $query->where('estado', self::ESTADO_AUSENTE);
    }

    public function scopeJustificados($query)
    {
        return $query->where('estado', self::ESTADO_JUSTIFICADO);
    }

    public function scopeFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function scopeDeInscripcion($query, int $inscripcionId)
    {
        return $query->where('inscripcion_id', $inscripcionId);
    }

    public function scopeDeMateria($query, int $materiaId)
    {
        return $query->where('materia_id', $materiaId);
    }
}
