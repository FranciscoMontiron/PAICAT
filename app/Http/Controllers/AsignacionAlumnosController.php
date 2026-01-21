<?php

namespace App\Http\Controllers;

use App\Models\Comision;
use App\Models\Inscripcion;
use App\Models\InscripcionComision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignacionAlumnosController extends Controller
{
    /**
     * Mostrar vista principal de asignación aleatoria
     */
    public function index(Request $request)
    {
        // Obtener comisiones activas con cupos disponibles
        $comisiones = Comision::with(['materias', 'docente'])
            ->activas()
            ->conCuposDisponibles()
            ->when($request->anio, fn($q, $anio) => $q->anio($anio))
            ->when($request->periodo, fn($q, $periodo) => $q->where('periodo', $periodo))
            ->orderBy('codigo')
            ->get();

        // Estadísticas generales
        $totalCuposDisponibles = $comisiones->sum('cupos_disponibles');

        // Alumnos sin asignar a ninguna comisión
        $alumnosSinAsignar = $this->obtenerAlumnosSinAsignar()->count();

        return view('asignacion-alumnos.index', [
            'comisiones' => $comisiones,
            'totalCuposDisponibles' => $totalCuposDisponibles,
            'alumnosSinAsignar' => $alumnosSinAsignar,
            'anioActual' => $request->anio ?? date('Y'),
        ]);
    }

    /**
     * Vista previa de la asignación (simulación)
     */
    public function preview(Request $request)
    {
        $request->validate([
            'comisiones' => 'required|array|min:1',
            'comisiones.*' => 'exists:comisiones,id',
        ]);

        $comisionesIds = $request->comisiones;

        // Obtener comisiones seleccionadas
        $comisiones = Comision::whereIn('id', $comisionesIds)
            ->activas()
            ->conCuposDisponibles()
            ->with('materias')
            ->get();

        if ($comisiones->isEmpty()) {
            return back()->with('error', 'Las comisiones seleccionadas no tienen cupos disponibles.');
        }

        // Simular asignación
        $simulacion = $this->simularAsignacion($comisiones);

        return view('asignacion-alumnos.preview', [
            'comisiones' => $comisiones,
            'simulacion' => $simulacion,
            'comisionesIds' => $comisionesIds,
        ]);
    }

    /**
     * Ejecutar la asignación aleatoria
     */
    public function ejecutar(Request $request)
    {
        $request->validate([
            'comisiones' => 'required|array|min:1',
            'comisiones.*' => 'exists:comisiones,id',
            'excluir' => 'nullable|array',
            'excluir.*' => 'exists:inscripciones,id',
        ]);

        $comisionesIds = $request->comisiones;
        $excluirIds = $request->excluir ?? [];

        // Obtener comisiones seleccionadas
        $comisiones = Comision::whereIn('id', $comisionesIds)
            ->activas()
            ->conCuposDisponibles()
            ->with('materias')
            ->get();

        if ($comisiones->isEmpty()) {
            return redirect()->route('asignacion-alumnos.index')
                ->with('error', 'Las comisiones seleccionadas no tienen cupos disponibles.');
        }

        try {
            DB::beginTransaction();

            $resultado = $this->ejecutarAsignacionEquitativa($comisiones, $excluirIds);

            DB::commit();

            return redirect()->route('asignacion-alumnos.index')
                ->with('success', "Se asignaron {$resultado['total']} alumnos a {$resultado['comisiones']} comisiones.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('asignacion-alumnos.index')
                ->with('error', 'Error al ejecutar la asignación: ' . $e->getMessage());
        }
    }

    /**
     * Obtener inscripciones sin asignar a ninguna comisión
     */
    private function obtenerAlumnosSinAsignar(?array $comisionesIds = null)
    {
        return Inscripcion::activas()
            ->whereDoesntHave('inscripcionesComision', function ($q) use ($comisionesIds) {
                if ($comisionesIds) {
                    $q->whereIn('comision_id', $comisionesIds);
                }
            })
            ->get();
    }

    /**
     * Filtrar alumnos elegibles por restricción de especialidad
     */
    private function filtrarPorEspecialidad($inscripciones, Comision $comision)
    {
        // Obtener especialidades de las materias de la comisión
        $especialidades = $comision->materias
            ->pluck('especialidad_id_sysacad')
            ->filter() // Eliminar nulls
            ->unique()
            ->values();

        // Si no hay restricción de especialidad (materias comunes), todos son elegibles
        if ($especialidades->isEmpty()) {
            return $inscripciones;
        }

        // Filtrar por especialidad (OR: coincide con principal O alternativa)
        return $inscripciones->filter(function ($inscripcion) use ($especialidades) {
            return $especialidades->contains($inscripcion->especialidad_id_sysacad) ||
                $especialidades->contains($inscripcion->especialidad_alternativa_id_sysacad);
        });
    }

    /**
     * Simular asignación sin ejecutar cambios
     */
    private function simularAsignacion($comisiones)
    {
        $simulacion = [];
        $asignadosGlobal = collect();

        // Obtener todos los alumnos sin asignar
        $alumnosSinAsignar = $this->obtenerAlumnosSinAsignar($comisiones->pluck('id')->toArray());

        // Preparar pool por comisión
        $poolPorComision = [];
        foreach ($comisiones as $comision) {
            $elegibles = $this->filtrarPorEspecialidad($alumnosSinAsignar, $comision);
            $poolPorComision[$comision->id] = [
                'comision' => $comision,
                'elegibles' => $elegibles->shuffle(),
                'cuposDisponibles' => $comision->cupos_disponibles,
                'asignados' => collect(),
            ];
        }

        // Distribución equitativa: round-robin
        $hayMas = true;
        while ($hayMas) {
            $hayMas = false;
            foreach ($poolPorComision as $id => &$data) {
                if ($data['cuposDisponibles'] <= 0 || $data['asignados']->count() >= $data['cuposDisponibles']) {
                    continue;
                }

                // Buscar un alumno elegible no asignado aún
                foreach ($data['elegibles'] as $key => $inscripcion) {
                    if (!$asignadosGlobal->contains($inscripcion->id)) {
                        $data['asignados']->push($inscripcion);
                        $asignadosGlobal->push($inscripcion->id);
                        $data['elegibles']->forget($key);
                        $hayMas = true;
                        break;
                    }
                }
            }
        }

        // Preparar resultado de simulación
        foreach ($poolPorComision as $data) {
            $simulacion[] = [
                'comision' => $data['comision'],
                'asignados' => $data['asignados'],
                'cuposDisponibles' => $data['cuposDisponibles'],
                'cantidadAsignados' => $data['asignados']->count(),
            ];
        }

        return $simulacion;
    }

    /**
     * Ejecutar asignación equitativa (round-robin)
     */
    private function ejecutarAsignacionEquitativa($comisiones, array $excluirIds = [])
    {
        $totalAsignados = 0;
        $comisionesConAsignados = 0;
        $asignadosGlobal = collect($excluirIds);

        // Obtener todos los alumnos sin asignar
        $alumnosSinAsignar = $this->obtenerAlumnosSinAsignar($comisiones->pluck('id')->toArray())
            ->reject(fn($i) => in_array($i->id, $excluirIds));

        // Preparar pool por comisión
        $poolPorComision = [];
        foreach ($comisiones as $comision) {
            $elegibles = $this->filtrarPorEspecialidad($alumnosSinAsignar, $comision);
            $poolPorComision[$comision->id] = [
                'comision' => $comision,
                'elegibles' => $elegibles->shuffle(),
                'cuposDisponibles' => $comision->cupos_disponibles,
                'asignados' => collect(),
            ];
        }

        // Distribución equitativa: round-robin
        $hayMas = true;
        while ($hayMas) {
            $hayMas = false;
            foreach ($poolPorComision as $id => &$data) {
                if ($data['asignados']->count() >= $data['cuposDisponibles']) {
                    continue;
                }

                // Buscar un alumno elegible no asignado aún
                foreach ($data['elegibles'] as $key => $inscripcion) {
                    if (!$asignadosGlobal->contains($inscripcion->id)) {
                        $data['asignados']->push($inscripcion);
                        $asignadosGlobal->push($inscripcion->id);
                        $data['elegibles']->forget($key);
                        $hayMas = true;
                        break;
                    }
                }
            }
        }

        // Crear inscripciones a comisión
        foreach ($poolPorComision as $data) {
            if ($data['asignados']->isEmpty()) {
                continue;
            }

            $comisionesConAsignados++;

            foreach ($data['asignados'] as $inscripcion) {
                // Obtener academico_dato_id si existe
                $academicoDato = $inscripcion->getAcademicoDato();

                InscripcionComision::create([
                    'inscripcion_id' => $inscripcion->id,
                    'academico_dato_id' => $academicoDato?->id,
                    'comision_id' => $data['comision']->id,
                    'fecha_inscripcion' => now(),
                    'estado' => 'inscripto',
                    'observaciones' => 'Asignación aleatoria automática',
                ]);

                $data['comision']->incrementarCupo();
                $totalAsignados++;
            }
        }

        return [
            'total' => $totalAsignados,
            'comisiones' => $comisionesConAsignados,
        ];
    }
}
