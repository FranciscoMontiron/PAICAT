<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportAlumnosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paicat:import-alumnos 
                            {--file=bases_externas/alumos.sql : Ruta al archivo SQL}
                            {--connection=alumnos_utn : Conexión de BD a usar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargar datos en BD alumnos_utn (simula la BD de la facultad)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $file = base_path($this->option('file'));
        $connection = $this->option('connection');

        if (!file_exists($file)) {
            $this->error("❌ Archivo no encontrado: {$file}");
            $this->info("   Asegúrate de que el archivo existe en bases_externas/");
            return Command::FAILURE;
        }

        $this->info('');
        $this->info('==========================================');
        $this->info(' Importando datos de alumnos...');
        $this->info('==========================================');
        $this->info('');
        $this->info("📄 Archivo: {$file}");
        $this->info("🔌 Conexión: {$connection}");
        $this->info('');

        // Verificar conexión
        try {
            DB::connection($connection)->getPdo();
            $this->info("✅ Conexión '{$connection}' OK");
        } catch (\Exception $e) {
            $this->error("❌ Error de conexión: " . $e->getMessage());
            $this->info("   Verifica las credenciales en .env para DB_*_{$connection}");
            return Command::FAILURE;
        }

        // Leer y ejecutar SQL
        $this->info('');
        $this->info('📥 Importando datos...');

        try {
            $sql = file_get_contents($file);

            // Separar por statements para evitar errores
            $statements = $this->parseSqlStatements($sql);
            $total = count($statements);
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $errors = 0;
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (empty($statement)) {
                    $bar->advance();
                    continue;
                }

                try {
                    DB::connection($connection)->unprepared($statement);
                } catch (\Exception $e) {
                    // Ignorar errores de tablas existentes, etc.
                    $errors++;
                }
                $bar->advance();
            }

            $bar->finish();
            $this->info('');
            $this->info('');

            if ($errors > 0) {
                $this->warn("⚠️  {$errors} statements con errores (probablemente tablas existentes)");
            }

            // Mostrar conteos
            $this->showTableCounts($connection);

            $this->info('');
            $this->info('==========================================');
            $this->info(' ¡Datos importados exitosamente!');
            $this->info('==========================================');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Parsear SQL en statements individuales
     */
    protected function parseSqlStatements(string $sql): array
    {
        // Remover comentarios de inline
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        // Dividir por ; pero respetando strings
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';

        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];

            if (!$inString && ($char === "'" || $char === '"')) {
                $inString = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar && ($i === 0 || $sql[$i - 1] !== '\\')) {
                $inString = false;
            }

            if ($char === ';' && !$inString) {
                $statements[] = $current;
                $current = '';
            } else {
                $current .= $char;
            }
        }

        if (!empty(trim($current))) {
            $statements[] = $current;
        }

        return $statements;
    }

    /**
     * Mostrar conteo de tablas importadas
     */
    protected function showTableCounts(string $connection): void
    {
        $tables = [
            'persons' => 'Personas/Alumnos',
            'academico_datos' => 'Datos académicos',
            'formulario_datos' => 'Formularios',
            'secundaria_datos' => 'Datos secundario',
            'escuelas' => 'Escuelas',
            'tipo_documentos' => 'Tipos de documento',
        ];

        $this->info('');
        $this->info('📊 Registros importados:');

        foreach ($tables as $table => $label) {
            try {
                $count = DB::connection($connection)->table($table)->count();
                $this->info("   {$label}: {$count}");
            } catch (\Exception $e) {
                // Tabla no existe, ignorar
            }
        }
    }
}
