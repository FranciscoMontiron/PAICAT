<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionVariable extends Model
{
    protected $table = 'configuracion_variables';

    protected $fillable = [
        'grupo', 'clave', 'valor', 'tipo', 'nombre',
        'descripcion', 'origen', 'tabla_sincronizacion',
        'editable', 'requerida', 'orden',
    ];

    protected $casts = [
        'editable' => 'boolean',
        'requerida' => 'boolean',
        'orden' => 'integer',
    ];

    public function historial()
    {
        return $this->hasMany(ConfiguracionHistorial::class);
    }

    public function getValorDecodificadoAttribute()
    {
        if ($this->tipo === 'json') {
            return json_decode($this->valor, true) ?? [];
        }
        if ($this->tipo === 'numero') {
            return is_numeric($this->valor) ? (float) $this->valor : null;
        }
        if ($this->tipo === 'booleano') {
            return filter_var($this->valor, FILTER_VALIDATE_BOOLEAN);
        }
        return $this->valor;
    }

    public function scopeGrupo($query, string $grupo)
    {
        return $query->where('grupo', $grupo);
    }

    public function scopeRequeridas($query)
    {
        return $query->where('requerida', true);
    }

    public function scopeSincronizables($query)
    {
        return $query->whereNotNull('tabla_sincronizacion');
    }
}
