<?php

namespace App\Models;

use App\Services\ConfiguracionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comision extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'paicat';
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

    public static function getTiposIngreso(): array
    {
        return self::TIPOS_INGRESO;
    }

    public static function getTurnos(): array
    {
        return self::TURNOS;
    }

    public static function getModalidades(): array
    {
        return self::MODALIDADES;
    }

    public static function getEstados(): array
    {
        return ConfiguracionService::get('estados_comision', [
            'activa' => 'Activa',
            'cerrada' => 'Cerrada',
            'finalizada' => 'Finalizada',
            'cancelada' => 'Cancelada',
        ]);
    }

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
        'extracupos_habilitados',
        'extracupos',
        'docente_id',
        'estado',
        'archivada',
        'observaciones',
    ];

    protected $casts = [
        'anio' => 'integer',
        'cupo_maximo' => 'integer',
        'cupo_actual' => 'integer',
        'extracupos_habilitados' => 'boolean',
        'extracupos' => 'integer',
        'archivada' => 'boolean',
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
     * Obtener el cupo actual REAL contando inscripciones activas
     * No depende del campo cupo_actual (desnormalizado), lo calcula de verdad
     */
    public function getCupoRealAttribute(): int
    {
        return $this->inscripciones()
            ->whereIn('estado', ['inscripto', 'confirmado', 'aprobado'])
            ->count();
    }

    /**
     * Sincronizar el campo cupo_actual con la cantidad real de inscripciones activas
     */
    public function sincronizarCupo(): void
    {
        $cupoReal = $this->cupo_real;
        if ($this->cupo_actual !== $cupoReal) {
            $this->update(['cupo_actual' => $cupoReal]);
        }
    }

    /**
     * Obtener el cupo total efectivo (cupo_maximo + extracupos si están habilitados)
     * Retorna null para comisiones sin límite (virtuales)
     */
    public function getCupoTotalAttribute(): ?int
    {
        if ($this->esVirtual() || is_null($this->cupo_maximo)) {
            return null;
        }

        $total = $this->cupo_maximo;

        if ($this->extracupos_habilitados && $this->extracupos > 0) {
            $total += $this->extracupos;
        }

        return $total;
    }

    /**
     * Verificar si la comisión tiene cupos disponibles
     * Las comisiones virtuales no tienen límite de cupo
     */
    public function tieneCuposDisponibles(): bool
    {
        if ($this->esVirtual() || is_null($this->cupo_maximo)) {
            return true;
        }

        return $this->cupo_real < $this->cupo_total;
    }

    /**
     * Obtener cupos disponibles (basado en cupo real)
     * Retorna null para comisiones sin límite
     */
    public function getCuposDisponiblesAttribute(): ?int
    {
        if ($this->esVirtual() || is_null($this->cupo_maximo)) {
            return null;
        }

        return max(0, $this->cupo_total - $this->cupo_real);
    }

    /**
     * Verificar si la comisión está usando extracupos (superó el cupo base)
     */
    public function estaUsandoExtracupos(): bool
    {
        if (!$this->extracupos_habilitados || !$this->cupo_maximo) {
            return false;
        }

        return $this->cupo_real > $this->cupo_maximo;
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
     * Incrementar cupo actual (sincroniza con la realidad)
     */
    public function incrementarCupo(): void
    {
        $this->sincronizarCupo();
    }

    /**
     * Decrementar cupo actual (sincroniza con la realidad)
     */
    public function decrementarCupo(): void
    {
        $this->sincronizarCupo();
    }

    /**
     * Obtener porcentaje de ocupación (basado en cupo total efectivo)
     */
    public function getPorcentajeOcupacionAttribute(): float
    {
        $cupoTotal = $this->cupo_total;

        if (is_null($cupoTotal) || $cupoTotal == 0) {
            return 0;
        }

        return round(($this->cupo_real / $cupoTotal) * 100, 2);
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
     * Usa subquery para contar inscripciones activas reales
     */
    public function scopeConCuposDisponibles($query)
    {
        return $query->where(function ($q) {
            // Virtuales o sin cupo_maximo: siempre tienen cupo
            $q->whereNull('cupo_maximo')
              ->orWhere('modalidad', 'Virtual')
              // Con cupo: cupo_maximo + extracupos (si habilitados) > inscripciones activas
              ->orWhereRaw('(cupo_maximo + CASE WHEN extracupos_habilitados = 1 THEN extracupos ELSE 0 END) > (SELECT COUNT(*) FROM inscripcion_comisiones WHERE inscripcion_comisiones.comision_id = comisiones.id AND inscripcion_comisiones.estado IN (?, ?, ?) AND inscripcion_comisiones.deleted_at IS NULL)', ['inscripto', 'confirmado', 'aprobado']);
        });
    }

    public function calcularPromedioAsistencia()
    {
        $inscripciones = $this->inscripciones;
        
        if ($inscripciones->isEmpty()) {
            return 0;
        }
        
        $totalPorcentaje = 0;
        $contadorAlumnos = 0;
        
        foreach ($inscripciones as $inscripcion) {
            $porcentaje = $inscripcion->calcularPorcentajeAsistencia();
            $totalPorcentaje += $porcentaje;
            $contadorAlumnos++;
        }
        
        return $contadorAlumnos > 0 ? $totalPorcentaje / $contadorAlumnos : 0;
    }

    public function contarAlumnosEnRiesgo()
    {
        $contador = 0;
        
        foreach ($this->inscripciones as $inscripcion) {
            if ($inscripcion->estaEnRiesgo()) {
                $contador++;
            }
        }
        
        return $contador;
    }
}
