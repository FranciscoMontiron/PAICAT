<?php

namespace Database\Seeders\Sysacad;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SysacadDataSeeder extends Seeder
{
    /**
     * Ejecutar los seeders de la base de datos.
     */
    public function run(): void
    {
        $excelFile = database_path('data/Datos Sysacad.xlsx');

        // Verificar si ya existen datos para evitar re-importar
        if (DB::connection('sysacad')->table('sysacad_paises')->exists()) {
            $this->command->info("Los datos de Sysacad ya existen. Saltando importación.");
            return;
        }

        if (!file_exists($excelFile)) {
            $this->command->error(" Archivo Excel no encontrado: {$excelFile}");
            return;
        }

        $this->command->info(" Cargando archivo Excel...");
        $spreadsheet = IOFactory::load($excelFile);

        $this->importPaises($spreadsheet);
        $this->importProvincias($spreadsheet);
        $this->importPartidos($spreadsheet);
        $this->importLocalidades($spreadsheet);
        $this->importEscuelas($spreadsheet);

        // 6. Importar catálogos
        $this->importEspecialidades($spreadsheet);
        $this->importTitulosSecundarios($spreadsheet);
        $this->importEstadosCiviles($spreadsheet);
        $this->importNacionalidades($spreadsheet);
        $this->importGeneros($spreadsheet);
        $this->importSexos($spreadsheet);
        $this->importTurnos($spreadsheet);
        $this->importModalidades($spreadsheet);
        $this->importTiposIngreso($spreadsheet);

        $this->command->info(" Todos los datos de Sysacad fueron importados correctamente!");
    }

    protected function importPaises($spreadsheet): void
    {
        $this->command->info(" Importando países...");
        $sheet = $spreadsheet->getSheetByName('paises');
        $rows = $sheet->toArray();
        array_shift($rows); // Remover encabezados

        $data = [];
        foreach ($rows as $row) {
            if (empty($row[0])) continue;

            // Usar el id de la fila (columna A) como id_sysacad
            $idSysacad = (int)$row[1];
            // Si es 0, usar el id de la fila del Excel
            if ($idSysacad === 0) {
                $idSysacad = (int)$row[0];
            }

            $data[] = [
                'id_sysacad' => $idSysacad,
                'nombre' => $row[2],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($data) >= 100) {
                DB::connection('sysacad')->table('sysacad_paises')->insert($data);
                $data = [];
            }
        }

        if (!empty($data)) {
            DB::connection('sysacad')->table('sysacad_paises')->insert($data);
        }

        $this->command->info(" Países importados: " . DB::connection('sysacad')->table('sysacad_paises')->count());
    }

