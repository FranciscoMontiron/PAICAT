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

    /**
     * Relación con la inscripción de comisión
     */
    public function inscripcionComision(): BelongsTo
    {
        return $this->belongsTo(InscripcionComision::class);
    }

    /**
     * Usuario que registró la asistencia
     */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    // Scopes útiles
    public function scopePresentes($query)
    {
        return $query->where('estado', 'presente');
    }

    public function scopeAusentes($query)
    {
        return $query->where('estado', 'ausente');
    }

    public function scopeFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }
}
