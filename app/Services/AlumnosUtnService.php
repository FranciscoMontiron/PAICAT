<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Servicio para gestionar la conexión con la base de datos alumnos_utn.
 *
 * La BD alumnos_utn es una base de datos externa (solo lectura) que puede:
 *  - Estar en el mismo servidor MariaDB del sistema (mismo host que paicat).
 *  - Estar en un servidor remoto configurado con DB_HOST_ALUMNOS.
 *  - No estar disponible (en ese caso el sistema funciona con funcionalidad reducida).
 *
 * Variables de entorno relevantes en .env:
 *   DB_HOST_ALUMNOS      → Host del servidor (por defecto usa DB_HOST)
 *   DB_PORT_ALUMNOS      → Puerto (por defecto usa DB_PORT)
 *   DB_DATABASE_ALUMNOS  → Nombre de la base de datos (por defecto: alumnos_utn)
 *   DB_USERNAME_ALUMNOS  → Usuario (por defecto usa DB_USERNAME)
 *   DB_PASSWORD_ALUMNOS  → Contraseña (por defecto usa DB_PASSWORD)
 *
 * Si la BD está en el mismo servidor que paicat y el usuario tiene permisos,
 * solo es necesario definir DB_DATABASE_ALUMNOS=alumnos_utn (las demás variables
 * se heredan automáticamente de la conexión principal).
 */
class AlumnosUtnService
{
    /** @var bool|null Cache por petición de disponibilidad */
    private static ?bool $disponible = null;

    /** @var string|null Mensaje de error de la última verificación */
    private static ?string $mensajeError = null;

    /**
     * Verifica si la conexión y las tablas de alumnos_utn están disponibles.
     * El resultado se cachea durante la vida de la petición HTTP.
     */
    public static function isAvailable(): bool
    {
        if (self::$disponible !== null) {
            return self::$disponible;
        }

        try {
            DB::connection('alumnos_utn')->getPdo();
            // Verificar que la tabla persons existe
            DB::connection('alumnos_utn')->table('persons')->where('id', 0)->exists();
            self::$disponible = true;
            self::$mensajeError = null;
        } catch (\Exception $e) {
            self::$disponible = false;
            self::$mensajeError = self::resolverMensajeError($e->getMessage());
        }

        return self::$disponible;
    }

    /**
     * Retorna un mensaje descriptivo sobre por qué la BD no está disponible,
     * junto con consideraciones para el administrador.
     */
    public static function getMensajeNoDisponible(): string
    {
        // Asegurarse de que se ejecutó la verificación
        if (self::$disponible === null) {
            self::isAvailable();
        }

        return self::$mensajeError
            ?? 'No se pudo establecer conexión con la base de datos de alumnos.';
    }

    /**
     * Resetea el cache (útil para tests).
     */
    public static function reset(): void
    {
        self::$disponible = null;
        self::$mensajeError = null;
    }

    /**
     * Convierte el mensaje de excepción en un texto amigable con consideraciones.
     */
    private static function resolverMensajeError(string $errorOriginal): string
    {
        $base = 'No se encontró conexión con la base de datos de alumnos (alumnos_utn).';
        $consideraciones = self::getConsideraciones();

        // Tabla inexistente
        if (str_contains($errorOriginal, 'doesn\'t exist') || str_contains($errorOriginal, 'Base table or view not found')) {
            return "{$base} La base de datos existe pero aún no tiene tablas inicializadas. "
                . "Ejecutá: docker compose exec app php artisan paicat:import-alumnos\n\n"
                . $consideraciones;
        }

        // Base de datos inexistente
        if (str_contains($errorOriginal, 'Unknown database')) {
            return "{$base} La base de datos indicada en DB_DATABASE_ALUMNOS no existe en el servidor. "
                . "Verificá que la BD fue creada o ejecutá el import.\n\n"
                . $consideraciones;
        }

        // Error de acceso / credenciales
        if (str_contains($errorOriginal, 'Access denied')) {
            return "{$base} Las credenciales configuradas no tienen acceso. "
                . "Revisá DB_USERNAME_ALUMNOS y DB_PASSWORD_ALUMNOS en el archivo .env.\n\n"
                . $consideraciones;
        }

        // Error de conexión al servidor
        if (
            str_contains($errorOriginal, 'Connection refused')
            || str_contains($errorOriginal, 'getaddrinfo')
            || str_contains($errorOriginal, 'php_network_getaddresses')
        ) {
            return "{$base} No se puede alcanzar el host configurado en DB_HOST_ALUMNOS. "
                . "Verificá que el servidor de BD esté activo y accesible.\n\n"
                . $consideraciones;
        }

        return "{$base}\n\n{$consideraciones}";
    }

    /**
     * Texto de ayuda con las variables de entorno y escenarios de configuración.
     */
    private static function getConsideraciones(): string
    {
        return "Consideraciones:\n"
            . "• Si la BD alumnos_utn está en el mismo servidor: "
            . "solo definí DB_DATABASE_ALUMNOS=alumnos_utn en .env (las demás variables se heredan).\n"
            . "• Si está en un servidor externo: configurá DB_HOST_ALUMNOS, DB_PORT_ALUMNOS, "
            . "DB_DATABASE_ALUMNOS, DB_USERNAME_ALUMNOS y DB_PASSWORD_ALUMNOS en .env.\n"
            . "• Para cargar los datos de prueba: docker compose exec app php artisan paicat:import-alumnos";
    }
}
