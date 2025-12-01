<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicoDato extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'academico_datos';

    protected $fillable = [
        'user_id',
        'especialidad_id',
        'especialidad_alternativa_id',
        'ingreso_carrera',
        'egreso_secundaria',
        'modalidad',
        'turno_ingreso',
        'turno_carrera',
        'tipo_ingreso',
        'sede',
        'estado',
    ];

    protected $casts = [
        'ingreso_carrera' => 'integer',
        'egreso_secundaria' => 'integer',
    ];

    /**
     * Usuario asociado
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Inscripciones a comisiones
     */
    public function inscripcionesComisiones(): HasMany
    {
        return $this->hasMany(InscripcionComision::class, 'academico_dato_id');
    }
}

