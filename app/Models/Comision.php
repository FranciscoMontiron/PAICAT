<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comision extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'comisiones';

    /**
     * Tipos de periodo disponibles (igual que tipo_ingreso en Inscripcion)
     */
    const TIPOS_INGRESO = [
        'Intensivo' => 'Intensivo',
        'Extensivo' => 'Extensivo',
    ];

    /**
     * Turnos disponibles (valores de la BD de alumnos)
     */
    const TURNOS = [
        'mañana' => 'Mañana',
        'tardenoche' => 'TardeNoche',
    ];

    /**
     * Modalidades disponibles
     */
    const MODALIDADES = [
        'Presencial' => 'Presencial',
        'Virtual' => 'Virtual',
        'Semipresencial' => 'Semipresencial',
    ];

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'anio',
        'periodo',
        'turno',
        'modalidad',
        'municipio_id',
        'aula_id',
        'cupo_maximo',
        'cupo_actual',
        'docente_id',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'anio' => 'integer',
        'cupo_maximo' => 'integer',
        'cupo_actual' => 'integer',
    ];

    /**
     * Relación N:M con materias (una comisión tiene varias materias)
     */
    public function materias(): BelongsToMany
    {
        return $this->belongsToMany(Materia::class, 'comision_materia')->withTimestamps();
    }

    /**
     * Relación con el docente asignado (legacy, para compatibilidad)
     */
    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    /**
     * Relación con municipio (sede presencial)
     */
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }

    /**
     * Relación con aula
     */
    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }

    /**
     * Cursadas de esta comisión (historial de estudiantes que cursaron)
     */
    public function cursadas(): HasMany
    {
        return $this->hasMany(Cursada::class);
    }

    /**
     * Cursadas activas de esta comisión
     */
    public function cursadasActivas(): HasMany
    {
        return $this->hasMany(Cursada::class)->where('estado', 'cursando');
    }

    /**
     * Todas las asignaciones de docentes (activos e inactivos)
     */
    public function asignacionesDocentes(): HasMany
    {
        return $this->hasMany(ComisionDocente::class);
    }

    /**
     * Docentes actualmente asignados (solo activos)
     */
    public function docentesActivos(): HasMany
    {
        return $this->hasMany(ComisionDocente::class)->where('activo', true);
    }

    /**
     * Historial de docentes (solo inactivos)
     */
    public function historialDocentes(): HasMany
    {
        return $this->hasMany(ComisionDocente::class)->where('activo', false);
    }

    /**
     * Relación N:M con usuarios docentes (para acceso directo)
     */
    public function docentes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comision_docente')
            ->withPivot(['activo', 'fecha_asignacion', 'fecha_baja', 'observaciones'])
            ->withTimestamps();
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
     * Las comisiones virtuales no tienen límite de cupo
     */
    public function tieneCuposDisponibles(): bool
    {
        // Virtual: sin límite de cupo
        if ($this->esVirtual()) {
            return true;
        }

        // Si cupo_maximo es null o 0, es sin límite
        if (!$this->cupo_maximo) {
            return true;
        }

        return $this->cupo_actual < $this->cupo_maximo;
    }

    /**
     * Obtener cupos disponibles
     * Retorna null para comisiones sin límite
     */
    public function getCuposDisponiblesAttribute(): ?int
    {
        // Virtual o sin límite definido
        if ($this->esVirtual() || !$this->cupo_maximo) {
            return null; // Sin límite
        }

        return max(0, $this->cupo_maximo - $this->cupo_actual);
    }

    /**
     * Verificar si es comisión virtual
     */
    public function esVirtual(): bool
    {
        return strtolower($this->modalidad ?? '') === 'virtual';
    }

    /**
     * Verificar si es comisión semipresencial
     */
    public function esSemipresencial(): bool
    {
        return strtolower($this->modalidad ?? '') === 'semipresencial';
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
