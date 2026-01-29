<?php

namespace App\Models;

use App\Models\AlumnosUtn\AcademicoDato;
use App\Models\AlumnosUtn\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Modelo Inscripcion - Inscripciones al curso de ingreso
 * Base de datos: paicat
 */
class Inscripcion extends Model
{
    use SoftDeletes;

    /**
     * La conexión de base de datos que debe ser usada por el modelo.
     */
    protected $connection = 'paicat';

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'inscripciones';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'person_id',
        'academico_dato_id',
        'anio_ingreso',
        'especialidad_id_sysacad',
        'especialidad_alternativa_id_sysacad',
        'modalidad',
        'turno_ingreso',
        'turno_carrera',
        'tipo_ingreso',
        'sede_id_sysacad',
        'estado', // Legacy - mantenido para compatibilidad
        'estado_documentacion',
        'estado_ingreso',
        'doc_dni_validado',
        'doc_titulo_validado',
        'doc_analitico_validado',
        'observaciones_documentacion',
        'usuario_registro_id',
        'usuario_validacion_id',
        'fecha_validacion',
        'observaciones',
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'anio_ingreso' => 'integer',
        'doc_dni_validado' => 'boolean',
        'doc_titulo_validado' => 'boolean',
        'doc_analitico_validado' => 'boolean',
        'fecha_validacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Estados disponibles (legacy - para compatibilidad)
     */
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_DOCUMENTACION_OK = 'documentacion_ok';
    const ESTADO_CONFIRMADO = 'confirmado';
    const ESTADO_CANCELADO = 'cancelado';
    const ESTADO_BAJA = 'baja';

    const ESTADOS = [
        self::ESTADO_PENDIENTE => 'Pendiente',
        self::ESTADO_DOCUMENTACION_OK => 'Documentación Validada',
        self::ESTADO_CONFIRMADO => 'Confirmado',
        self::ESTADO_CANCELADO => 'Cancelado',
        self::ESTADO_BAJA => 'Baja',
    ];

    // ==================== ESTADOS SEPARADOS ====================

    /**
     * Estados de documentación
     */
    const DOC_PENDIENTE = 'pendiente';
    const DOC_VALIDADA = 'validada';
    const DOC_INCOMPLETA = 'incompleta';
    const DOC_RECHAZADA = 'rechazada';

    const ESTADOS_DOCUMENTACION = [
        self::DOC_PENDIENTE => 'Pendiente',
        self::DOC_VALIDADA => 'Validada',
        self::DOC_INCOMPLETA => 'Incompleta',
        self::DOC_RECHAZADA => 'Rechazada',
    ];

    /**
     * Estados de ingreso/cursada
     */
    const INGRESO_INSCRIPTO = 'inscripto';
    const INGRESO_CURSANDO = 'cursando';
    const INGRESO_APROBADO = 'aprobado';
    const INGRESO_DESAPROBADO = 'desaprobado';
    const INGRESO_LIBRE = 'libre';
    const INGRESO_BAJA = 'baja';
    const INGRESO_CANCELADO = 'cancelado';

    const ESTADOS_INGRESO = [
        self::INGRESO_INSCRIPTO => 'Inscripto',
        self::INGRESO_CURSANDO => 'Cursando',
        self::INGRESO_APROBADO => 'Aprobado',
        self::INGRESO_DESAPROBADO => 'Desaprobado',
        self::INGRESO_LIBRE => 'Libre',
        self::INGRESO_BAJA => 'Baja',
        self::INGRESO_CANCELADO => 'Cancelado',
    ];

    /**
     * Modalidades disponibles
     */
    const MODALIDADES = [
        'Presencial' => 'Presencial',
        'Virtual' => 'Virtual',
        'Semipresencial' => 'Semipresencial',
    ];

    /**
     * Tipos de ingreso disponibles
     */
    const TIPOS_INGRESO = [
        'Intensivo' => 'Intensivo',
        'Extensivo' => 'Extensivo',
    ];

    /**
     * Obtener la persona (alumno) desde alumnos_utn
     * Nota: No es una relación Eloquent tradicional por ser otra BD
     */
    public function getPerson(): ?Person
    {
        if (!$this->person_id) {
            return null;
        }

        return Person::on('alumnos_utn')->find($this->person_id);
    }

    /**
     * Obtener los datos académicos desde alumnos_utn
     */
    public function getAcademicoDato(): ?AcademicoDato
    {
        if (!$this->academico_dato_id) {
            return null;
        }

        return AcademicoDato::on('alumnos_utn')->find($this->academico_dato_id);
    }

