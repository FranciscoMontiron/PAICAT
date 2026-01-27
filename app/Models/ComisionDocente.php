<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ComisionDocente - Asignación de docentes a comisiones
 * 
 * Tabla pivot que permite:
 * - Múltiples docentes por comisión
 * - Historial de asignaciones (activo/inactivo)
 * - Registro de fechas de alta y baja
 */
class ComisionDocente extends Model
{
    protected $table = 'comision_docente';

    protected $fillable = [
        'comision_id',
        'user_id',
        'activo',
        'fecha_asignacion',
        'fecha_baja',
        'observaciones',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_asignacion' => 'datetime',
        'fecha_baja' => 'datetime',
    ];

    /**
     * Relación con la comisión
     */
    public function comision(): BelongsTo
    {
        return $this->belongsTo(Comision::class);
    }

    /**
     * Relación con el usuario (docente)
     */
    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope para asignaciones activas
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para asignaciones inactivas (historial)
     */
    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    /**
     * Dar de baja la asignación
     */
    public function darDeBaja(?string $observaciones = null): void
    {
        $this->update([
            'activo' => false,
            'fecha_baja' => now(),
            'observaciones' => $observaciones ?? $this->observaciones,
        ]);
    }
}
