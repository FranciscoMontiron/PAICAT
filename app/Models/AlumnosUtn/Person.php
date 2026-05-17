<?php

namespace App\Models\AlumnosUtn;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Person - Datos personales de los alumnos
 * Base de datos: alumnos_utn (Solo lectura desde PAICAT)
 */
class Person extends Model
{
    /**
     * La conexión de base de datos que debe ser usada por el modelo.
     */
    protected $connection = 'alumnos_utn';

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'persons';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'cuit',
        'cuit_captura',
        'estado_civil',
        'nacionalidad',
        'documento',
        'documento_captura',
        'tipo_documento',
        'pais_documento',
        'pais_origen',
        'nacimiento_fecha',
        'sexo',
        'genero',
        'numero_casa',
        'pais_residencia',
        'prov_residencia',
        'loc_residencia',
        'part_residencia',
        'piso',
        'departamento',
        'direccion',
        'barrio',
        'codigo_postal',
        'telefono_fijo',
        'telefono_celular',
        'telefono_emergencia',
        'contacto_emergencia',
        'email',
        '__proceso',
        '__estado',
        '__usuario',
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'nacimiento_fecha' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con datos académicos
     */
    public function academicoDatos(): HasMany
    {
        return $this->hasMany(AcademicoDato::class, 'person_id');
    }

    /**
     * Relación con datos del secundario
     */
    public function secundariaDato(): HasOne
    {
        return $this->hasOne(SecundariaDato::class, 'person_id');
    }

    /**
     * Relación con estado del formulario
     */
    public function formularioDato(): HasOne
    {
        return $this->hasOne(FormularioDato::class, 'person_id');
    }

    /**
     * Obtener nombre completo del alumno
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->apellido}, {$this->nombre}");
    }

    /**
     * Obtener nombre completo formateado (Nombre Apellido)
     */
    public function getNombreCompletoInversoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido}");
    }

    /**
     * Verificar si la documentación está completa
     */
    public function documentacionCompleta(): bool
    {
        return $this->__estado === 'Verificado' &&
               $this->formularioDato?->estado === 'Completo';
    }

    /**
     * Scope para filtrar por documento
     */
    public function scopeByDocumento($query, string $documento)
    {
        return $query->where('documento', $documento);
    }

    /**
     * Scope para filtrar por estado verificado
     */
    public function scopeVerificados($query)
    {
        return $query->where('__estado', 'Verificado');
    }

    /**
     * Scope para filtrar por estado pendiente
     */
    public function scopePendientes($query)
    {
        return $query->where('__estado', 'Pendiente');
    }

    /**
     * Scope para búsqueda general
     */
    public function scopeBuscar($query, string $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('apellido', 'like', "%{$termino}%")
              ->orWhere('documento', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%");
        });
    }
}
