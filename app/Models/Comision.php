<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comision extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'comisiones';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'anio',
        'periodo',
        'turno',
        'modalidad',
        'cupo_maximo',
        'cupo_actual',
        'docente_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'anio' => 'integer',
        'cupo_maximo' => 'integer',
        'cupo_actual' => 'integer',
    ];

    /**
     * Relación con el docente asignado
     */
    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    /**
     * Inscripciones a esta comisión
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(InscripcionComision::class, 'comision_id');
    }

    /**
     * Asistencias de esta comisión
     */
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'comision_id');
    }

    /**
     * Evaluaciones de esta comisión
     */
    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'comision_id');
    }

    /**
     * Actividades del cronograma de esta comisión
     */
    public function cronogramaActividades(): HasMany
    {
        return $this->hasMany(CronogramaActividad::class, 'comision_id');
    }

    /**
     * Verificar si la comisión tiene cupos disponibles
     */
    public function tieneCuposDisponibles(): bool
    {
        return $this->cupo_actual < $this->cupo_maximo;
    }

    /**
     * Obtener cupos disponibles
     */
    public function getCuposDisponiblesAttribute(): int
    {
        return max(0, $this->cupo_maximo - $this->cupo_actual);
    }

    /**
     * Verificar si la comisión está activa
     */
    public function isActiva(): bool
    {
        return $this->estado === 'activa';
    }

    /**
     * Verificar si la comisión puede recibir inscripciones
     */
    public function puedeRecibirInscripciones(): bool
    {
        return $this->isActiva() && $this->tieneCuposDisponibles();
    }

    /**
     * Incrementar cupo actual
     */
    public function incrementarCupo(): void
    {
        if ($this->cupo_actual < $this->cupo_maximo) {
            $this->increment('cupo_actual');
        }
    }

    /**
     * Decrementar cupo actual
     */
    public function decrementarCupo(): void
    {
        if ($this->cupo_actual > 0) {
            $this->decrement('cupo_actual');
        }
    }

    /**
     * Obtener porcentaje de ocupación
     */
    public function getPorcentajeOcupacionAttribute(): float
    {
        if ($this->cupo_maximo == 0) {
            return 0;
        }
        return round(($this->cupo_actual / $this->cupo_maximo) * 100, 2);
    }

    /**
     * Scope para filtrar por año
     */
    public function scopeAnio($query, $anio)
    {
        return $query->where('anio', $anio);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para comisiones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    /**
     * Scope para comisiones con cupos disponibles
     */
    public function scopeConCuposDisponibles($query)
    {
        return $query->whereRaw('cupo_actual < cupo_maximo');
    }
}

