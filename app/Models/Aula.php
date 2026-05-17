<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aula extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'aulas';

    protected $fillable = [
        'nombre',
        'codigo',
        'capacidad',
        'municipio_id',
        'ubicacion',
        'activa',
        'observaciones',
    ];

    protected $casts = [
        'capacidad' => 'integer',
        'activa' => 'boolean',
    ];

    // ==================== RELACIONES ====================

    /**
     * Municipio al que pertenece el aula
     */
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }

    /**
     * Comisiones que usan este aula
     */
    public function comisiones(): HasMany
    {
        return $this->hasMany(Comision::class);
    }

    // ==================== SCOPES ====================

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeDelMunicipio($query, $municipioId)
    {
        return $query->where('municipio_id', $municipioId);
    }

    // ==================== MÉTODOS ====================

    /**
     * Nombre completo con municipio
     */
    public function getNombreCompletoAttribute(): string
    {
        $nombre = $this->nombre;
        if ($this->codigo) {
            $nombre .= " ({$this->codigo})";
        }
        if ($this->municipio) {
            $nombre .= " - {$this->municipio->nombre}";
        }
        return $nombre;
    }

    /**
     * Verificar si el aula está disponible (no tiene comisiones activas)
     */
    public function estaDisponible(): bool
    {
        return !$this->comisiones()->where('estado', 'activa')->exists();
    }
}
