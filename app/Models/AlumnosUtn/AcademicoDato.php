<?php

namespace App\Models\AlumnosUtn;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo AcademicoDato - Datos académicos del ingreso
 * Base de datos: alumnos_utn (Solo lectura desde PAICAT)
 */
class AcademicoDato extends Model
{
    /**
     * La conexión de base de datos que debe ser usada por el modelo.
     */
    protected $connection = 'alumnos_utn';

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'academico_datos';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'person_id',
        'especialidad_id',
        'especialidad_alternativa_id',
        'ingreso_carrera',
        'egreso_secundaria',
        'modalidad',
        'turno_ingreso',
        'turno_carrera',
        'tipo_ingreso',
        'sede',
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ingreso_carrera' => 'integer',
        'egreso_secundaria' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con la persona
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    /**
     * Obtener el nombre de la especialidad desde sysacad
     */
    public function getEspecialidadNombreAttribute(): ?string
    {
        if (!$this->especialidad_id) {
            return null;
        }

        $especialidad = \DB::connection('paicat')
            ->table('sysacad_especialidades')
            ->where('id_sysacad', $this->especialidad_id)
            ->first();

        return $especialidad?->nombre;
    }

    /**
     * Obtener el nombre de la especialidad alternativa desde sysacad
     */
    public function getEspecialidadAlternativaNombreAttribute(): ?string
    {
        if (!$this->especialidad_alternativa_id) {
            return null;
        }

        $especialidad = \DB::connection('paicat')
            ->table('sysacad_especialidades')
            ->where('id_sysacad', $this->especialidad_alternativa_id)
            ->first();

        return $especialidad?->nombre;
    }

    /**
     * Scope para filtrar por año de ingreso
     */
    public function scopeAnioIngreso($query, int $anio)
    {
        return $query->where('ingreso_carrera', $anio);
    }

    /**
     * Scope para filtrar por especialidad
     */
    public function scopeEspecialidad($query, int $especialidadId)
    {
        return $query->where('especialidad_id', $especialidadId);
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
}
