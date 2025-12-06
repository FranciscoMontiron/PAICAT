<?php

namespace App\Models;

use App\Models\AlumnosUtn\AcademicoDato;
use App\Models\AlumnosUtn\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'estado',
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
     * Estados disponibles
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
     * Obtener el color del badge según el estado
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
     * Scope para inscripciones pendientes de validación
     */
    public function scopePendientesValidacion($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
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
