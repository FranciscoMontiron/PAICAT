<?php

namespace App\Models\AlumnosUtn;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo SecundariaDato - Datos del secundario
 * Base de datos: alumnos_utn (Solo lectura desde PAICAT)
 */
class SecundariaDato extends Model
{
    /**
     * La conexión de base de datos que debe ser usada por el modelo.
     */
    protected $connection = 'alumnos_utn';

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'secundaria_datos';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'person_id',
        'titulo',
        'institucion',
        'anio_egreso',
        'tipo_titulo',
        'promedio',
        'pais_estudio',
        'prov_estudio',
        'loc_estudio',
        'part_estudio',
        'tipo_certificado',
        'certificado_captura',
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
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
     * Obtener el nombre del título desde sysacad
     */
    public function getTituloNombreAttribute(): ?string
    {
        if (!$this->titulo) {
            return null;
        }

        $titulo = \DB::connection('paicat')
            ->table('sysacad_titulos_secundarios')
            ->where('id_sysacad', $this->titulo)
            ->first();

        return $titulo?->nombre ?? $this->titulo;
    }

    /**
     * Obtener el nombre del país de estudio desde sysacad
     */
    public function getPaisEstudioNombreAttribute(): ?string
    {
        if (!$this->pais_estudio) {
            return null;
        }

        $pais = \DB::connection('paicat')
            ->table('sysacad_paises')
            ->where('id_sysacad', $this->pais_estudio)
            ->first();

        return $pais?->nombre;
    }

    /**
     * Obtener el nombre de la provincia de estudio desde sysacad
     */
    public function getProvinciaEstudioNombreAttribute(): ?string
    {
        if (!$this->prov_estudio) {
            return null;
        }

        $provincia = \DB::connection('paicat')
            ->table('sysacad_provincias')
            ->where('id_sysacad', $this->prov_estudio)
            ->first();

        return $provincia?->nombre;
    }
}
