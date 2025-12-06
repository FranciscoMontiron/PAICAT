<?php

namespace App\Models\AlumnosUtn;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo FormularioDato - Estado del formulario de inscripción
 * Base de datos: alumnos_utn (Solo lectura desde PAICAT)
 */
class FormularioDato extends Model
{
    /**
     * La conexión de base de datos que debe ser usada por el modelo.
     */
    protected $connection = 'alumnos_utn';

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'formulario_datos';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'person_id',
        'estado',
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
     * Verificar si el formulario está completo
     */
    public function estaCompleto(): bool
    {
        return $this->estado === 'Completo';
    }

    /**
     * Scope para formularios completos
     */
    public function scopeCompletos($query)
    {
        return $query->where('estado', 'Completo');
    }

    /**
     * Scope para formularios incompletos
     */
    public function scopeIncompletos($query)
    {
        return $query->where('estado', 'Incompleto');
    }
}
