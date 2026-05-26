<?php

namespace App\Services;

use App\Models\ConfiguracionVariable;
use App\Models\ConfiguracionHistorial;

class ConfiguracionService
{
    protected static array $cache = [];
    protected static bool $booted = false;

    /**
     * Carga todas las variables en cache (una sola query por request).
     */
    public static function boot(): void
    {
        if (static::$booted) {
            return;
        }

        try {
            $variables = ConfiguracionVariable::all();
            foreach ($variables as $var) {
                static::$cache[$var->clave] = $var->valor_decodificado;
            }
            static::$booted = true;
        } catch (\Exception $e) {
            // La tabla puede no existir aún (durante migraciones)
        }
    }

    /**
     * Obtiene el valor de una variable de configuración.
     * Prioridad: DB > config/paicat.php > default
     */
    public static function get(string $clave, $default = null)
    {
        static::boot();

        if (array_key_exists($clave, static::$cache)) {
            return static::$cache[$clave] ?? $default;
        }

        // Fallback a config/paicat.php
        $configValue = config("paicat.{$clave}");
        if ($configValue !== null) {
            return $configValue;
        }

        return $default;
    }

    /**
     * Establece el valor de una variable y registra el cambio en historial.
     */
    public static function set(
        string $clave,
        $valor,
        ?int $userId = null,
        ?string $motivo = null,
        string $tipoCambio = 'manual'
    ): void {
        $variable = ConfiguracionVariable::where('clave', $clave)->firstOrFail();

        $valorAnterior = $variable->valor;
        $valorNuevo = $variable->tipo === 'json'
            ? json_encode($valor, JSON_UNESCAPED_UNICODE)
            : (string) $valor;

        if ($valorAnterior !== $valorNuevo) {
            ConfiguracionHistorial::create([
                'configuracion_variable_id' => $variable->id,
                'valor_anterior' => $valorAnterior,
                'valor_nuevo' => $valorNuevo,
                'motivo' => $motivo,
                'tipo_cambio' => $tipoCambio,
                'usuario_id' => $userId,
            ]);

            $variable->update(['valor' => $valorNuevo]);
            static::$cache[$clave] = $variable->fresh()->valor_decodificado;
        }
    }

    /**
     * Limpia la cache para forzar recarga.
     */
    public static function clearCache(): void
    {
        static::$cache = [];
        static::$booted = false;
    }

    /**
     * Verifica si hay variables requeridas sin configurar.
     */
    public static function hayRequeridasSinConfigurar(): bool
    {
        try {
            return ConfiguracionVariable::where('requerida', true)
                ->where(function ($q) {
                    $q->whereNull('valor')->orWhere('valor', '');
                })
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Devuelve las variables requeridas que faltan configurar.
     */
    public static function requeridasFaltantes()
    {
        try {
            return ConfiguracionVariable::where('requerida', true)
                ->where(function ($q) {
                    $q->whereNull('valor')->orWhere('valor', '');
                })
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }
}
