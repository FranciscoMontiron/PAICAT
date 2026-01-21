<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Materia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'materias';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'anio_cursado',
        'especialidad_id_sysacad',
        'es_nivelacion',
        'carga_horaria',
        'tipo',
        'activa',
    ];

    protected $casts = [
        'anio_cursado' => 'integer',
        'especialidad_id_sysacad' => 'integer',
        'es_nivelacion' => 'boolean',
        'carga_horaria' => 'integer',
        'activa' => 'boolean',
    ];

    /**
     * Tipos disponibles
     */
    const TIPO_OBLIGATORIA = 'obligatoria';
    const TIPO_OPTATIVA = 'optativa';
    const TIPO_NIVELACION = 'nivelacion';

    const TIPOS = [
        self::TIPO_OBLIGATORIA => 'Obligatoria',
        self::TIPO_OPTATIVA => 'Optativa',
        self::TIPO_NIVELACION => 'Nivelación',
    ];

    /**
     * Comisiones de esta materia
     */
    public function comisiones(): HasMany
    {
        return $this->hasMany(Comision::class, 'materia_id');
    }

    /**
     * Evaluaciones de esta materia
     */
    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'materia_id');
    }

    /**
     * Asistencias registradas para esta materia
     */
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'materia_id');
    }

    /**
     * Obtener el nombre de la especialidad desde sysacad
     */
    public function getEspecialidadNombreAttribute(): ?string
    {
        if (empty($this->especialidad_id_sysacad)) {
            return 'Común a todas';
        }

        try {
            $especialidad = DB::connection('paicat')->table('sysacad_especialidades')
                ->where('id_sysacad', $this->especialidad_id_sysacad)
                ->first();

            return $especialidad?->nombre;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Scope para materias activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para materias de nivelación
     */
    public function scopeNivelacion($query)
    {
        return $query->where('es_nivelacion', true);
    }

    /**
     * Scope para materias de una especialidad (incluyendo comunes)
     */
    public function scopeParaEspecialidad($query, ?int $especialidadId)
    {
        return $query->where(function ($q) use ($especialidadId) {
            $q->whereNull('especialidad_id_sysacad')
                ->orWhere('especialidad_id_sysacad', $especialidadId);
        });
    }

    /**
     * Obtener nombre completo con código
     */
    public function getNombreCompletoAttribute(): string
    {
        return "[{$this->codigo}] {$this->nombre}";
    }
}