    protected function importProvincias($spreadsheet): void
    {
        $this->command->info("  Importando provincias...");
        $sheet = $spreadsheet->getSheetByName('provincias');
        $rows = $sheet->toArray();
        array_shift($rows);

        $data = [];
        foreach ($rows as $row) {
            if (empty($row[0])) continue;

            $paisId = null;
            if (!empty($row[4])) {
                $pais = DB::connection('sysacad')->table('sysacad_paises')
                    ->where('id_sysacad', (int)$row[4])
                    ->first();
                $paisId = $pais->id ?? null;
            }

            $data[] = [
                'nombre' => $row[1],
                'pais_id' => $paisId,
                'id_sysacad' => (int)$row[3],
                'pais_id_sysacad' => !empty($row[4]) ? (int)$row[4] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($data)) {
            DB::connection('sysacad')->table('sysacad_provincias')->insert($data);
        }

        $this->command->info(" Provincias importadas: " . DB::connection('sysacad')->table('sysacad_provincias')->count());
    }

    protected function importPartidos($spreadsheet): void
    {
        $this->command->info("  Importando partidos...");
        $sheet = $spreadsheet->getSheetByName('partidos');
        $rows = $sheet->toArray();
        array_shift($rows);

        $data = [];
        foreach ($rows as $row) {
            if (empty($row[0])) continue;

            $provinciaId = null;
            if (!empty($row[3])) {
                $provincia = DB::connection('sysacad')->table('sysacad_provincias')
                    ->where('id_sysacad', (int)$row[3])
                    ->first();
                $provinciaId = $provincia->id ?? null;
            }

            $data[] = [
                'nombre' => $row[1],
                'provincia_id' => $provinciaId,
                'provincia_id_sysacad' => !empty($row[3]) ? (int)$row[3] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($data)) {
            DB::connection('sysacad')->table('sysacad_partidos')->insert($data);
        }

        $this->command->info(" Partidos importados: " . DB::connection('sysacad')->table('sysacad_partidos')->count());
    }

    protected function importLocalidades($spreadsheet): void
    {
        $this->command->info("  Importando localidades...");
        $sheet = $spreadsheet->getSheetByName('localidades');
        $rows = $sheet->toArray();
        array_shift($rows);

        $data = [];
        $count = 0;

        foreach ($rows as $row) {
            if (empty($row[0])) continue;

            $provinciaId = null;
            if (!empty($row[3])) {
                $provincia = DB::connection('sysacad')->table('sysacad_provincias')
                    ->where('id_sysacad', (int)$row[3])
                    ->first();
                $provinciaId = $provincia->id ?? null;
            }

            $partidoId = null;
            if (!empty($row[4])) {
                $partido = DB::connection('sysacad')->table('sysacad_partidos')
                    ->where('id', (int)$row[4])
                    ->first();
                $partidoId = $partido->id ?? null;
            }

            $data[] = [
                'nombre' => $row[1],
                'provincia_id' => $provinciaId,
                'partido_id' => $partidoId,
                'provincia_id_sysacad' => !empty($row[3]) ? (int)$row[3] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;

            // Insert en chunks de 1000
            if (count($data) >= 1000) {
                DB::connection('sysacad')->table('sysacad_localidades')->insert($data);
                $this->command->info("  → Procesadas {$count} localidades...");
                $data = [];
            }
        }

        if (!empty($data)) {
            DB::connection('sysacad')->table('sysacad_localidades')->insert($data);
        }

        $this->command->info(" Localidades importadas: " . DB::connection('sysacad')->table('sysacad_localidades')->count());
    }

    protected function importEscuelas($spreadsheet): void
    {
        $this->command->info("  Importando escuelas...");
        $sheet = $spreadsheet->getSheetByName('escuelas');
        $rows = $sheet->toArray();
        array_shift($rows);

        $data = [];
        $count = 0;

        foreach ($rows as $row) {
            if (empty($row[0])) continue;

            $localidadId = null;
            if (!empty($row[6])) {
                $localidad = DB::connection('sysacad')->table('sysacad_localidades')
                    ->where('id', (int)$row[6])
                    ->first();
                $localidadId = $localidad->id ?? null;
            }

            // Normalizar gestión
            $gestion = strtolower(trim($row[1] ?? 'estatal'));
            if (str_contains($gestion, 'priv')) {
                $gestion = 'Privado';
            } else {
                $gestion = 'Estatal';
            }

            // Normalizar ámbito
            $ambito = strtolower(trim($row[2] ?? 'urbano'));
            if (str_contains($ambito, 'rur')) {
                $ambito = 'Rural';
            } else {
                $ambito = 'Urbano';
            }

            // Normalizar técnica
            $tecnica = strtoupper(trim($row[3] ?? 'NO'));
            $tecnica = ($tecnica === '1' || $tecnica === 'SI' || $tecnica === 'S') ? 'SI' : 'NO';

            $data[] = [
                'cue' => $row[0],
                'gestion' => $gestion,
                'ambito' => $ambito,
                'tecnica' => $tecnica,
                'nombre' => $row[4] ?? 'Sin nombre',
                'domicilio_localidad' => $row[5] ?? null,
                'localidad_id' => $localidadId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;

            // Insert en chunks de 1000
            if (count($data) >= 1000) {
                DB::connection('sysacad')->table('sysacad_escuelas')->insert($data);
                $this->command->info("  → Procesadas {$count} escuelas...");
                $data = [];
            }
        }

        if (!empty($data)) {
            DB::connection('sysacad')->table('sysacad_escuelas')->insert($data);
        }

        $this->command->info(" Escuelas importadas: " . DB::connection('sysacad')->table('sysacad_escuelas')->count());
    }

    protected function importEspecialidades($spreadsheet): void
    {
        $this->command->info(" Importando especialidades...");
        $this->importCatalogo($spreadsheet, 'especialidades', 'sysacad_especialidades');
    }

    protected function importTitulosSecundarios($spreadsheet): void
    {
        $this->command->info(" Importando títulos secundarios...");
        $this->importCatalogo($spreadsheet, 'titulos sec', 'sysacad_titulos_secundarios');
    }

    protected function importEstadosCiviles($spreadsheet): void
    {
        $this->command->info(" Importando estados civiles...");
        $this->importCatalogo($spreadsheet, 'est civil', 'sysacad_estados_civiles');
    }

    protected function importNacionalidades($spreadsheet): void
    {
        $this->command->info(" Importando nacionalidades...");
        $this->importCatalogo($spreadsheet, 'nacionalidades', 'sysacad_nacionalidades');
    }

    protected function importGeneros($spreadsheet): void
    {
        $this->command->info(" Importando géneros...");
        $this->importCatalogo($spreadsheet, 'generos', 'sysacad_generos');
    }

    protected function importSexos($spreadsheet): void
    {
        $this->command->info(" Importando sexos...");
        $this->importCatalogo($spreadsheet, 'sexos', 'sysacad_sexos');
    }

    protected function importTurnos($spreadsheet): void
    {
        $this->command->info(" Importando turnos...");
        $this->importCatalogo($spreadsheet, 'turnos', 'sysacad_turnos');
    }

    protected function importModalidades($spreadsheet): void
    {
        $this->command->info(" Importando modalidades...");
        $this->importCatalogo($spreadsheet, 'modalidad', 'sysacad_modalidades');
    }

    protected function importTiposIngreso($spreadsheet): void
    {
        $this->command->info(" Importando tipos de ingreso...");
        $this->importCatalogo($spreadsheet, 'tipo ingreso', 'sysacad_tipos_ingreso');
    }

    protected function importCatalogo($spreadsheet, string $sheetName, string $tableName): void
    {
        $sheet = $spreadsheet->getSheetByName($sheetName);
        $rows = $sheet->toArray();
        array_shift($rows); // Remover encabezados

        $data = [];
        foreach ($rows as $row) {
            if (empty($row[0])) continue;

            $data[] = [
                'id_sysacad' => (int)$row[0],
                'nombre' => $row[1],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($data)) {
            DB::connection('sysacad')->table($tableName)->insert($data);
        }

        $this->command->info("✅ {$tableName} importados: " . DB::connection('sysacad')->table($tableName)->count());
    }
}
