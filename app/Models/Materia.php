<?php

namespace App\Models;

use App\Services\ConfiguracionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Materia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'materias';

    protected $fillable = [
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

    public static function getTipos(): array
    {
        return ConfiguracionService::get('tipos_materia', self::TIPOS);
    }

    /**
     * Relación N:M con comisiones (una materia puede estar en varias comisiones)
     */
    public function comisiones(): BelongsToMany
    {
        return $this->belongsToMany(Comision::class, 'comision_materia')->withTimestamps();
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
        return "{$this->nombre}";
    }

    /**
     * Calcular estadísticas de asistencia para esta materia en una comisión específica
     * 
     * @param int $comisionId ID de la comisión
     * @return array Estadísticas con total_clases, promedio_asistencia, alumnos_en_riesgo
     */
    public function estadisticasAsistencia(int $comisionId): array
    {
        // Obtener todas las asistencias de esta materia en esta comisión
        $asistencias = $this->asistencias()
            ->whereHas('inscripcionComision', function ($query) use ($comisionId) {
                $query->where('comision_id', $comisionId);
            })
            ->with('inscripcionComision')
            ->get();

        if ($asistencias->isEmpty()) {
            return [
                'total_clases' => 0,
                'promedio_asistencia' => 0,
                'alumnos_en_riesgo' => 0,
            ];
        }

        // Contar clases únicas (fechas distintas)
        $totalClases = $asistencias->pluck('fecha')->unique()->count();

        // Calcular promedio de asistencia
        $totalRegistros = $asistencias->count();
        $presentes = $asistencias->whereIn('estado', ['presente', 'justificado'])->count();
        $tardanzas = $asistencias->where('estado', 'tardanza')->count();
        
        $puntos = $presentes + ($tardanzas * 0.5);
        $promedioAsistencia = $totalRegistros > 0 ? round(($puntos / $totalRegistros) * 100, 1) : 0;

        // Calcular alumnos en riesgo (< 75% de asistencia)
        $inscripciones = $asistencias->pluck('inscripcion_comision_id')->unique();
        $alumnosEnRiesgo = 0;
        $minimoAsistencia = \App\Services\ConfiguracionService::get('asistencia_minima', 75);

        foreach ($inscripciones as $inscripcionId) {
            $asistenciasAlumno = $asistencias->where('inscripcion_comision_id', $inscripcionId);
            $totalAlumno = $asistenciasAlumno->count();
            
            if ($totalAlumno > 0) {
                $presentesAlumno = $asistenciasAlumno->whereIn('estado', ['presente', 'justificado'])->count();
                $tardanzasAlumno = $asistenciasAlumno->where('estado', 'tardanza')->count();
                
                $puntosAlumno = $presentesAlumno + ($tardanzasAlumno * 0.5);
                $porcentajeAlumno = ($puntosAlumno / $totalAlumno) * 100;
                
                if ($porcentajeAlumno < $minimoAsistencia) {
                    $alumnosEnRiesgo++;
                }
            }
        }

        return [
            'total_clases' => $totalClases,
            'promedio_asistencia' => $promedioAsistencia,
            'alumnos_en_riesgo' => $alumnosEnRiesgo,
        ];
    }
}