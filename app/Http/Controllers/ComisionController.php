<?php

namespace App\Http\Controllers;

use App\Models\AcademicoDato;
use App\Models\Comision;
use App\Models\Inscripcion;
use App\Models\InscripcionComision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComisionController extends Controller
{
    /**
     * Mostrar lista de comisiones
     */
    public function index(Request $request)
    {
        $query = Comision::with('docente');

        // Filtros
        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }

        if ($request->filled('turno')) {
            $query->where('turno', $request->turno);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        $comisiones = $query->orderBy('anio', 'desc')
                           ->orderBy('nombre')
                           ->paginate(15);

        // Estadísticas
        $stats = [
            'total' => Comision::count(),
            'activas' => Comision::where('estado', 'activa')->count(),
            'cupos_totales' => Comision::sum('cupo_maximo'),
            'cupos_ocupados' => Comision::sum('cupo_actual'),
        ];

        return view('comisiones.index', compact('comisiones', 'stats'));
    }

    /**
     * Mostrar formulario para crear comisión
     */
    public function create()
    {
        $docentes = User::whereHas('roles', function($query) {
            $query->whereIn('slug', ['admin', 'coordinador', 'docente']);
        })->orderBy('name')->get();

        return view('comisiones.create', compact('docentes'));
    }

    /**
     * Guardar nueva comisión
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:20|unique:comisiones,codigo',
            'descripcion' => 'nullable|string',
            'anio' => 'required|integer|min:2020|max:2100',
            'periodo' => 'required|in:Verano,Invierno,Anual',
            'turno' => 'required|in:Mañana,Tarde,Noche',
            'modalidad' => 'required|in:Presencial,Virtual,Semipresencial',
            'cupo_maximo' => 'required|integer|min:1|max:200',
            'docente_id' => 'nullable|exists:users,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'observaciones' => 'nullable|string',
        ]);

        $validated['cupo_actual'] = 0;
        $validated['estado'] = 'activa';

        $comision = Comision::create($validated);

        return redirect()->route('comisiones.show', $comision)
                        ->with('success', 'Comisión creada exitosamente.');
    }

    /**
     * Mostrar detalles de una comisión
     */
    public function show(Comision $comision)
    {
        $comision->load(['docente', 'inscripciones.academicoDato.user', 'evaluaciones']);

        $stats = [
            'inscriptos' => $comision->inscripciones()->count(),
            'cupos_disponibles' => $comision->cupos_disponibles,
            'porcentaje_ocupacion' => $comision->porcentaje_ocupacion,
            'evaluaciones' => $comision->evaluaciones()->count(),
        ];

        return view('comisiones.show', compact('comision', 'stats'));
    }

    /**
     * Mostrar formulario para editar comisión
     */
    public function edit(Comision $comision)
    {
        $docentes = User::whereHas('roles', function($query) {
            $query->whereIn('slug', ['admin', 'coordinador', 'docente']);
        })->orderBy('name')->get();

        return view('comisiones.edit', compact('comision', 'docentes'));
    }

    /**
     * Actualizar comisión
     */
    public function update(Request $request, Comision $comision)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:20|unique:comisiones,codigo,' . $comision->id,
            'descripcion' => 'nullable|string',
            'anio' => 'required|integer|min:2020|max:2100',
            'periodo' => 'required|in:Verano,Invierno,Anual',
            'turno' => 'required|in:Mañana,Tarde,Noche',
            'modalidad' => 'required|in:Presencial,Virtual,Semipresencial',
            'cupo_maximo' => 'required|integer|min:' . $comision->cupo_actual . '|max:200',
            'docente_id' => 'nullable|exists:users,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:activa,cerrada,finalizada,cancelada',
            'observaciones' => 'nullable|string',
        ]);

        $comision->update($validated);

        return redirect()->route('comisiones.show', $comision)
                        ->with('success', 'Comisión actualizada exitosamente.');
    }

    /**
     * Eliminar comisión (soft delete)
     */
    public function destroy(Comision $comision)
    {
        // Verificar que no tenga inscripciones activas
        if ($comision->inscripciones()->whereIn('estado', ['inscripto', 'confirmado'])->exists()) {
            return redirect()->back()
                           ->with('error', 'No se puede eliminar una comisión con inscripciones activas.');
        }

        $comision->delete();

        return redirect()->route('comisiones.index')
                        ->with('success', 'Comisión eliminada exitosamente.');
    }

    /**
     * Cambiar estado de la comisión
     */
    public function cambiarEstado(Request $request, Comision $comision)
    {
        $validated = $request->validate([
            'estado' => 'required|in:activa,cerrada,finalizada,cancelada',
        ]);

        $comision->update(['estado' => $validated['estado']]);

        return redirect()->back()
                        ->with('success', 'Estado de la comisión actualizado.');
    }

    /**
     * Asignar docente a la comisión
     */
    public function asignarDocente(Request $request, Comision $comision)
    {
        $validated = $request->validate([
            'docente_id' => 'required|exists:users,id',
        ]);

        // Verificar que el usuario tenga rol de docente
        $docente = User::findOrFail($validated['docente_id']);
        if (!$docente->hasAnyRole(['admin', 'coordinador', 'docente'])) {
            return redirect()->back()
                           ->with('error', 'El usuario seleccionado no tiene permisos de docente.');
        }

        $comision->update(['docente_id' => $validated['docente_id']]);

        return redirect()->back()
                        ->with('success', 'Docente asignado exitosamente.');
    }

    /**
     * Obtener alumnos disponibles para inscribir (AJAX)
     */
    public function alumnosDisponibles(Request $request, Comision $comision)
    {
        // Obtener IDs de inscripciones ya asignadas a esta comisión
        $inscripcionesYaAsignadas = $comision->inscripciones()
            ->whereNotNull('inscripcion_id')
            ->pluck('inscripcion_id')
            ->toArray();

        // Buscar inscripciones activas que no estén ya asignadas a esta comisión
        // Se incluyen: pendiente, documentacion_ok, confirmado (excluye cancelado y baja)
        $query = Inscripcion::whereNotIn('estado', [
                Inscripcion::ESTADO_CANCELADO,
                Inscripcion::ESTADO_BAJA
            ])
            ->whereNotIn('id', $inscripcionesYaAsignadas);

        // Filtrar por búsqueda si se proporciona
        if ($request->filled('search')) {
            $search = $request->search;

            // Buscar en alumnos_utn.persons
            $personIds = \App\Models\AlumnosUtn\Person::on('alumnos_utn')
                ->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%")
                      ->orWhere('documento', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->pluck('id')
                ->toArray();

            $query->whereIn('person_id', $personIds);
        }

        $inscripciones = $query->limit(50)->get();

        $alumnos = $inscripciones->map(function($inscripcion) {
            $person = $inscripcion->getPerson();

            if (!$person) {
                return null;
            }

            return [
                'id' => $inscripcion->id, // Ahora devolvemos el ID de la inscripción
                'nombre' => $person->nombre . ' ' . $person->apellido,
                'email' => $person->email ?? 'Sin email',
                'dni' => $person->documento ?? 'N/A',
                'especialidad' => $inscripcion->especialidad_nombre ?? 'Sin especialidad',
            ];
        })->filter(); // Eliminar nulls

        return response()->json($alumnos->values());
    }

    /**
     * Inscribir alumno a la comisión
     */
    public function inscribirAlumno(Request $request, Comision $comision)
    {
        $validated = $request->validate([
            'inscripcion_id' => 'required|exists:inscripciones,id',
        ]);

        // Obtener la inscripción
        $inscripcion = Inscripcion::findOrFail($validated['inscripcion_id']);

        // Verificar que la comisión tenga cupo disponible
        if ($comision->cupos_disponibles <= 0) {
            return redirect()->back()
                           ->with('error', 'La comisión no tiene cupos disponibles.');
        }

        // Verificar que el alumno no esté ya inscripto
        $yaInscripto = $comision->inscripciones()
            ->where('inscripcion_id', $inscripcion->id)
            ->exists();

        if ($yaInscripto) {
            return redirect()->back()
                           ->with('error', 'El alumno ya está inscripto en esta comisión.');
        }

        // Crear la inscripción a la comisión
        InscripcionComision::create([
            'inscripcion_id' => $inscripcion->id,
            'academico_dato_id' => $inscripcion->academico_dato_id, // Puede ser null
            'comision_id' => $comision->id,
            'fecha_inscripcion' => now(),
            'estado' => 'inscripto',
        ]);

        // Actualizar cupo actual
        $comision->increment('cupo_actual');

        return redirect()->back()
                        ->with('success', 'Alumno inscripto exitosamente.');
    }

    /**
     * Desinscribir alumno de la comisión
     */
    public function desinscribirAlumno(Request $request, Comision $comision, InscripcionComision $inscripcion)
    {
        // Verificar que la inscripción pertenezca a esta comisión
        if ($inscripcion->comision_id !== $comision->id) {
            return redirect()->back()
                           ->with('error', 'La inscripción no pertenece a esta comisión.');
        }

        // Eliminar la inscripción (soft delete)
        $inscripcion->delete();

        // Actualizar cupo actual
        $comision->decrement('cupo_actual');

        return redirect()->back()
                        ->with('success', 'Alumno desinscripto exitosamente.');
    }
}