    /**
     * Relación con el usuario que registró la inscripción
     */
    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_registro_id');
    }

    /**
     * Relación con el usuario que validó la documentación
     */
    public function usuarioValidacion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_validacion_id');
    }

    /**
     * Trayectorias del estudiante
     */
    public function trayectorias(): HasMany
    {
        return $this->hasMany(Trayectoria::class);
    }

    /**
     * Trayectoria actual (la más reciente sin fecha_fin)
     */
    public function trayectoriaActual()
    {
        return $this->hasOne(Trayectoria::class)->whereNull('fecha_fin')->latest();
    }

    /**
     * Solicitudes de cambio
     */
    public function solicitudesCambio(): HasMany
    {
        return $this->hasMany(SolicitudCambio::class);
    }

    /**
     * Condiciones particulares
     */
    public function condicionesParticulares(): HasMany
    {
        return $this->hasMany(CondicionParticular::class);
    }

    /**
     * Asistencias del estudiante (desacopladas de comisión)
     */
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    /**
     * Notas del estudiante (desacopladas de comisión)
     */
    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }

    /**
     * Inscripciones a comisiones
     */
    public function inscripcionesComision(): HasMany
    {
        return $this->hasMany(InscripcionComision::class);
    }

    /**
     * Obtener el nombre de la especialidad desde sysacad
     */
    public function getEspecialidadNombreAttribute(): ?string
    {
        if (empty($this->especialidad_id_sysacad)) {
            return null;
        }

        try {
            $especialidad = DB::connection('sysacad')->table('sysacad_especialidades')
                ->where('id_sysacad', $this->especialidad_id_sysacad)
                ->first();

            return $especialidad?->nombre;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Obtener el nombre de la especialidad alternativa desde sysacad
     */
    public function getEspecialidadAlternativaNombreAttribute(): ?string
    {
        if (empty($this->especialidad_alternativa_id_sysacad)) {
            return null;
        }

        try {
            $especialidad = DB::connection('sysacad')->table('sysacad_especialidades')
                ->where('id_sysacad', $this->especialidad_alternativa_id_sysacad)
                ->first();

            return $especialidad?->nombre;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Accessors para compatibilidad con las vistas (aliases)
     */
    public function getDniValidadoAttribute(): bool
    {
        return (bool) $this->doc_dni_validado;
    }

    public function getTituloValidadoAttribute(): bool
    {
        return (bool) $this->doc_titulo_validado;
    }

    public function getAnaliticoValidadoAttribute(): bool
    {
        return (bool) $this->doc_analitico_validado;
    }

    /**
     * Obtener el nombre del estado formateado
     */
    public function getEstadoNombreAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    /**
     * Obtener el color del badge según el estado (legacy)
     */
    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_PENDIENTE => 'yellow',
            self::ESTADO_DOCUMENTACION_OK => 'blue',
            self::ESTADO_CONFIRMADO => 'green',
            self::ESTADO_CANCELADO => 'red',
            self::ESTADO_BAJA => 'gray',
            default => 'gray',
        };
    }

    // ==================== ACCESSORS ESTADOS SEPARADOS ====================

    /**
     * Obtener el nombre del estado de documentación formateado
     */
    public function getEstadoDocumentacionNombreAttribute(): string
    {
        return self::ESTADOS_DOCUMENTACION[$this->estado_documentacion] ?? $this->estado_documentacion ?? 'N/A';
    }

    /**
     * Obtener el nombre del estado de ingreso formateado
     */
    public function getEstadoIngresoNombreAttribute(): string
    {
        return self::ESTADOS_INGRESO[$this->estado_ingreso] ?? $this->estado_ingreso ?? 'N/A';
    }

    /**
     * Obtener el color del badge según el estado de documentación
     */
    public function getEstadoDocumentacionColorAttribute(): string
    {
        return match ($this->estado_documentacion) {
            self::DOC_PENDIENTE => 'yellow',
            self::DOC_VALIDADA => 'green',
            self::DOC_INCOMPLETA => 'orange',
            self::DOC_RECHAZADA => 'red',
            default => 'gray',
        };
    }

    /**
     * Obtener el color del badge según el estado de ingreso
     */
    public function getEstadoIngresoColorAttribute(): string
    {
        return match ($this->estado_ingreso) {
            self::INGRESO_INSCRIPTO => 'blue',
            self::INGRESO_CURSANDO => 'indigo',
            self::INGRESO_APROBADO => 'green',
            self::INGRESO_DESAPROBADO => 'red',
            self::INGRESO_LIBRE => 'orange',
            self::INGRESO_BAJA => 'gray',
            self::INGRESO_CANCELADO => 'red',
            default => 'gray',
        };
    }

    /**
     * Verificar si la documentación está validada
     */
    public function documentacionValidada(): bool
    {
        return $this->estado_documentacion === self::DOC_VALIDADA;
    }

    /**
     * Verificar si está cursando activamente
     */
    public function estaCursando(): bool
    {
        return $this->estado_ingreso === self::INGRESO_CURSANDO;
    }

    /**
     * Verificar si aprobó el ingreso
     */
    public function aproboIngreso(): bool
    {
        return $this->estado_ingreso === self::INGRESO_APROBADO;
    }

    /**
     * Verificar si está activo (no dado de baja ni cancelado)
     */
    public function estaActivo(): bool
    {
        return !in_array($this->estado_ingreso, [self::INGRESO_BAJA, self::INGRESO_CANCELADO]);
    }

    /**
     * Verificar si la documentación está completa
     */
    public function documentacionCompleta(): bool
    {
        return $this->doc_dni_validado &&
            $this->doc_titulo_validado &&
            $this->doc_analitico_validado;
    }

    /**
     * Verificar si se puede modificar la inscripción
     */
    public function puedeModificarse(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_PENDIENTE,
            self::ESTADO_DOCUMENTACION_OK,
        ]);
    }

    /**
     * Verificar si se puede cancelar la inscripción
     */
    public function puedeCancelarse(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_PENDIENTE,
            self::ESTADO_DOCUMENTACION_OK,
            self::ESTADO_CONFIRMADO,
        ]);
    }

    /**
     * Verificar si es un duplicado potencial
     */
    public static function esDuplicado(int $personId, int $anioIngreso): bool
    {
        return self::where('person_id', $personId)
            ->where('anio_ingreso', $anioIngreso)
            ->whereNotIn('estado', [self::ESTADO_CANCELADO, self::ESTADO_BAJA])
            ->exists();
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para filtrar por año de ingreso
     */
    public function scopeAnioIngreso($query, int $anio)
    {
        return $query->where('anio_ingreso', $anio);
    }

    /**
     * Scope para filtrar por especialidad
     */
    public function scopeEspecialidad($query, int $especialidadId)
    {
        return $query->where('especialidad_id_sysacad', $especialidadId);
    }

    /**
     * Scope para filtrar por modalidad
     */
    public function scopeModalidad($query, string $modalidad)
    {
        return $query->where('modalidad', $modalidad);
    }

    /**
     * Scope para filtrar por tipo de ingreso
     */
    public function scopeTipoIngreso($query, string $tipo)
    {
        return $query->where('tipo_ingreso', $tipo);
    }

    /**
     * Scope para inscripciones activas (no canceladas ni baja)
     */
    public function scopeActivas($query)
    {
        return $query->whereNotIn('estado', [self::ESTADO_CANCELADO, self::ESTADO_BAJA]);
    }

    /**
     * Scope para inscripciones pendientes de validación (legacy)
     */
    public function scopePendientesValidacion($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    // ==================== SCOPES ESTADOS SEPARADOS ====================

    /**
     * Scope para filtrar por estado de documentación
     */
    public function scopeEstadoDocumentacion($query, string $estado)
    {
        return $query->where('estado_documentacion', $estado);
    }

    /**
     * Scope para filtrar por estado de ingreso
     */
    public function scopeEstadoIngreso($query, string $estado)
    {
        return $query->where('estado_ingreso', $estado);
    }

    /**
     * Scope para inscripciones con documentación pendiente
     */
    public function scopeDocumentacionPendiente($query)
    {
        return $query->where('estado_documentacion', self::DOC_PENDIENTE);
    }

    /**
     * Scope para inscripciones con documentación validada
     */
    public function scopeDocumentacionValidada($query)
    {
        return $query->where('estado_documentacion', self::DOC_VALIDADA);
    }

    /**
     * Scope para inscripciones cursando
     */
    public function scopeCursando($query)
    {
        return $query->where('estado_ingreso', self::INGRESO_CURSANDO);
    }

    /**
     * Scope para inscripciones aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado_ingreso', self::INGRESO_APROBADO);
    }

    /**
     * Scope para inscripciones activas (nuevo - basado en estado_ingreso)
     */
    public function scopeActivasNuevo($query)
    {
        return $query->whereNotIn('estado_ingreso', [self::INGRESO_BAJA, self::INGRESO_CANCELADO]);
    }

    /**
     * Scope para búsqueda por datos del alumno
     */
    public function scopeBuscarAlumno($query, string $termino)
    {
        // Buscar en alumnos_utn y obtener los person_ids
        $personIds = Person::on('alumnos_utn')
            ->buscar($termino)
            ->pluck('id')
            ->toArray();

        return $query->whereIn('person_id', $personIds);
    }
}
