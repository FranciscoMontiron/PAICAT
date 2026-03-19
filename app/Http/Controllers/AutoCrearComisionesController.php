<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Comision;
use App\Models\Inscripcion;
use App\Models\Materia;
use App\Models\Municipio;
use App\Services\DataNormalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutoCrearComisionesController extends Controller
{
    /**
     * Formulario para configurar la auto-creación
     */
    public function index()
    {
        $anios = range(date('Y') - 1, date('Y') + 1);
        $municipios = Municipio::activos()->with(['aulas' => fn($q) => $q->activas()])->orderBy('nombre')->get();
        $materias = Materia::activas()->orderBy('codigo')->get();

        return view('comisiones.auto-crear', compact('anios', 'municipios', 'materias'));
    }

    /**
     * Generar preview de comisiones a crear
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'anio' => 'required|integer|min:2020|max:2100',
            'materias' => 'required|array|min:1',
            'materias.*' => 'exists:materias,id',
            'municipios' => 'required|array|min:1',
            'municipios.*' => 'exists:municipios,id',
        ]);

        $anio = $validated['anio'];
        $materiasIds = $validated['materias'];
        $municipiosIds = $validated['municipios'];

        // Inscripciones sin comisión para este año
        $sinAsignar = $this->obtenerInscripcionesSinComision($anio);

        if ($sinAsignar->isEmpty()) {
            return back()->with('error', 'No hay inscripciones sin comisión para el año ' . $anio . '.');
        }

        // Agrupar por (modalidad, tipo_ingreso, turno)
        $grupos = $sinAsignar->groupBy(function ($i) {
            return strtolower($i->modalidad) . '|' . strtolower($i->tipo_ingreso) . '|' . strtolower($i->turno_ingreso ?? 'sin_turno');
        });

        // Aulas disponibles por municipio
        $aulasPorMunicipio = Aula::activas()
            ->whereIn('municipio_id', $municipiosIds)
            ->whereDoesntHave('comisiones', fn($q) => $q->where('anio', $anio)->where('estado', 'activa'))
            ->with('municipio')
            ->orderByDesc('capacidad')
            ->get()
            ->groupBy('municipio_id');

        // Generar propuestas de comisiones
        $propuestas = $this->generarPropuestas($grupos, $aulasPorMunicipio, $anio, $municipiosIds);

        $municipios = Municipio::whereIn('id', $municipiosIds)->pluck('nombre', 'id');
        $materias = Materia::whereIn('id', $materiasIds)->get();

        return view('comisiones.auto-crear-preview', [
            'propuestas' => $propuestas,
            'anio' => $anio,
            'materiasIds' => $materiasIds,
            'materias' => $materias,
            'municipios' => $municipios,
            'sinAsignar' => $sinAsignar,
            'resumenGrupos' => $grupos->map->count(),
        ]);
    }

    /**
     * Ejecutar la creación de comisiones seleccionadas
     */
    public function ejecutar(Request $request)
    {
        $validated = $request->validate([
            'anio' => 'required|integer',
            'materias' => 'required|array|min:1',
            'materias.*' => 'exists:materias,id',
            'comisiones' => 'required|array|min:1',
            'comisiones.*.nombre' => 'required|string|max:100',
            'comisiones.*.codigo' => 'required|string|max:20',
            'comisiones.*.modalidad' => 'required|string',
            'comisiones.*.periodo' => 'required|string',
            'comisiones.*.turno' => 'nullable|string',
            'comisiones.*.municipio_id' => 'nullable|integer',
            'comisiones.*.aula_id' => 'nullable|integer',
            'comisiones.*.cupo_maximo' => 'nullable|integer|min:1',
            'comisiones.*.seleccionada' => 'nullable',
        ]);

        $materiasIds = $validated['materias'];
        $anio = $validated['anio'];
        $normalizador = new DataNormalizationService();

        $creadas = 0;

        DB::transaction(function () use ($validated, $materiasIds, $anio, $normalizador, &$creadas) {
            foreach ($validated['comisiones'] as $datos) {
                // Solo crear las que están marcadas
                if (empty($datos['seleccionada'])) {
                    continue;
                }

                $modalidad = $normalizador->normalizarModalidad($datos['modalidad']);
                $esVirtual = strtolower($modalidad) === 'virtual';

                $comision = Comision::create([
                    'nombre' => $datos['nombre'],
                    'codigo' => $datos['codigo'],
                    'anio' => $anio,
                    'modalidad' => $modalidad,
                    'periodo' => $normalizador->normalizarTipoIngreso($datos['periodo']),
                    'turno' => $esVirtual ? null : $normalizador->normalizarTurno($datos['turno'] ?? null),
                    'municipio_id' => $esVirtual ? null : ($datos['municipio_id'] ?? null),
                    'aula_id' => $esVirtual ? null : ($datos['aula_id'] ?? null),
                    'cupo_maximo' => $esVirtual ? null : ($datos['cupo_maximo'] ?? 80),
                    'cupo_actual' => 0,
                    'estado' => 'activa',
                ]);

                $comision->materias()->sync($materiasIds);
                $creadas++;
            }
        });

        return redirect()->route('comisiones.index')
            ->with('success', "Se crearon {$creadas} comisiones automáticamente.");
    }

    /**
     * Obtener inscripciones activas sin comisión asignada para un año
     */
    private function obtenerInscripcionesSinComision(int $anio)
    {
        return Inscripcion::activas()
            ->where('anio_ingreso', $anio)
            ->whereDoesntHave('inscripcionesComision', function ($q) {
                $q->whereIn('estado', ['inscripto', 'confirmado', 'aprobado']);
            })
            ->get();
    }

    /**
     * Generar propuestas de comisiones basándose en demanda y aulas disponibles
     */
    private function generarPropuestas($grupos, $aulasPorMunicipio, int $anio, array $municipiosIds): array
    {
        $propuestas = [];
        $aulasUsadas = collect();
        $codigosUsados = Comision::where('anio', $anio)->pluck('codigo')->toArray();
        $contadorPorTipo = [];

        foreach ($grupos as $clave => $inscripciones) {
            [$modalidad, $periodo, $turno] = explode('|', $clave);
            $cantidad = $inscripciones->count();

            if ($modalidad === 'virtual') {
                // Virtual: una sola comisión sin límite
                $codigo = $this->generarCodigo($anio, $modalidad, $periodo, $turno, null, $codigosUsados, $contadorPorTipo);

                $propuestas[] = [
                    'nombre' => $this->generarNombre($modalidad, $periodo, $turno, null),
                    'codigo' => $codigo,
                    'modalidad' => ucfirst($modalidad),
                    'periodo' => ucfirst($periodo),
                    'turno' => null,
                    'municipio_id' => null,
                    'municipio_nombre' => null,
                    'aula_id' => null,
                    'aula_nombre' => null,
                    'cupo_maximo' => null,
                    'demanda' => $cantidad,
                    'seleccionada' => true,
                ];
                continue;
            }

            // Presencial / Semipresencial: crear comisiones por aula
            $restante = $cantidad;

            foreach ($municipiosIds as $munId) {
                $aulasDisponibles = ($aulasPorMunicipio[$munId] ?? collect())
                    ->reject(fn($a) => $aulasUsadas->contains($a->id));

                foreach ($aulasDisponibles as $aula) {
                    if ($restante <= 0) {
                        break 2;
                    }

                    $cupo = $aula->capacidad ?? 40;
                    $aulasUsadas->push($aula->id);

                    $codigo = $this->generarCodigo($anio, $modalidad, $periodo, $turno, $aula->municipio, $codigosUsados, $contadorPorTipo);

                    $propuestas[] = [
                        'nombre' => $this->generarNombre($modalidad, $periodo, $turno, $aula->municipio),
                        'codigo' => $codigo,
                        'modalidad' => ucfirst($modalidad),
                        'periodo' => ucfirst($periodo),
                        'turno' => $turno === 'sin_turno' ? null : ucfirst($turno),
                        'municipio_id' => $munId,
                        'municipio_nombre' => $aula->municipio->nombre ?? '',
                        'aula_id' => $aula->id,
                        'aula_nombre' => $aula->nombre,
                        'cupo_maximo' => $cupo,
                        'demanda' => min($restante, $cupo),
                        'seleccionada' => true,
                    ];

                    $restante -= $cupo;
                }
            }

            // Si todavía quedan alumnos sin cubrir, crear comisiones sin aula asignada
            while ($restante > 0) {
                $cupo = 40; // Cupo por defecto sin aula
                $codigo = $this->generarCodigo($anio, $modalidad, $periodo, $turno, null, $codigosUsados, $contadorPorTipo);

                $propuestas[] = [
                    'nombre' => $this->generarNombre($modalidad, $periodo, $turno, null),
                    'codigo' => $codigo,
                    'modalidad' => ucfirst($modalidad),
                    'periodo' => ucfirst($periodo),
                    'turno' => $turno === 'sin_turno' ? null : ucfirst($turno),
                    'municipio_id' => $municipiosIds[0] ?? null,
                    'municipio_nombre' => 'Sin aula asignada',
                    'aula_id' => null,
                    'aula_nombre' => null,
                    'cupo_maximo' => $cupo,
                    'demanda' => min($restante, $cupo),
                    'seleccionada' => true,
                ];

                $restante -= $cupo;
            }
        }

        return $propuestas;
    }

    /**
     * Generar código único para comisión
     * Formato: {AÑO}-{MOD}{PER}-{TURNO}-{NUM}
     */
    private function generarCodigo(int $anio, string $modalidad, string $periodo, string $turno, $municipio, array &$codigosUsados, array &$contadorPorTipo): string
    {
        $mod = match (strtolower($modalidad)) {
            'presencial' => 'PRE',
            'virtual' => 'VIR',
            'semipresencial' => 'SEMI',
            default => 'X',
        };

        $per = match (strtolower($periodo)) {
            'intensivo' => 'I',
            'extensivo' => 'E',
            default => 'X',
        };

        $tur = match (strtolower($turno)) {
            'mañana', 'manana' => 'M',
            'tardenoche' => 'TN',
            default => '',
        };

        $base = "{$anio}-{$mod}{$per}";
        if ($tur) {
            $base .= "-{$tur}";
        }

        // Contador secuencial
        $contadorPorTipo[$base] = ($contadorPorTipo[$base] ?? 0) + 1;
        $num = str_pad($contadorPorTipo[$base], 2, '0', STR_PAD_LEFT);
        $codigo = "{$base}-{$num}";

        // Asegurar unicidad
        while (in_array($codigo, $codigosUsados)) {
            $contadorPorTipo[$base]++;
            $num = str_pad($contadorPorTipo[$base], 2, '0', STR_PAD_LEFT);
            $codigo = "{$base}-{$num}";
        }

        $codigosUsados[] = $codigo;
        return $codigo;
    }

    /**
     * Generar nombre descriptivo para comisión
     */
    private function generarNombre(string $modalidad, string $periodo, string $turno, $municipio): string
    {
        $partes = [ucfirst($modalidad), ucfirst($periodo)];

        if ($turno && $turno !== 'sin_turno') {
            $partes[] = ucfirst($turno);
        }

        if ($municipio) {
            $partes[] = $municipio->nombre ?? '';
        }

        return implode(' - ', array_filter($partes));
    }
}
