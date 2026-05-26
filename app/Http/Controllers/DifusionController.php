<?php

namespace App\Http\Controllers;

use App\Models\Difusion;
use App\Models\Comision;
use App\Models\Inscripcion;
use App\Models\InscripcionComision;
use App\Models\User;
use App\Models\AlumnosUtn\Person;
use App\Mail\DifusionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DifusionController extends Controller
{
    public function index()
    {
        return view('difusiones.index');
    }

    public function create(Request $request)
    {
        // Validar el request
        $validated = $request->validate([
            'asunto'                 => 'required|string|max:255',
            'mensaje'                => 'required|string',
            'modalidad'              => 'nullable|string|in:Presencial,Semipresencial,Virtual',
            'asistencia_operador'    => 'nullable|string|in:>=,>,<=,<,=',
            'asistencia_porcentaje'  => 'nullable|numeric|min:0|max:100',
            'asistencia_desde'       => 'nullable|numeric|min:0|max:100',
            'asistencia_hasta'       => 'nullable|numeric|min:0|max:100',
        ]);

        // Detectar si el filtro de asistencia está activo
        $filtroAsistencia = false;
        $operador         = $validated['asistencia_operador'] ?? '>=';
        $porcentaje       = isset($validated['asistencia_porcentaje']) && $validated['asistencia_porcentaje'] !== ''
                                ? (float) $validated['asistencia_porcentaje']
                                : null;
        $desde            = isset($validated['asistencia_desde']) && $validated['asistencia_desde'] !== ''
                                ? (float) $validated['asistencia_desde']
                                : null;
        $hasta            = isset($validated['asistencia_hasta']) && $validated['asistencia_hasta'] !== ''
                                ? (float) $validated['asistencia_hasta']
                                : null;
        $esRango          = ($desde !== null && $hasta !== null);

        if ($esRango || $porcentaje !== null) {
            $filtroAsistencia = true;
        }

        try {
            // Crear la difusión
            $difusion              = new Difusion();
            $difusion->asunto      = $validated['asunto'];
            $difusion->mensaje     = $validated['mensaje'];
            $difusion->modalidad   = $validated['modalidad'] ?? null;
            $difusion->usuario_id  = auth()->id();

            if ($filtroAsistencia) {
                // ── MODO CON FILTRO DE ASISTENCIA ─────────────────────────────────────────
                // Buscar inscripciones_comision de comisiones ACTIVAS,
                // opcionalmente filtradas por la modalidad de la Inscripcion padre.
                $inscripcionComisionQuery = InscripcionComision::query()
                    ->whereIn('estado', ['inscripto', 'confirmado', 'aprobado'])
                    ->whereHas('comision', fn ($q) => $q->where('estado', 'activa'))
                    ->with(['asistencias', 'inscripcion']);

                if (!empty($validated['modalidad'])) {
                    $inscripcionComisionQuery->whereHas(
                        'inscripcion',
                        fn ($q) => $q->where('modalidad', $validated['modalidad'])
                    );
                }

                $registros = $inscripcionComisionQuery->get();

                // Agrupar por person_id y quedarnos solo con los que cumplen
                // la condición en AL MENOS UNA comisión activa.
                $personIdsCumplen = collect();

                // Mapear inscripcion_comision -> person_id
                $registrosPorPersona = $registros->groupBy(function ($ic) {
                    return $ic->inscripcion?->person_id;
                })->filter(fn ($grupo, $personId) => !empty($personId));

                foreach ($registrosPorPersona as $personId => $grupo) {
                    $cumple = false;
                    foreach ($grupo as $ic) {
                        $pct = $ic->calcularPorcentajeAsistencia();
                        if ($esRango) {
                            if ($pct >= $desde && $pct <= $hasta) {
                                $cumple = true;
                                break;
                            }
                        } else {
                            $cumpleCondicion = match ($operador) {
                                '>='    => $pct >= $porcentaje,
                                '>'     => $pct >  $porcentaje,
                                '<='    => $pct <= $porcentaje,
                                '<'     => $pct <  $porcentaje,
                                '='     => $pct == $porcentaje,
                                default => false,
                            };
                            if ($cumpleCondicion) {
                                $cumple = true;
                                break;
                            }
                        }
                    }
                    if ($cumple) {
                        $personIdsCumplen->push($personId);
                    }
                }

                $personIds = $personIdsCumplen->unique()->filter()->values()->toArray();
            } else {
                // ── MODO SIN FILTRO DE ASISTENCIA ─────────────────────────────────────────
                // Comportamiento original: todos los inscriptos, opcionalmente por modalidad.
                $inscripcionesQuery = Inscripcion::query();
                if (!empty($validated['modalidad'])) {
                    $inscripcionesQuery->where('modalidad', $validated['modalidad']);
                }
                $personIds = $inscripcionesQuery->pluck('person_id')->unique()->filter()->toArray();
            }

            // Consultar la tabla persons en la conexión externa 'alumnos_utn' y obtener emails
            $emails = Person::on('alumnos_utn')
                ->whereIn('id', $personIds)
                ->pluck('email')
                ->filter()
                ->unique()
                ->values();

            $difusion->enviado_a = $emails->count();
            $difusion->save();

            // Enviar mails en cola
            foreach ($emails as $email) {
                Mail::to($email)->queue(new DifusionMail($difusion));
            }

            return redirect()->route('difusiones.index')
                ->with('success', "Difusión generada exitosamente! Se enviaron {$emails->count()} correos.");
        } catch (\Exception $e) {
            return redirect()->route('difusiones.index')
                ->with('error', 'Error al generar la difusión: ' . $e->getMessage());
        }
    }

    public function createPorComision(Request $request, Comision $comision)
    {
        // 1. Validar permisos del docente asignado si no es admin/coordinador
        $user = auth()->user();
        if ($user->hasRole('docente') && $comision->docente_id !== $user->id && !$comision->docentesActivos()->where('user_id', $user->id)->exists()) {
            abort(403, 'No tienes permisos para enviar comunicados a esta comisión.');
        }

        // 2. Validar el request
        $validated = $request->validate([
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string',
        ]);

        try {
            // 3. Obtener correos de los alumnos activos de la comisión
            $emails = $comision->inscripciones()
                ->whereIn('estado', ['inscripto', 'confirmado', 'aprobado'])
                ->with('inscripcion')
                ->get()
                ->map(function ($inscripcionComision) {
                    return $inscripcionComision->inscripcion?->getPerson()?->email;
                })
                ->filter()
                ->unique()
                ->values();

            if ($emails->isEmpty()) {
                return redirect()->back()->with('error', 'No hay alumnos con correos válidos inscriptos en esta comisión.');
            }
            // 4. Crear la difusión
            $difusion = new Difusion();
            $difusion->asunto = $validated['asunto'];
            $difusion->mensaje = $validated['mensaje'];
            $difusion->modalidad = "Comisión: " . $comision->nombre;
            $difusion->usuario_id = $user->id;
            $difusion->enviado_a = $emails->count();
            $difusion->save();

            // 5. Enviar mails en cola
            foreach ($emails as $email) {
                Mail::to($email)->queue(new DifusionMail($difusion));
            }

            return redirect()->back()
                ->with('success', "Comunicado enviado exitosamente! Se encolaron {$emails->count()} correos para esta comisión.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al enviar el comunicado: ' . $e->getMessage());
        }
    }
}
