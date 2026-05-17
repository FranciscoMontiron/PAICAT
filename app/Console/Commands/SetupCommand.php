<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paicat:setup 
                            {--fresh : Ejecutar migrate:fresh en lugar de migrate}
                            {--seed : Ejecutar seeders después de las migraciones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configurar PAICAT: migraciones, seeders, cache y permisos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('==========================================');
        $this->info(' Configurando PAICAT...');
        $this->info('==========================================');
        $this->info('');

        // Generar clave de aplicación si no existe
        if (empty(env('APP_KEY')) || env('APP_KEY') === 'base64:') {
            $this->info('🔑 Generando clave de aplicación...');
            $this->call('key:generate', ['--force' => true]);
        }

        // Limpiar caché
        $this->info('🧹 Limpiando caché...');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        // Ejecutar migraciones
        $this->info('');
        if ($this->option('fresh')) {
            $this->info('🗄️  Ejecutando migrate:fresh...');
            $this->call('migrate:fresh', ['--force' => true]);
        } else {
            $this->info('🗄️  Ejecutando migraciones...');
            $this->call('migrate', ['--force' => true]);
        }

        // Ejecutar seeders
        if ($this->option('seed') || $this->option('fresh')) {
            $this->info('');
            $this->info('🌱 Ejecutando seeders...');
            $this->call('db:seed', ['--force' => true]);
        }

        // Crear enlace simbólico de storage
        $this->info('');
        $this->info('📁 Creando enlace simbólico de storage...');
        $this->callSilently('storage:link');

        // Optimizar aplicación
        $this->info('');
        $this->info('⚡ Optimizando aplicación...');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');

        $this->info('');
        $this->info('==========================================');
        $this->info(' ¡PAICAT configurado exitosamente!');
        $this->info('==========================================');
        $this->info('');
        $this->info(' Credenciales de acceso:');
        $this->info('   Email: admin@paicat.utn.edu.ar');
        $this->info('   Password: admin123');
        $this->info('');
        $this->info(' Accede a la aplicación en: http://localhost');
        $this->info('');

        return Command::SUCCESS;
    }
}
