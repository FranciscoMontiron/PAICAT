<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaFinalMateria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nota_final_materias';

    protected $fillable = [
        'inscripcion_id',
        'comision_id',
        'materia_id',
        'nota_final',
        'nota_aprobacion_snapshot',
        'cargado_por',
        'observaciones',
    ];

    protected $casts = [
        'nota_final' => 'decimal:2',
        'nota_aprobacion_snapshot' => 'decimal:2',
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function comision(): BelongsTo
    {
        return $this->belongsTo(Comision::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function cargadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cargado_por');
    }

    public function estaAprobada(): bool
    {
        // Usar el snapshot guardado al momento en que se cargó la nota.
        // Si no tiene snapshot (registros anteriores a esta funcionalidad), usar 6.
        $notaMinima = $this->nota_aprobacion_snapshot ?? 6;
        return $this->nota_final !== null && $this->nota_final >= $notaMinima;
    }
}
