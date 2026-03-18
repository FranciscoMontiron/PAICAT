<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\ConfiguracionService;

class Cursada extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'paicat';
    protected $table = 'cursadas';

    /**
     * Estados posibles de una cursada
     */
    const ESTADO_CURSANDO = 'cursando';
    const ESTADO_APROBADO = 'aprobado';
    const ESTADO_DESAPROBADO = 'desaprobado';
    const ESTADO_LIBRE = 'libre';
    const ESTADO_ABANDONO = 'abandono';
    const ESTADO_BAJA = 'baja';

    const ESTADOS = [
        self::ESTADO_CURSANDO => 'Cursando',
        self::ESTADO_APROBADO => 'Aprobado',
        self::ESTADO_DESAPROBADO => 'Desaprobado',
        self::ESTADO_LIBRE => 'Libre',
        self::ESTADO_ABANDONO => 'Abandonó',
        self::ESTADO_BAJA => 'Baja',
    ];

    public static function getEstados(): array
    {
        return self::ESTADOS;
    }

    /**
     * Nota mínima para aprobar (ingreso UTN)
     */
    const NOTA_APROBACION = 6;

    protected $fillable = [
        'inscripcion_id',
        'comision_id',
        'anio',
        'estado',
        'modalidad',
        'resultado',
        'nota_final',
        'es_recursante',
        'fecha_inicio',
        'fecha_fin',
        'usuario_cambio_estado_id',
        'fecha_cambio_estado',
        'observaciones',
    ];

    protected $casts = [
        'anio' => 'integer',
        'nota_final' => 'decimal:2',
        'es_recursante' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_cambio_estado' => 'datetime',
    ];

    // ==================== RELACIONES ====================

    /**
     * Inscripción (estudiante) al que pertenece esta cursada
     */
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    /**
     * Comisión en la que se cursa
     */
    public function comision(): BelongsTo
    {
        return $this->belongsTo(Comision::class);
    }

    /**
     * Usuario que cambió el estado
     */
    public function usuarioCambioEstado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_cambio_estado_id');
    }

    /**
     * Asistencias de esta cursada
     */
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    /**
     * Notas de esta cursada
     */
    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }

    /**
     * Reincorporaciones de esta cursada
     */
    public function reincorporaciones(): HasMany
    {
        return $this->hasMany(Reincorporacion::class);
    }

    // ==================== SCOPES ====================

    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_CURSANDO);
    }

    public function scopeFinalizadas($query)
    {
        return $query->whereIn('estado', [
            self::ESTADO_APROBADO,
            self::ESTADO_DESAPROBADO,
            self::ESTADO_LIBRE,
        ]);
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', self::ESTADO_APROBADO);
    }

    public function scopeDelAnio($query, int $anio)
    {
        return $query->where('anio', $anio);
    }

    public function scopeRecursantes($query)
    {
        return $query->where('es_recursante', true);
    }

    // ==================== MÉTODOS ====================

    /**
     * Verificar si la cursada está activa
     */
    public function estaActiva(): bool
    {
        return $this->estado === self::ESTADO_CURSANDO;
    }

    /**
     * Verificar si el estudiante aprobó
     */
    public function aprobo(): bool
    {
        return $this->estado === self::ESTADO_APROBADO;
    }

    /**
     * Verificar si la cursada está finalizada
     */
    public function estaFinalizada(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_APROBADO,
            self::ESTADO_DESAPROBADO,
            self::ESTADO_LIBRE,
            self::ESTADO_ABANDONO,
            self::ESTADO_BAJA,
        ]);
    }

    /**
     * Cambiar estado de la cursada
     */
    public function cambiarEstado(string $nuevoEstado, ?int $usuarioId = null, ?string $observacion = null): bool
    {
        if (!array_key_exists($nuevoEstado, self::ESTADOS)) {
            return false;
        }

        $this->estado = $nuevoEstado;
        $this->usuario_cambio_estado_id = $usuarioId ?? auth()->id();
        $this->fecha_cambio_estado = now();

        if ($observacion) {
            $this->observaciones = ($this->observaciones ? $this->observaciones . "\n" : '') .
                "[" . now()->format('d/m/Y H:i') . "] Estado cambiado a {$nuevoEstado}: {$observacion}";
        }

        // Si se aprueba o desaprueba, registrar fecha fin
        if (in_array($nuevoEstado, [self::ESTADO_APROBADO, self::ESTADO_DESAPROBADO, self::ESTADO_LIBRE])) {
            $this->fecha_fin = $this->fecha_fin ?? now();
        }

        return $this->save();
    }

    /**
     * Calcular y actualizar nota final basada en las notas registradas
     */
    public function calcularNotaFinal(): ?float
    {
        $notas = $this->notas()
            ->whereHas('evaluacion', function ($q) {
                $q->where('cuenta_promedio', true);
            })
            ->whereNotNull('nota')
            ->get();

        if ($notas->isEmpty()) {
            return null;
        }

        $promedio = $notas->avg('nota');
        $this->nota_final = round($promedio, 2);
        $this->save();

        return $this->nota_final;
    }

    /**
     * Determinar si aprueba basado en nota final
     */
    public function determinaAprobacion(): bool
    {
        $notaMinima = ConfiguracionService::get('nota_aprobacion', 6);
        return $this->nota_final !== null && $this->nota_final >= $notaMinima;
    }

    /**
     * Calcular porcentaje de asistencia
     */
    public function porcentajeAsistencia(): float
    {
        $total = $this->asistencias()->count();

        if ($total === 0) {
            return 0;
        }

        $presentes = $this->asistencias()->where('presente', true)->count();

        return round(($presentes / $total) * 100, 2);
    }

    /**
     * Obtener datos del estudiante (persona)
     */
    public function getEstudiante()
    {
        return $this->inscripcion?->getPerson();
    }

    /**
     * Nombre completo del estudiante
     */
    public function getNombreEstudianteAttribute(): string
    {
        $persona = $this->getEstudiante();
        return $persona ? "{$persona->apellido}, {$persona->nombre}" : 'Sin datos';
    }

    /**
     * Verificar si es la primera vez que cursa esta materia
     */
    public function esPrimeraCursada(): bool
    {
        return !self::where('inscripcion_id', $this->inscripcion_id)
            ->where('comision_id', '!=', $this->comision_id)
            ->whereHas('comision', function ($q) {
                $q->whereIn('materias.id', $this->comision->materias->pluck('id'));
            })
            ->exists();
    }
}
