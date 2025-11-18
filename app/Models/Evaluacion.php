<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'evaluaciones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'fecha',
        'peso_porcentual',
        'comision_id',
        'anio',
    ];

    protected $casts = [
        'fecha' => 'date',
        'peso_porcentual' => 'decimal:2',
        'anio' => 'integer',
    ];

    public function comision(): BelongsTo
    {
        return $this->belongsTo(Comision::class);
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }
}

