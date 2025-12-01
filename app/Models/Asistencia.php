<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';

    protected $fillable = [
        'inscripcion_comision_id',
        'fecha',
        'estado',
        'observaciones',
        'registrado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function inscripcionComision(): BelongsTo
    {
        return $this->belongsTo(InscripcionComision::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}

