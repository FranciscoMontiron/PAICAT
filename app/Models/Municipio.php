<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Municipio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'municipios';

    protected $fillable = [
        'nombre',
        'codigo',
        'direccion',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // ==================== RELACIONES ====================

    /**
     * Comisiones en este municipio
     */
    public function comisiones(): HasMany
    {
        return $this->hasMany(Comision::class);
    }

    /**
     * Aulas en este municipio
     */
    public function aulas(): HasMany
    {
        return $this->hasMany(Aula::class);
    }

    // ==================== SCOPES ====================

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // ==================== MÉTODOS ====================

    /**
     * Cantidad de comisiones activas en el municipio
     */
    public function cantidadComisionesActivas(): int
    {
        return $this->comisiones()->where('estado', 'activa')->count();
    }
}
