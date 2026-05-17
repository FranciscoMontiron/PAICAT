<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionHistorial extends Model
{
    protected $table = 'configuracion_historial';

    protected $fillable = [
        'configuracion_variable_id', 'valor_anterior', 'valor_nuevo',
        'motivo', 'tipo_cambio', 'usuario_id',
    ];

    public function variable()
    {
        return $this->belongsTo(ConfiguracionVariable::class, 'configuracion_variable_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function getValorAnteriorDecodificadoAttribute()
    {
        $decoded = json_decode($this->valor_anterior, true);
        return $decoded !== null ? $decoded : $this->valor_anterior;
    }

    public function getValorNuevoDecodificadoAttribute()
    {
        $decoded = json_decode($this->valor_nuevo, true);
        return $decoded !== null ? $decoded : $this->valor_nuevo;
    }
}
