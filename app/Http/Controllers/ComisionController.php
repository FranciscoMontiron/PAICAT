<?php

namespace App\Http\Controllers;

use App\Models\AcademicoDato;
use App\Models\Aula;
use App\Models\Comision;
use App\Models\Cursada;
use App\Models\Inscripcion;
use App\Models\InscripcionComision;
use App\Models\Materia;
use App\Models\Municipio;
use App\Models\User;
use App\Services\ConfiguracionService;
use App\Services\DataNormalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComisionController extends Controller
{
    /**
     * Mostrar lista de comisiones
     */
    public function index(Request $request)
    {
        $verArchivadas = $request->boolean('archivadas');

        $query = Comision::with(['docente', 'materias']);

        // Por defecto ocultar archivadas
        if (!$verArchivadas) {
            $query->where(function ($q) {
                $q->where('archivada', false)->orWhereNull('archivada');
            });
        } else {
            $query->where('archivada', true);
        }

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
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        $comisiones = $query->withCount(['docentesActivos'])
            ->orderByRaw('CASE WHEN docente_id IS NULL AND (SELECT COUNT(*) FROM comision_docente WHERE comision_docente.comision_id = comisiones.id AND comision_docente.activo = 1) = 0 THEN 0 ELSE 1 END')
            ->orderBy('anio', 'desc')
            ->orderBy('nombre')
            ->paginate(15);

        // Estadísticas (excluir virtuales del cálculo de cupos)
        $cuposTotales = Comision::whereNotNull('cupo_maximo')
            ->selectRaw('SUM(cupo_maximo + CASE WHEN extracupos_habilitados = 1 THEN extracupos ELSE 0 END) as total')
            ->value('total') ?? 0;

        $sinDocente = Comision::whereNull('docente_id')
            ->whereDoesntHave('docentesActivos')
            ->count();

        $stats = [
            'total' => Comision::count(),
            'activas' => Comision::where('estado', 'activa')->count(),
            'cupos_totales' => $cuposTotales,
            'cupos_ocupados' => \DB::table('inscripcion_comisiones')
                ->whereNull('deleted_at')
                ->whereIn('estado', ['inscripto', 'confirmado', 'aprobado'])
                ->count(),
            'sin_docente' => $sinDocente,
            'archivadas' => Comision::where('archivada', true)->count(),
        ];

        // Detectar conflictos de aula: misma aula, año, turno y periodo (solo presenciales)
        $conflictosAulaRaw = Comision::whereNotNull('aula_id')
            ->where('estado', '!=', 'cancelada')
            ->whereRaw("LOWER(modalidad) = 'presencial'")
            ->where(function ($q) {
                $q->where('archivada', false)->orWhereNull('archivada');
            })
            ->select('aula_id', 'anio', 'turno', 'periodo')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('GROUP_CONCAT(nombre SEPARATOR ", ") as nombres')
            ->selectRaw('GROUP_CONCAT(id SEPARATOR ",") as ids')
            ->groupBy('aula_id', 'anio', 'turno', 'periodo')
            ->having('total', '>', 1)
            ->get();

        // IDs de comisiones en conflicto para marcar en la tabla
        $idsEnConflicto = collect();
        $conflictosAula = $conflictosAulaRaw->map(function ($conflicto) use (&$idsEnConflicto) {
            $aula = Aula::find($conflicto->aula_id);
            $ids = explode(',', $conflicto->ids);
            $idsEnConflicto = $idsEnConflicto->merge($ids);
            return [
                'aula' => $aula ? $aula->nombre : 'Aula #' . $conflicto->aula_id,
                'anio' => $conflicto->anio,
                'turno' => $conflicto->turno,
                'periodo' => $conflicto->periodo,
                'total' => $conflicto->total,
                'nombres' => $conflicto->nombres,
            ];
        });
        $idsEnConflicto = $idsEnConflicto->map(fn($id) => (int) $id)->unique()->values();

        // Años disponibles desde la BD
        $aniosDisponibles = Comision::select('anio')->distinct()->orderBy('anio', 'desc')->pluck('anio');

        return view('comisiones.index', compact('comisiones', 'stats', 'aniosDisponibles', 'verArchivadas', 'conflictosAula', 'idsEnConflicto'));
    }

    /**
     * Mostrar formulario para crear comisión
     */
    public function create()
    {
        $docentes = User::where('estado', 'activo')
            ->whereHas('roles', function ($query) {
                $query->where('solo_contenido_asignado', true);
            })->with('roles:id,nombre,slug')
            ->orderBy('name')->get();

        $materias = Materia::activas()->orderBy('codigo')->get();

        $turnos = Comision::getTurnos();

        $tiposIngreso = Comision::getTiposIngreso();

        $modalidades = Comision::getModalidades();

        // Municipios activos
        $municipios = Municipio::activos()->orderBy('nombre')->get();

        // Aulas activas con su municipio
        $aulas = Aula::activas()->with('municipio')->orderBy('nombre')->get();

        return view('comisiones.create', compact('docentes', 'materias', 'turnos', 'tiposIngreso', 'modalidades', 'municipios', 'aulas'));
    }

    /**
     * Guardar nueva comisión
     */
    public function store(Request $request)
    {
        // Validación condicional basada en modalidad
        $modalidad = strtolower($request->input('modalidad', ''));
        $esVirtual = $modalidad === 'virtual';

        $rules = [
            'materias' => 'required|array|min:1',
            'materias.*' => 'exists:materias,id',
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:20|unique:comisiones,codigo',
            'descripcion' => 'nullable|string',
            'anio' => 'required|integer|min:2020|max:2100',
            'periodo' => 'required|string|max:50',
            'turno' => $esVirtual ? 'nullable|string|max:50' : 'required|string|max:50',
            'modalidad' => 'required|string|max:50',
            'cupo_maximo' => $esVirtual ? 'nullable|integer|min:0' : 'required|integer|min:1|max:500',
            'municipio_id' => $esVirtual ? 'nullable|exists:municipios,id' : 'required|exists:municipios,id',
            'aula_id' => 'nullable|exists:aulas,id',
            'docentes' => 'nullable|array',
            'docentes.*' => 'exists:users,id',
            'observaciones' => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        $materiasIds = $validated['materias'];
        $docentesIds = $validated['docentes'] ?? [];
        unset($validated['materias'], $validated['docentes']);

        // Normalizar datos antes de crear
        $normalizador = new DataNormalizationService();
        $validated['modalidad'] = $normalizador->normalizarModalidad($validated['modalidad'] ?? null);
        $validated['turno'] = $normalizador->normalizarTurno($validated['turno'] ?? null);
        $validated['periodo'] = $normalizador->normalizarTipoIngreso($validated['periodo'] ?? null);

        $validated['cupo_actual'] = 0;
        $validated['estado'] = 'activa';

        // Lógica especial para comisiones virtuales: sin cupo ni turno
        if (strtolower($validated['modalidad']) === 'virtual') {
            $validated['turno'] = null;
            $validated['cupo_maximo'] = null; // Sin límite de cupo
            $validated['municipio_id'] = null;
            $validated['aula_id'] = null;
            $validated['extracupos_habilitados'] = false;
            $validated['extracupos'] = 0;
        }

        // Si hay docentes, asignar el primero como docente_id (compatibilidad)
        if (!empty($docentesIds)) {
            $validated['docente_id'] = $docentesIds[0];
        }

        $comision = Comision::create($validated);
        $comision->materias()->sync($materiasIds);

        // Agregar docentes a la tabla pivot
        foreach ($docentesIds as $docenteId) {
            \App\Models\ComisionDocente::create([
                'comision_id' => $comision->id,
                'user_id' => $docenteId,
                'activo' => true,
                'fecha_asignacion' => now(),
            ]);
        }

        $mensaje = 'Comisión creada exitosamente.';

        // Agregar mensaje de correcciones si hay
        $mensajeCorrecciones = $normalizador->getMensajeCorrecciones();
        if ($mensajeCorrecciones) {
            $mensaje .= ' ' . str_replace("\n", ' ', $mensajeCorrecciones);
        }

        return redirect()->route('comisiones.show', $comision)
            ->with('success', $mensaje);
    }

    /**
     * Mostrar detalles de una comisión
     */
    public function show(Comision $comision)
    {
        $comision->load([
            'docente',
            'docentesActivos.docente.roles',
            'inscripciones' => function ($query) {
                $query->whereIn('estado', ['inscripto', 'confirmado', 'aprobado'])
                      ->with(['inscripcion', 'academicoDato.user']);
            },
            'evaluaciones'
        ]);

        $stats = [
            'inscriptos' => $comision->inscripciones()->whereIn('estado', ['inscripto', 'confirmado', 'aprobado'])->count(),
            'cupos_disponibles' => $comision->cupos_disponibles,
            'porcentaje_ocupacion' => $comision->porcentaje_ocupacion,
            'evaluaciones' => $comision->evaluaciones()->count(),
        ];

        // Docentes disponibles para asignar
        $docentes = User::where('estado', 'activo')
            ->whereHas('roles', function ($query) {
                $query->where('solo_contenido_asignado', true);
            })->with('roles:id,nombre,slug')
            ->orderBy('name')->get();

        return view('comisiones.show', compact('comision', 'stats', 'docentes'));
    }

    /**
     * Mostrar formulario para editar comisión
     */
    public function edit(Comision $comision)
    {
        $docentes = User::where('estado', 'activo')
            ->whereHas('roles', function ($query) {
                $query->where('solo_contenido_asignado', true);
            })->with('roles:id,nombre,slug')
            ->orderBy('name')->get();

        $materias = Materia::activas()->orderBy('codigo')->get();

        $turnos = Comision::getTurnos();

        $tiposIngreso = Comision::getTiposIngreso();

        $modalidades = Comision::getModalidades();

        // Municipios activos
        $municipios = Municipio::activos()->orderBy('nombre')->get();

        // Aulas activas con su municipio
        $aulas = Aula::activas()->with('municipio')->orderBy('nombre')->get();

        // Cargar asignaciones de docentes
        $comision->load(['docentesActivos.docente', 'historialDocentes.docente']);

        return view('comisiones.edit', compact('comision', 'docentes', 'materias', 'turnos', 'tiposIngreso', 'modalidades', 'municipios', 'aulas'));
    }

    /**
     * Actualizar comisión
     */
    public function update(Request $request, Comision $comision)
    {
        // Validación condicional basada en modalidad
        $modalidad = strtolower($request->input('modalidad', ''));
        $esVirtual = $modalidad === 'virtual';

        $rules = [
            'materias' => 'required|array|min:1',
            'materias.*' => 'exists:materias,id',
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:20|unique:comisiones,codigo,' . $comision->id,
            'descripcion' => 'nullable|string',
            'anio' => 'required|integer|min:2020|max:2100',
            'periodo' => 'required|string|max:50',
            'turno' => $esVirtual ? 'nullable|string|max:50' : 'required|string|max:50',
            'modalidad' => 'required|string|max:50',
            'cupo_maximo' => $esVirtual ? 'nullable|integer|min:0' : 'required|integer|min:' . $comision->cupo_real . '|max:500',
            'estado' => 'required|in:' . implode(',', array_keys(Comision::getEstados())),
            'municipio_id' => $esVirtual ? 'nullable|exists:municipios,id' : 'required|exists:municipios,id',
            'aula_id' => 'nullable|exists:aulas,id',
            'docentes' => 'nullable|array',
            'docentes.*' => 'exists:users,id',
            'observaciones' => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        $materiasIds = $validated['materias'];
        $docentesIds = $validated['docentes'] ?? [];
        unset($validated['materias'], $validated['docentes']);

        // Normalizar datos antes de actualizar
        $normalizador = new DataNormalizationService();
        $validated['modalidad'] = $normalizador->normalizarModalidad($validated['modalidad'] ?? null);
        $validated['turno'] = $normalizador->normalizarTurno($validated['turno'] ?? null);
        $validated['periodo'] = $normalizador->normalizarTipoIngreso($validated['periodo'] ?? null);

        // Lógica especial para comisiones virtuales
        if (strtolower($validated['modalidad']) === 'virtual') {
            $validated['turno'] = null;
            $validated['cupo_maximo'] = null; // Sin límite de cupo
            $validated['municipio_id'] = null;
            $validated['aula_id'] = null;
            $validated['extracupos_habilitados'] = false;
            $validated['extracupos'] = 0;
        }

        // Si hay docentes, actualizar el docente_id (compatibilidad)
        if (!empty($docentesIds)) {
            $validated['docente_id'] = $docentesIds[0];
        } else {
            $validated['docente_id'] = null;
        }

        $comision->update($validated);
        $comision->materias()->sync($materiasIds);

        // Sincronizar docentes en la tabla pivot
        // Desactivar todos los docentes actuales
        \App\Models\ComisionDocente::where('comision_id', $comision->id)
            ->where('activo', true)
            ->update([
                'activo' => false,
                'fecha_baja' => now(),
            ]);

        // Agregar/reactivar docentes nuevos
        foreach ($docentesIds as $docenteId) {
            $asignacion = \App\Models\ComisionDocente::where('comision_id', $comision->id)
                ->where('user_id', $docenteId)
                ->first();

            if ($asignacion) {
                // Reactivar si existe
                $asignacion->update([
                    'activo' => true,
                    'fecha_baja' => null,
                ]);
            } else {
                // Crear nueva asignación
                \App\Models\ComisionDocente::create([
                    'comision_id' => $comision->id,
                    'user_id' => $docenteId,
                    'activo' => true,
                    'fecha_asignacion' => now(),
                ]);
            }
        }

        $mensaje = 'Comisión actualizada exitosamente.';

        // Agregar mensaje de correcciones si hay
        $mensajeCorrecciones = $normalizador->getMensajeCorrecciones();
        if ($mensajeCorrecciones) {
            $mensaje .= ' ' . str_replace("\n", ' ', $mensajeCorrecciones);
        }

        return redirect()->route('comisiones.show', $comision)
            ->with('success', $mensaje);
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
            'estado' => 'required|in:' . implode(',', array_keys(Comision::getEstados())),
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

        $alumnos = $inscripciones->map(function ($inscripcion) {
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

        // Verificar que la comisión tenga cupo disponible (null = sin límite para virtuales)
        if (!$comision->tieneCuposDisponibles()) {
            return redirect()->back()
                ->with('error', 'La comisión no tiene cupos disponibles.');
        }

        // Validar que el tipo de ingreso coincida (Intensivo/Extensivo)
        if ($inscripcion->tipo_ingreso && $comision->periodo && $inscripcion->tipo_ingreso !== $comision->periodo) {
            return redirect()->back()
                ->with('error', "El alumno tiene tipo de ingreso \"{$inscripcion->tipo_ingreso}\" pero la comisión es \"{$comision->periodo}\". No se puede asignar.");
        }

        // Validar que la modalidad coincida
        if ($inscripcion->modalidad !== $comision->modalidad) {
            return redirect()->back()
                ->with('error', "El alumno tiene modalidad \"{$inscripcion->modalidad}\" pero la comisión es \"{$comision->modalidad}\". No se puede asignar.");
        }

        // Verificar que el alumno no esté ya inscripto en CUALQUIER comisión activa
        $yaInscriptoOtra = InscripcionComision::where('inscripcion_id', $inscripcion->id)
            ->whereIn('estado', ['inscripto', 'confirmado', 'aprobado'])
            ->first();

        if ($yaInscriptoOtra) {
            $comisionExistente = $yaInscriptoOtra->comision;
            $nombreComision = $comisionExistente ? $comisionExistente->nombre : 'otra comisión';
            return redirect()->back()
                ->with('error', "El alumno ya está inscripto en {$nombreComision}.");
        }

        // Buscar si existe una inscripción_comision cancelada para reactivarla
        $inscripcionComisionExistente = InscripcionComision::where('inscripcion_id', $inscripcion->id)
            ->where('comision_id', $comision->id)
            ->whereIn('estado', ['cancelado', 'trasladado'])
            ->first();

        if ($inscripcionComisionExistente) {
            // Reactivar la inscripción existente
            $inscripcionComisionExistente->update([
                'estado' => 'inscripto',
                'fecha_inscripcion' => now(),
                'observaciones' => 'Reasignación manual - Reactivada',
            ]);
        } else {
            // Crear la inscripción a la comisión
            InscripcionComision::create([
                'inscripcion_id' => $inscripcion->id,
                'academico_dato_id' => $inscripcion->academico_dato_id,
                'comision_id' => $comision->id,
                'fecha_inscripcion' => now(),
                'estado' => 'inscripto',
            ]);
        }

        // Crear o reactivar registro de cursada
        $anioActual = date('Y');
        $esRecursante = Cursada::where('inscripcion_id', $inscripcion->id)
            ->where('anio', '<', $anioActual)
            ->whereIn('estado', [Cursada::ESTADO_DESAPROBADO, Cursada::ESTADO_LIBRE])
            ->exists();

        // Buscar cursada existente (puede estar dada de baja)
        $cursadaExistente = Cursada::where('inscripcion_id', $inscripcion->id)
            ->where('comision_id', $comision->id)
            ->where('anio', $anioActual)
            ->first();

        if ($cursadaExistente) {
            // Reactivar cursada si estaba dada de baja
            if ($cursadaExistente->estado === Cursada::ESTADO_BAJA) {
                $cursadaExistente->update([
                    'estado' => Cursada::ESTADO_CURSANDO,
                    'fecha_inicio' => now(),
                    'fecha_fin' => null,
                    'usuario_cambio_estado_id' => auth()->id(),
                    'fecha_cambio_estado' => now(),
                    'observaciones' => 'Reactivada por reasignación',
                ]);
            }
        } else {
            Cursada::create([
                'inscripcion_id' => $inscripcion->id,
                'comision_id' => $comision->id,
                'anio' => $anioActual,
                'estado' => Cursada::ESTADO_CURSANDO,
                'modalidad' => $comision->modalidad,
                'es_recursante' => $esRecursante,
                'fecha_inicio' => now(),
            ]);
        }

        // Actualizar estado de la inscripción a 'cursando'
        $inscripcion->update(['estado_ingreso' => Inscripcion::INGRESO_CURSANDO]);

        // Registrar en trayectoria
        \App\Models\Trayectoria::registrarEvento(
            $inscripcion->id,
            \App\Models\Trayectoria::ESTADO_ACTIVO,
            'Asignado a comisión ' . $comision->nombre,
            null,
            auth()->id()
        );

        // Sincronizar cupo actual
        $comision->sincronizarCupo();

        return redirect()->back()
            ->with('success', 'Alumno inscripto exitosamente.');
    }

    /**
     * Actualizar configuración de extracupos
     */
    public function actualizarExtracupos(Request $request, Comision $comision)
    {
        if ($comision->esVirtual()) {
            return redirect()->back()
                ->with('error', 'Las comisiones virtuales no tienen límite de cupos.');
        }

        $validated = $request->validate([
            'extracupos_habilitados' => 'required|boolean',
            'extracupos' => 'required_if:extracupos_habilitados,1|integer|min:0|max:200',
        ]);

        $comision->update([
            'extracupos_habilitados' => $validated['extracupos_habilitados'],
            'extracupos' => $validated['extracupos_habilitados'] ? ($validated['extracupos'] ?? 0) : 0,
        ]);

        return redirect()->back()
            ->with('success', $validated['extracupos_habilitados']
                ? "Extracupos habilitados: {$validated['extracupos']} lugares adicionales."
                : 'Extracupos deshabilitados.');
    }

    /**
     * Archivar/desarchivar comisión
     */
    public function toggleArchivar(Comision $comision)
    {
        if ($comision->estado === 'activa' && !$comision->archivada) {
            return redirect()->back()
                ->with('error', 'No se puede archivar una comisión con estado activa.');
        }

        $comision->update(['archivada' => !$comision->archivada]);

        $mensaje = $comision->archivada ? 'Comisión archivada.' : 'Comisión desarchivada.';

        return redirect()->back()->with('success', $mensaje);
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
        $comision->decrementarCupo();

        return redirect()->back()
            ->with('success', 'Alumno desinscripto exitosamente.');
    }
}
