<?php

namespace App\Exports;

use App\Models\Comision;
use App\Models\Evaluacion;
use App\Models\InscripcionComision;
use App\Models\Nota;
use App\Models\NotaFinalMateria;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PlanillaNotasExport implements WithMultipleSheets
{
    protected Comision $comision;
    protected ?int $materiaId;

    public function __construct(Comision $comision, ?int $materiaId = null)
    {
        $this->comision = $comision;
        $this->materiaId = $materiaId ? (int) $materiaId : null;
    }

    public function sheets(): array
    {
        $comision = $this->comision;
        $comision->load(['materias', 'municipio']);
        $materias = $this->materiaId
            ? $comision->materias->where('id', $this->materiaId)
            : $comision->materias;

        $inscripciones = InscripcionComision::where('comision_id', $comision->id)
            ->whereIn('estado', ['inscripto', 'confirmado', 'regular'])
            ->with(['inscripcion', 'academicoDato'])
            ->get()
            ->sortBy(function ($ic) {
                $p = $ic->inscripcion?->getPerson();
                return $p->apellido ?? $ic->academicoDato->apellido ?? '';
            })
            ->values();

        $sheets = [];

        foreach ($materias as $materia) {
            $evaluaciones = Evaluacion::where('materia_id', $materia->id)
                ->where(function ($q) use ($comision) {
                    $q->where('comision_id', $comision->id)
                        ->orWhereNull('comision_id');
                })
                ->orderBy('fecha', 'asc')
                ->get();

            $evaluacionIds = $evaluaciones->pluck('id');
            $inscripcionIds = $inscripciones->pluck('inscripcion_id');

            $notasRaw = Nota::whereIn('evaluacion_id', $evaluacionIds)
                ->whereIn('inscripcion_id', $inscripcionIds)
                ->get();

            $notasMap = [];
            foreach ($notasRaw as $nota) {
                $notasMap[$nota->inscripcion_id][$nota->evaluacion_id] = $nota;
            }

            $notasFinales = NotaFinalMateria::where('comision_id', $comision->id)
                ->where('materia_id', $materia->id)
                ->whereIn('inscripcion_id', $inscripcionIds)
                ->get()
                ->keyBy('inscripcion_id');

            $promedios = [];
            foreach ($inscripciones as $ic) {
                $notasAlumno = $notasRaw->where('inscripcion_id', $ic->inscripcion_id);
                if ($notasAlumno->isNotEmpty()) {
                    $promedios[$ic->inscripcion_id] = round($notasAlumno->avg('nota'), 2);
                }
            }

            $sheets[] = new PlanillaMateriaSheet(
                $comision,
                $materia,
                $evaluaciones,
                $inscripciones,
                $notasMap,
                $notasFinales,
                $promedios
            );
        }

        if (empty($sheets)) {
            $sheets[] = new PlanillaMateriaSheet($comision, null, collect(), $inscripciones, [], collect(), []);
        }

        // Si hay más de una materia, agregar hoja resumen al inicio
        if (count($sheets) > 1) {
            array_unshift($sheets, new PlanillaResumenSheet($comision, $materias, $inscripciones));
        }

        return $sheets;
    }
}
