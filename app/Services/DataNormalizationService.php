<?php

namespace App\Services;

class DataNormalizationService
{
    /**
     * Mapeo de correcciones conocidas para turnos
     */
    private static array $turnoCorrecciones = [
        'manaña' => 'mañana',
        'manana' => 'mañana',
        'maniana' => 'mañana',
        'tarde-noche' => 'tardenoche',
        'tarde noche' => 'tardenoche',
        'tardenoche' => 'tardenoche',
    ];

    /**
     * Mapeo de correcciones conocidas para modalidades
     */
    private static array $modalidadCorrecciones = [
        'presencial' => 'Presencial',
        'virtual' => 'Virtual',
        'semipresencial' => 'Semipresencial',
        'semi-presencial' => 'Semipresencial',
        'semi presencial' => 'Semipresencial',
        'semipresnecial' => 'Semipresencial',  // Typo común
    ];

    /**
     * Mapeo de correcciones conocidas para tipo de ingreso
     */
    private static array $tipoIngresoCorrecciones = [
        'intensivo' => 'Intensivo',
        'extensivo' => 'Extensivo',
        'intesivo' => 'Intensivo',  // Typo común
        'extencivo' => 'Extensivo',  // Typo común
    ];

    /**
     * Correcciones aplicadas durante el proceso
     */
    private array $correccionesAplicadas = [];

    /**
     * Normalizar turno
     */
    public function normalizarTurno(?string $turno): ?string
    {
        if (!$turno) {
            return null;
        }

        $turnoOriginal = $turno;
        $turnoLower = strtolower(trim($turno));

        // Buscar coincidencia en el mapa de correcciones
        $turnoCorregido = self::$turnoCorrecciones[$turnoLower] ?? null;

        if ($turnoCorregido && $turnoOriginal !== $turnoCorregido) {
            $this->registrarCorreccion('turno', $turnoOriginal, $turnoCorregido);
            return $turnoCorregido;
        }

        // Si no hay corrección, capitalizar primera letra
        return $turnoLower;
    }

    /**
     * Normalizar modalidad
     */
    public function normalizarModalidad(?string $modalidad): ?string
    {
        if (!$modalidad) {
            return 'Presencial'; // Default
        }

        $modalidadOriginal = $modalidad;
        $modalidadLower = strtolower(trim($modalidad));

        // Buscar coincidencia en el mapa de correcciones
        $modalidadCorregida = self::$modalidadCorrecciones[$modalidadLower] ?? null;

        if ($modalidadCorregida && $modalidadOriginal !== $modalidadCorregida) {
            $this->registrarCorreccion('modalidad', $modalidadOriginal, $modalidadCorregida);
            return $modalidadCorregida;
        }

        // Si no hay corrección, capitalizar primera letra
        return ucfirst($modalidadLower);
    }

    /**
     * Normalizar tipo de ingreso
     */
    public function normalizarTipoIngreso(?string $tipoIngreso): ?string
    {
        if (!$tipoIngreso) {
            return 'Extensivo'; // Default
        }

        $tipoOriginal = $tipoIngreso;
        $tipoLower = strtolower(trim($tipoIngreso));

        // Buscar coincidencia en el mapa de correcciones
        $tipoCorregido = self::$tipoIngresoCorrecciones[$tipoLower] ?? null;

        if ($tipoCorregido && $tipoOriginal !== $tipoCorregido) {
            $this->registrarCorreccion('tipo_ingreso', $tipoOriginal, $tipoCorregido);
            return $tipoCorregido;
        }

        // Si no hay corrección, capitalizar primera letra
        return ucfirst($tipoLower);
    }

    /**
     * Registrar una corrección aplicada
     */
    private function registrarCorreccion(string $campo, string $valorOriginal, string $valorCorregido): void
    {
        if (!isset($this->correccionesAplicadas[$campo])) {
            $this->correccionesAplicadas[$campo] = [];
        }

        $key = "{$valorOriginal}→{$valorCorregido}";
        if (!isset($this->correccionesAplicadas[$campo][$key])) {
            $this->correccionesAplicadas[$campo][$key] = [
                'original' => $valorOriginal,
                'corregido' => $valorCorregido,
                'cantidad' => 0,
            ];
        }

        $this->correccionesAplicadas[$campo][$key]['cantidad']++;
    }

    /**
     * Obtener resumen de correcciones aplicadas
     */
    public function getResumenCorrecciones(): array
    {
        return $this->correccionesAplicadas;
    }

    /**
     * Obtener mensaje de correcciones para mostrar al usuario
     */
    public function getMensajeCorrecciones(): ?string
    {
        if (empty($this->correccionesAplicadas)) {
            return null;
        }

        $mensajes = [];

        foreach ($this->correccionesAplicadas as $campo => $correcciones) {
            foreach ($correcciones as $correccion) {
                $mensajes[] = sprintf(
                    "%s: '%s' → '%s' (%d registro%s)",
                    ucfirst(str_replace('_', ' ', $campo)),
                    $correccion['original'],
                    $correccion['corregido'],
                    $correccion['cantidad'],
                    $correccion['cantidad'] > 1 ? 's' : ''
                );
            }
        }

        return "Correcciones automáticas aplicadas:\n• " . implode("\n• ", $mensajes);
    }

    /**
     * Reiniciar contador de correcciones
     */
    public function reset(): void
    {
        $this->correccionesAplicadas = [];
    }
}
