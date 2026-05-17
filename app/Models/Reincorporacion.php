<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reincorporacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reincorporaciones';

    protected $fillable = [
        'cursada_id',
        'fecha',
        'motivo',
        'estado_anterior',
        'autorizado_por_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // ==================== RELACIONES ====================

    /**
     * Cursada a la que pertenece esta reincorporación
     */
    public function cursada(): BelongsTo
    {
        return $this->belongsTo(Cursada::class);
    }

    /**
     * Usuario que autorizó la reincorporación
     */
    public function autorizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizado_por_id');
    }

    // ==================== MÉTODOS ====================

    /**
     * Procesar la reincorporación: cambiar estado de cursada a 'cursando'
     */
    public function procesar(): bool
    {
        $cursada = $this->cursada;

        if (!$cursada) {
            return false;
        }

        // Guardar estado anterior
        $this->estado_anterior = $cursada->estado;
        $this->save();

        // Cambiar estado de la cursada a cursando
        return $cursada->cambiarEstado(
            Cursada::ESTADO_CURSANDO,
            $this->autorizado_por_id,
            "Reincorporación autorizada. Motivo: {$this->motivo}"
        );
    }
}
