<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LimpiarBaseDatosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paicat:limpiar-bd
                            {--force : Ejecutar sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia la BD PAICAT dejando solo admin, roles, permisos y datos sysacad';

    /**
     * Tablas que se deben vaciar (datos transaccionales)
     * Orden importante por dependencias de FK
     */
    protected array $tablasAVaciar = [
        // Primero las tablas dependientes
        'notas',
        'asistencias',
        'evaluaciones',
        'cursadas',
        'reincorporaciones',
        'inscripcion_comisiones',
        'comision_docente',
        'comision_materia',
        'cronograma_actividades',
        'trayectorias',
        'solicitudes_cambio',
        'condiciones_particulares',
        'certificados',
        'reportes',
        // Luego las tablas principales
        'comisiones',
        'inscripciones',
        'materias',
        'aulas',
        'municipios',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('==========================================');
        $this->info(' Limpieza de Base de Datos PAICAT');
        $this->info('==========================================');
        $this->info('');

        $this->warn('Esta operación eliminará todos los datos transaccionales:');
        $this->line('  - Inscripciones');
        $this->line('  - Comisiones');
        $this->line('  - Evaluaciones y notas');
        $this->line('  - Asistencias');
        $this->line('  - Trayectorias y solicitudes de cambio');
        $this->line('  - Materias');
        $this->line('  - Certificados y reportes');
        $this->info('');
        $this->info('Se conservarán:');
        $this->line('  - Usuario admin y roles/permisos');
        $this->line('  - Datos maestros de Sysacad (especialidades, turnos, etc.)');
        $this->line('  - Datos de alumnos_utn (base de datos externa)');
        $this->info('');

        if (!$this->option('force')) {
            if (!$this->confirm('¿Deseas continuar con la limpieza?', false)) {
                $this->info('Operación cancelada.');
                return Command::SUCCESS;
            }
        }

        $this->info('');
        $this->info('Iniciando limpieza...');

        // Desactivar verificación de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($this->tablasAVaciar as $tabla) {
                if (Schema::hasTable($tabla)) {
                    DB::table($tabla)->truncate();
                    $this->line("  ✓ Tabla '{$tabla}' vaciada");
                } else {
                    $this->line("  - Tabla '{$tabla}' no existe, omitida");
                }
            }

            // Limpiar usuarios excepto admin
            $adminDeleted = DB::table('users')
                ->where('email', '!=', 'admin@paicat.utn.edu.ar')
                ->delete();

            $this->line("  ✓ Usuarios eliminados (excepto admin): {$adminDeleted}");

        } finally {
            // Reactivar verificación de claves foráneas
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->info('');
        $this->info('==========================================');
        $this->info(' ¡Limpieza completada exitosamente!');
        $this->info('==========================================');
        $this->info('');
        $this->info('La BD está lista para nuevas pruebas.');
        $this->info('');

        return Command::SUCCESS;
    }
}
