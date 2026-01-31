<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInscripcionRequest;
use App\Http\Requests\UpdateInscripcionRequest;
use App\Http\Requests\ValidarDocumentacionRequest;
use App\Models\AlumnosUtn\Person;
use App\Models\Inscripcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class InscripcionController extends Controller
{
    /**
     * Mostrar listado de inscripciones
     */
    public function index(Request $request): View
    {
        $query = Inscripcion::query()
            ->with(['usuarioRegistro', 'usuarioValidacion'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('buscar')) {
            $termino = $request->input('buscar');
            // Buscar por datos del alumno en alumnos_utn
            $personIds = Person::on('alumnos_utn')
                ->where(function ($q) use ($termino) {
                    $q->where('nombre', 'like', "%{$termino}%")
                        ->orWhere('apellido', 'like', "%{$termino}%")
                        ->orWhere('documento', 'like', "%{$termino}%")
                        ->orWhere('email', 'like', "%{$termino}%");
                })
                ->pluck('id')
                ->toArray();

            $query->whereIn('person_id', $personIds);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('estado_documentacion')) {
            $query->where('estado_documentacion', $request->input('estado_documentacion'));
        }

        if ($request->filled('estado_ingreso')) {
            $query->where('estado_ingreso', $request->input('estado_ingreso'));
        }

        if ($request->filled('anio_ingreso')) {
            $query->where('anio_ingreso', $request->input('anio_ingreso'));
        }

        if ($request->filled('especialidad')) {
            $query->where('especialidad_id_sysacad', $request->input('especialidad'));
        }

        if ($request->filled('modalidad')) {
            $query->where('modalidad', $request->input('modalidad'));
        }

        if ($request->filled('tipo_ingreso')) {
            $query->where('tipo_ingreso', $request->input('tipo_ingreso'));
        }

        if ($request->filled('turno_ingreso')) {
            $query->where('turno_ingreso', $request->input('turno_ingreso'));
        }

        // Filtro: solo alumnos SIN comisión asignada
        if ($request->filled('sin_comision') && $request->input('sin_comision') === '1') {
            $query->whereDoesntHave('inscripcionesComision', function ($q) {
                $q->whereIn('estado', ['inscripto', 'confirmado']);
            });
        }

        // Filtro: solo alumnos CON comisión asignada
        if ($request->filled('con_comision') && $request->input('con_comision') === '1') {
            $query->whereHas('inscripcionesComision', function ($q) {
                $q->whereIn('estado', ['inscripto', 'confirmado']);
            });
        }

        $inscripciones = $query->paginate(25)->withQueryString();

        // Cargar datos de personas para cada inscripción
        $personIds = $inscripciones->pluck('person_id')->unique()->toArray();
        $personas = Person::on('alumnos_utn')
            ->whereIn('id', $personIds)
            ->get()
            ->keyBy('id');

        // Obtener especialidades para el filtro (conexión sysacad)
        $especialidades = DB::connection('sysacad')->table('sysacad_especialidades')
            ->orderBy('nombre')
            ->get();

        // Obtener turnos únicos desde inscripciones (valores reales)
        $turnos = Inscripcion::select('turno_ingreso')
            ->distinct()
            ->whereNotNull('turno_ingreso')
            ->orderBy('turno_ingreso')
            ->pluck('turno_ingreso');

        // Obtener modalidades únicas desde inscripciones (valores reales)
        $modalidades = Inscripcion::select('modalidad')
            ->distinct()
            ->whereNotNull('modalidad')
            ->orderBy('modalidad')
            ->pluck('modalidad');

        // Años disponibles para el filtro
        $aniosDisponibles = Inscripcion::select('anio_ingreso')
            ->distinct()
            ->orderBy('anio_ingreso', 'desc')
            ->pluck('anio_ingreso');

        if ($aniosDisponibles->isEmpty()) {
            $aniosDisponibles = collect([date('Y')]);
        }

        return view('inscripciones.index', compact(
            'inscripciones',
            'personas',
            'especialidades',
            'turnos',
            'modalidades',
            'aniosDisponibles'
        ));
    }

    /**
     * Mostrar formulario para crear nueva inscripción
     */
    public function create(Request $request): View
    {
        // Obtener especialidades (conexión sysacad)
        $especialidades = DB::connection('sysacad')->table('sysacad_especialidades')
            ->orderBy('nombre')
            ->get();

        // Obtener turnos (conexión sysacad)
        $turnos = DB::connection('sysacad')->table('sysacad_turnos')
            ->orderBy('nombre')
            ->get();

        // Obtener modalidades
        $modalidades = Inscripcion::MODALIDADES;

        // Obtener tipos de ingreso
        $tiposIngreso = Inscripcion::TIPOS_INGRESO;

        // Si se pasa un person_id, precargar datos del alumno
        $personaSeleccionada = null;
        if ($request->filled('person_id')) {
            $personaSeleccionada = Person::on('alumnos_utn')
                ->with(['academicoDatos', 'secundariaDato', 'formularioDato'])
                ->find($request->input('person_id'));
        }

        return view('inscripciones.create', compact(
            'especialidades',
            'turnos',
            'modalidades',
            'tiposIngreso',
            'personaSeleccionada'
        ));
    }

    /**
     * Guardar nueva inscripción
     */
    public function store(StoreInscripcionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Verificar duplicado
        if (Inscripcion::esDuplicado($data['person_id'], $data['anio_ingreso'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ya existe una inscripción activa para este alumno en el año seleccionado.');
        }

        // Verificar que el alumno existe en alumnos_utn
        $persona = Person::on('alumnos_utn')->find($data['person_id']);
        if (!$persona) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'El alumno seleccionado no existe en el sistema.');
        }

        // Crear inscripción
        $inscripcion = Inscripcion::create([
            ...$data,
            'estado' => Inscripcion::ESTADO_PENDIENTE,
            'usuario_registro_id' => auth()->id(),
        ]);

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', 'Inscripción registrada exitosamente.');
    }

    /**
     * Mostrar detalle de una inscripción
     */
    public function show(Inscripcion $inscripcion): View
    {
        $inscripcion->load([
            'usuarioRegistro',
            'usuarioValidacion',
            'condicionesParticulares',
            'solicitudesCambio',
            'trayectorias' => fn($q) => $q->orderBy('fecha_inicio', 'desc'),
        ]);

        // Cargar datos del alumno
        $persona = Person::on('alumnos_utn')
            ->with(['academicoDatos', 'secundariaDato', 'formularioDato'])
            ->find($inscripcion->person_id);

        // Obtener especialidad (conexión sysacad)
        $especialidad = DB::connection('sysacad')->table('sysacad_especialidades')
            ->where('id_sysacad', $inscripcion->especialidad_id_sysacad)
            ->first();

        $especialidadAlternativa = null;
        if ($inscripcion->especialidad_alternativa_id_sysacad) {
            $especialidadAlternativa = DB::connection('sysacad')->table('sysacad_especialidades')
                ->where('id_sysacad', $inscripcion->especialidad_alternativa_id_sysacad)
                ->first();
        }

        // Cargar datos de trayectoria académica
        $trayectoriaData = $this->cargarTrayectoriaAcademica($inscripcion);

        // Obtener comisiones disponibles para solicitud de cambio
        $comisionesDisponibles = \App\Models\Comision::activas()
            ->conCuposDisponibles()
            ->where('anio', date('Y'))
            ->orderBy('turno')
            ->orderBy('modalidad')
            ->orderBy('nombre')
            ->get();

        return view('inscripciones.show', array_merge(
            compact('inscripcion', 'persona', 'especialidad', 'especialidadAlternativa', 'comisionesDisponibles'),
            $trayectoriaData
        ));
    }

    /**
     * Cargar datos de trayectoria académica del alumno
     * Incluye: comisión asignada, cursada, materias, notas y asistencia
     */
    private function cargarTrayectoriaAcademica(Inscripcion $inscripcion): array
    {
        $resultado = [
            'tieneComision' => false,
            'inscripcionComision' => null,
            'comision' => null,
            'cursada' => null,
            'materias' => collect(),
            'notasPorMateria' => [],
            'asistencias' => collect(),
            'puedeAprobarCursada' => false,
            'resumenNotas' => null,
        ];

        // Obtener inscripción a comisión (activa: inscripto, confirmado, o ya aprobada)
        $inscripcionComision = $inscripcion->inscripcionesComision()
            ->with(['comision.materias', 'comision.municipio', 'comision.aula', 'comision.docentesActivos.docente'])
            ->whereIn('estado', ['inscripto', 'confirmado', 'aprobado'])
            ->latest()
            ->first();

        if (!$inscripcionComision || !$inscripcionComision->comision) {
            return $resultado;
        }

        $resultado['tieneComision'] = true;
        $resultado['inscripcionComision'] = $inscripcionComision;
        $resultado['comision'] = $inscripcionComision->comision;

        $comision = $inscripcionComision->comision;

        // Buscar cursada asociada
        $cursada = \App\Models\Cursada::where('inscripcion_id', $inscripcion->id)
            ->where('comision_id', $comision->id)
            ->first();
        $resultado['cursada'] = $cursada;

        // Obtener materias de la comisión
        $materias = $comision->materias ?? collect();
        $resultado['materias'] = $materias;

        // Obtener asistencias
        $asistencias = $inscripcionComision->asistencias()
            ->orderBy('fecha', 'desc')
            ->limit(10)
            ->get();
        $resultado['asistencias'] = $asistencias;

        // Procesar notas por materia
        $notasPorMateria = [];
        $materiasAprobadas = 0;
        $totalMaterias = $materias->count();

        foreach ($materias as $materia) {
            $evaluaciones = \App\Models\Evaluacion::where('materia_id', $materia->id)
                ->where(function ($q) use ($comision) {
                    $q->where('comision_id', $comision->id)
                        ->orWhereNull('comision_id');
                })
                ->orderBy('instancia')
                ->orderBy('fecha')
                ->get();

            $notasMateria = [];
            $mejorNota = null;
            $aprobada = false;

            foreach ($evaluaciones as $evaluacion) {
                $nota = \App\Models\Nota::where('inscripcion_id', $inscripcion->id)
                    ->where('evaluacion_id', $evaluacion->id)
                    ->first();

                $notasMateria[] = [
                    'evaluacion' => $evaluacion,
                    'nota' => $nota,
                ];

                if ($nota && $nota->nota !== null) {
                    if ($mejorNota === null || $nota->nota > $mejorNota) {
                        $mejorNota = $nota->nota;
                    }
                    if ($nota->nota >= 6) {
                        $aprobada = true;
                    }
                }
            }

            if ($aprobada) {
                $materiasAprobadas++;
            }

            $notasPorMateria[$materia->id] = [
                'materia' => $materia,
                'notas' => $notasMateria,
                'nota_final' => $mejorNota,
                'aprobada' => $aprobada,
            ];
        }

        $resultado['notasPorMateria'] = $notasPorMateria;

        // Calcular resumen
        $porcentajeAsistencia = $inscripcionComision->calcularPorcentajeAsistencia();

        $resultado['resumenNotas'] = [
            'total_materias' => $totalMaterias,
            'materias_aprobadas' => $materiasAprobadas,
            'porcentaje_asistencia' => $porcentajeAsistencia,
        ];

        // Puede aprobar si todas las materias están aprobadas y no está ya aprobado
        $resultado['puedeAprobarCursada'] = $totalMaterias > 0
            && $materiasAprobadas === $totalMaterias
            && $inscripcion->estado_ingreso !== Inscripcion::INGRESO_APROBADO;

        return $resultado;
    }

    /**
     * Mostrar formulario para editar inscripción
     */
    public function edit(Inscripcion $inscripcion): View|RedirectResponse
    {
        if (!$inscripcion->puedeModificarse()) {
            return redirect()
                ->route('inscripciones.show', $inscripcion)
                ->with('error', 'Esta inscripción no puede ser modificada en su estado actual.');
        }

        // Cargar datos del alumno
        $persona = Person::on('alumnos_utn')
            ->with(['academicoDatos', 'secundariaDato'])
            ->find($inscripcion->person_id);

        // Obtener especialidades (conexión sysacad)
        $especialidades = DB::connection('sysacad')->table('sysacad_especialidades')
            ->orderBy('nombre')
            ->get();

        // Obtener turnos (conexión sysacad)
        $turnos = DB::connection('sysacad')->table('sysacad_turnos')
            ->orderBy('nombre')
            ->get();

        $modalidades = Inscripcion::MODALIDADES;
        $tiposIngreso = Inscripcion::TIPOS_INGRESO;

        return view('inscripciones.edit', compact(
            'inscripcion',
            'persona',
            'especialidades',
            'turnos',
            'modalidades',
            'tiposIngreso'
        ));
    }

    /**
     * Actualizar inscripción existente
     */
    public function update(UpdateInscripcionRequest $request, Inscripcion $inscripcion): RedirectResponse
    {
        if (!$inscripcion->puedeModificarse()) {
            return redirect()
                ->route('inscripciones.show', $inscripcion)
                ->with('error', 'Esta inscripción no puede ser modificada en su estado actual.');
        }

        $inscripcion->update($request->validated());

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', 'Inscripción actualizada exitosamente.');
    }

    /**
     * Validar documentación de una inscripción
     */
    public function validarDocumentacion(ValidarDocumentacionRequest $request, Inscripcion $inscripcion): RedirectResponse
    {
        $data = $request->validated();

        $inscripcion->update([
            ...$data,
            'usuario_validacion_id' => auth()->id(),
            'fecha_validacion' => now(),
        ]);

        // Refrescar el modelo para obtener los valores actualizados
        $inscripcion->refresh();

        // Actualizar estado según documentación
        if ($inscripcion->documentacionCompleta()) {
            // Si toda la documentación está validada, cambiar ambos estados
            $inscripcion->update([
                'estado' => Inscripcion::ESTADO_DOCUMENTACION_OK,
                'estado_documentacion' => Inscripcion::DOC_VALIDADA,
            ]);
            $mensaje = 'Documentación validada completamente. La inscripción está lista para confirmar.';
        } else {
            // Si falta algún documento, volver a pendiente
            $updateData = ['estado_documentacion' => Inscripcion::DOC_PENDIENTE];
            if ($inscripcion->estado === Inscripcion::ESTADO_DOCUMENTACION_OK) {
                $updateData['estado'] = Inscripcion::ESTADO_PENDIENTE;
            }
            $inscripcion->update($updateData);
            $mensaje = 'Documentación actualizada. Faltan documentos por validar.';
        }

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', $mensaje);
    }

    /**
     * Confirmar inscripción
     */
    public function confirmar(Inscripcion $inscripcion): RedirectResponse
    {
        if (!$inscripcion->documentacionCompleta()) {
            return redirect()
                ->route('inscripciones.show', $inscripcion)
                ->with('error', 'No se puede confirmar la inscripción sin validar toda la documentación.');
        }

        $inscripcion->update([
            'estado' => Inscripcion::ESTADO_CONFIRMADO,
            'estado_documentacion' => Inscripcion::DOC_CONFIRMADA,
        ]);

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', 'Inscripción confirmada exitosamente.');
    }

    /**
     * Cancelar inscripción
     */
    public function cancelar(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        if (!$inscripcion->puedeCancelarse()) {
            return redirect()
                ->route('inscripciones.show', $inscripcion)
                ->with('error', 'Esta inscripción no puede ser cancelada en su estado actual.');
        }

        $inscripcion->update([
            'estado' => Inscripcion::ESTADO_CANCELADO,
            'observaciones' => $request->input('motivo_cancelacion', $inscripcion->observaciones),
        ]);

        return redirect()
            ->route('inscripciones.index')
            ->with('success', 'Inscripción cancelada exitosamente.');
    }

    /**
     * Buscar aspirante en alumnos_utn
     */
    public function buscarAspirante(Request $request)
    {
        $termino = $request->input('q', '');

        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $personas = Person::on('alumnos_utn')
            ->where(function ($query) use ($termino) {
                $query->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('apellido', 'like', "%{$termino}%")
                    ->orWhere('documento', 'like', "%{$termino}%")
                    ->orWhere('email', 'like', "%{$termino}%");
            })
            ->with(['formularioDato'])
            ->limit(20)
            ->get()
            ->map(function ($persona) {
                return [
                    'id' => $persona->id,
                    'text' => "{$persona->apellido}, {$persona->nombre} - DNI: {$persona->documento}",
                    'documento' => $persona->documento,
                    'email' => $persona->email,
                    'estado_formulario' => $persona->formularioDato?->estado ?? 'Sin formulario',
                ];
            });

        return response()->json($personas);
    }

    /**
     * Mostrar formulario de importación masiva
     */
    public function showImportar(Request $request): View
    {
        // Obtener alumnos de alumnos_utn que no tienen inscripción activa
        $inscripcionesActivas = Inscripcion::activas()
            ->pluck('person_id')
            ->toArray();

        $query = Person::on('alumnos_utn')
            ->with(['academicoDatos', 'formularioDato'])
            ->whereNotIn('id', $inscripcionesActivas);

        // Filtro por estado del formulario (por defecto solo Completo)
        if ($request->boolean('incluir_incompletos')) {
            // Mostrar todos (Completo e Incompleto)
            $query->whereHas('formularioDato');
        } else {
            // Solo mostrar Completo
            $query->whereHas('formularioDato', function ($q) {
                $q->where('estado', 'Completo');
            });
        }

        // Filtro por búsqueda de nombre/DNI
        if ($request->filled('buscar')) {
            $termino = $request->input('buscar');
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('apellido', 'like', "%{$termino}%")
                    ->orWhere('documento', 'like', "%{$termino}%");
            });
        }

        // Filtro por modalidad (en academico_datos)
        if ($request->filled('modalidad')) {
            $modalidad = $request->input('modalidad');
            $query->whereHas('academicoDatos', function ($q) use ($modalidad) {
                $q->where('modalidad', $modalidad);
            });
        }

        // Filtro por turno de ingreso (en academico_datos)
        if ($request->filled('turno_ingreso')) {
            $turno = $request->input('turno_ingreso');
            $query->whereHas('academicoDatos', function ($q) use ($turno) {
                $q->where('turno_ingreso', $turno);
            });
        }

        // Filtro por año de ingreso (en academico_datos)
        if ($request->filled('anio_ingreso')) {
            $anio = $request->input('anio_ingreso');
            $query->whereHas('academicoDatos', function ($q) use ($anio) {
                $q->where('ingreso_carrera', $anio);
            });
        }

        $alumnosDisponibles = $query
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->paginate(25);

        // Cargar especialidades indexadas por id_sysacad para mostrar nombres en la tabla
        $especialidades = DB::connection('sysacad')->table('sysacad_especialidades')
            ->get()
            ->keyBy('id_sysacad');

        // Obtener turnos únicos desde academico_datos (valores reales)
        $turnos = DB::connection('alumnos_utn')
            ->table('academico_datos')
            ->select('turno_ingreso')
            ->distinct()
            ->whereNotNull('turno_ingreso')
            ->orderBy('turno_ingreso')
            ->pluck('turno_ingreso');

        // Obtener modalidades únicas desde academico_datos (valores reales)
        $modalidades = DB::connection('alumnos_utn')
            ->table('academico_datos')
            ->select('modalidad')
            ->distinct()
            ->whereNotNull('modalidad')
            ->orderBy('modalidad')
            ->pluck('modalidad');

        // Años de ingreso disponibles en alumnos_utn
        $aniosDisponibles = DB::connection('alumnos_utn')
            ->table('academico_datos')
            ->select('ingreso_carrera')
            ->distinct()
            ->orderBy('ingreso_carrera', 'desc')
            ->pluck('ingreso_carrera');

        return view('inscripciones.importar', compact(
            'alumnosDisponibles',
            'especialidades',
            'turnos',
            'modalidades',
            'aniosDisponibles'
        ));
    }

    /**
     * Procesar importación masiva de inscripciones
     */
    public function importar(Request $request): RedirectResponse
    {
        // Si se seleccionó "importar todos", obtener todos los IDs filtrados
        if ($request->input('importar_todos') === '1') {
            $personIds = $this->obtenerTodosLosPersonIds($request);
        } else {
            $request->validate([
                'person_ids' => 'required|array|min:1',
                'person_ids.*' => 'integer',
            ]);
            $personIds = $request->input('person_ids');
        }

        if (empty($personIds)) {
            return redirect()->back()->with('error', 'No hay alumnos para importar.');
        }

        $importados = 0;
        $errores = 0;
        $duplicados = 0;
        $sinDatosAcademicos = 0;

        DB::beginTransaction();

        try {
            foreach ($personIds as $personId) {
                // Verificar que el alumno existe y cargar sus datos académicos
                $persona = Person::on('alumnos_utn')
                    ->with('academicoDatos')
                    ->find($personId);

                if (!$persona) {
                    $errores++;
                    continue;
                }

                // Obtener datos académicos del alumno (primer registro)
                $datosAcademicos = $persona->academicoDatos->first();
                if (!$datosAcademicos) {
                    $sinDatosAcademicos++;
                    continue;
                }

                // El año de ingreso se toma de los datos académicos del alumno
                $anioIngreso = $datosAcademicos->ingreso_carrera;

                // Verificar duplicado con el año de ingreso del alumno
                if (Inscripcion::esDuplicado($personId, $anioIngreso)) {
                    $duplicados++;
                    continue;
                }

                // Crear inscripción con TODOS los datos del alumno desde alumnos_utn
                Inscripcion::create([
                    'person_id' => $personId,
                    'anio_ingreso' => $anioIngreso,
                    'especialidad_id_sysacad' => $datosAcademicos->especialidad_id,
                    'especialidad_alternativa_id_sysacad' => $datosAcademicos->especialidad_alternativa_id,
                    'modalidad' => $datosAcademicos->modalidad ?? 'Presencial',
                    'turno_ingreso' => $datosAcademicos->turno_ingreso,
                    'turno_carrera' => $datosAcademicos->turno_carrera,
                    'tipo_ingreso' => $datosAcademicos->tipo_ingreso ?? 'Extensivo',
                    'estado' => Inscripcion::ESTADO_PENDIENTE,
                    'estado_documentacion' => Inscripcion::DOC_PENDIENTE,
                    'estado_ingreso' => Inscripcion::INGRESO_INSCRIPTO,
                    'usuario_registro_id' => auth()->id(),
                ]);

                $importados++;
            }

            DB::commit();

            $mensaje = "Importación completada: {$importados} inscripciones creadas.";
            if ($duplicados > 0) {
                $mensaje .= " {$duplicados} duplicados omitidos.";
            }
            if ($sinDatosAcademicos > 0) {
                $mensaje .= " {$sinDatosAcademicos} sin datos académicos.";
            }
            if ($errores > 0) {
                $mensaje .= " {$errores} errores.";
            }

            return redirect()
                ->route('inscripciones.index')
                ->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Error al importar inscripciones: ' . $e->getMessage());
        }
    }

    /**
     * Exportar listado de inscripciones a Excel
     */
    public function exportar(Request $request)
    {
        $query = Inscripcion::query()
            ->with(['usuarioRegistro'])
            ->orderBy('created_at', 'desc');

        // Aplicar los mismos filtros que en index
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('anio_ingreso')) {
            $query->where('anio_ingreso', $request->input('anio_ingreso'));
        }

        if ($request->filled('especialidad')) {
            $query->where('especialidad_id_sysacad', $request->input('especialidad'));
        }

        if ($request->filled('modalidad')) {
            $query->where('modalidad', $request->input('modalidad'));
        }

        $inscripciones = $query->get();

        // Cargar datos de personas
        $personIds = $inscripciones->pluck('person_id')->unique()->toArray();
        $personas = Person::on('alumnos_utn')
            ->whereIn('id', $personIds)
            ->get()
            ->keyBy('id');

        // Cargar especialidades (conexión sysacad)
        $especialidades = DB::connection('sysacad')->table('sysacad_especialidades')
            ->get()
            ->keyBy('id_sysacad');

        // Preparar datos para exportación
        $data = [];
        $data[] = [
            'DNI',
            'Apellido',
            'Nombre',
            'Email',
            'Teléfono',
            'Año Ingreso',
            'Especialidad',
            'Modalidad',
            'Tipo Ingreso',
            'Estado',
            'Fecha Inscripción',
        ];

        foreach ($inscripciones as $inscripcion) {
            $persona = $personas->get($inscripcion->person_id);
            $especialidad = $especialidades->get($inscripcion->especialidad_id_sysacad);

            $data[] = [
                $persona?->documento ?? 'N/A',
                $persona?->apellido ?? 'N/A',
                $persona?->nombre ?? 'N/A',
                $persona?->email ?? 'N/A',
                $persona?->telefono_celular ?? $persona?->telefono_fijo ?? 'N/A',
                $inscripcion->anio_ingreso,
                $especialidad?->nombre ?? 'N/A',
                $inscripcion->modalidad,
                $inscripcion->tipo_ingreso,
                Inscripcion::ESTADOS[$inscripcion->estado] ?? $inscripcion->estado,
                $inscripcion->created_at->format('d/m/Y H:i'),
            ];
        }

        // Generar archivo CSV con BOM para UTF-8 correcto en Excel
        $filename = 'inscripciones_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Agregar BOM para que Excel reconozca UTF-8
        fwrite($handle, "\xEF\xBB\xBF");

        foreach ($data as $row) {
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Eliminar inscripción (soft delete)
     */
    public function destroy(Inscripcion $inscripcion): RedirectResponse
    {
        $inscripcion->delete();

        return redirect()
            ->route('inscripciones.index')
            ->with('success', 'Inscripción eliminada exitosamente.');
    }

    /**
     * Obtener todos los person_ids aplicando los mismos filtros de showImportar
     * Se usa cuando el usuario selecciona "importar todos"
     */
    private function obtenerTodosLosPersonIds(Request $request): array
    {
        // IDs de personas que ya tienen inscripción activa
        $inscripcionesExistentes = Inscripcion::whereNotIn('estado', [
            Inscripcion::ESTADO_CANCELADO,
            Inscripcion::ESTADO_BAJA
        ])->pluck('person_id')->toArray();

        // Query base desde alumnos_utn
        $query = Person::on('alumnos_utn')
            ->whereNotIn('id', $inscripcionesExistentes)
            ->whereHas('academicoDatos');

        // Solo con formulario completo por defecto
        if (!$request->input('incluir_incompletos')) {
            $query->whereHas('formularioDato', function ($q) {
                $q->where('estado', 'Completo');
            });
        }

        // Aplicar filtros
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('apellido', 'like', "%{$buscar}%")
                    ->orWhere('documento', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('modalidad') || $request->filled('turno_ingreso') || $request->filled('anio_ingreso')) {
            $query->whereHas('academicoDatos', function ($q) use ($request) {
                if ($request->filled('modalidad')) {
                    $q->where('modalidad', $request->input('modalidad'));
                }
                if ($request->filled('turno_ingreso')) {
                    $q->where('turno_ingreso', $request->input('turno_ingreso'));
                }
                if ($request->filled('anio_ingreso')) {
                    $q->where('ingreso_carrera', $request->input('anio_ingreso'));
                }
            });
        }

        return $query->pluck('id')->toArray();
    }

    /**
     * Aprobar cursada de una inscripción
     * Solo si todas las materias están aprobadas
     */
    public function aprobarCursada(Inscripcion $inscripcion): RedirectResponse
    {
        // Verificar que tenga inscripción a comisión activa (no cancelada/trasladada)
        $inscripcionComision = $inscripcion->inscripcionesComision()
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->with('comision')
            ->first();

        if (!$inscripcionComision) {
            // Verificar si ya está aprobada
            $yaAprobada = $inscripcion->inscripcionesComision()
                ->where('estado', 'aprobado')
                ->exists();

            if ($yaAprobada) {
                return redirect()
                    ->route('inscripciones.show', $inscripcion)
                    ->with('info', 'La cursada ya fue aprobada anteriormente.');
            }

            return redirect()
                ->route('inscripciones.show', $inscripcion)
                ->with('error', 'El alumno no está asignado a ninguna comisión activa.');
        }

        // Cargar datos de trayectoria para verificar
        $trayectoriaData = $this->cargarTrayectoriaAcademica($inscripcion);

        if (!$trayectoriaData['puedeAprobarCursada']) {
            // Generar mensaje de error más descriptivo
            $resumen = $trayectoriaData['resumenNotas'] ?? null;
            if ($inscripcion->estado_ingreso === Inscripcion::INGRESO_APROBADO) {
                $mensaje = 'La cursada ya fue aprobada anteriormente.';
            } elseif (!$resumen || $resumen['total_materias'] === 0) {
                $mensaje = 'La comisión no tiene materias asignadas.';
            } else {
                $mensaje = "Faltan materias por aprobar ({$resumen['materias_aprobadas']}/{$resumen['total_materias']}).";
            }
            return redirect()
                ->route('inscripciones.show', $inscripcion)
                ->with('error', $mensaje);
        }

        // Actualizar estado de la inscripción
        $inscripcion->update([
            'estado_ingreso' => Inscripcion::INGRESO_APROBADO,
            'estado' => Inscripcion::ESTADO_CONFIRMADO,
            'estado_documentacion' => Inscripcion::DOC_CONFIRMADA,
        ]);

        // Actualizar estado de la inscripción a comisión
        $inscripcionComision->update([
            'estado' => 'aprobado',
        ]);

        // Si existe una cursada asociada, actualizarla
        $cursada = \App\Models\Cursada::where('inscripcion_id', $inscripcion->id)
            ->where('comision_id', $inscripcionComision->comision_id)
            ->first();

        if ($cursada) {
            $cursada->cambiarEstado(
                \App\Models\Cursada::ESTADO_APROBADO,
                auth()->id(),
                'Cursada aprobada - Todas las materias completadas'
            );
        }

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', 'Cursada aprobada exitosamente. El alumno ha completado el curso de ingreso.');
    }

    /**
     * Agregar condición particular a una inscripción
     */
    public function agregarCondicion(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $request->validate([
            'tipo' => 'required|string|max:50',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'requiere_adecuacion' => 'nullable|boolean',
        ]);

        $inscripcion->condicionesParticulares()->create([
            'tipo' => $request->tipo,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'requiere_adecuacion' => $request->boolean('requiere_adecuacion'),
            'activa' => true,
            'registrado_por' => auth()->id(),
        ]);

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', 'Condición particular agregada correctamente.');
    }

    /**
     * Desactivar una condición particular
     */
    public function desactivarCondicion(\App\Models\CondicionParticular $condicion): RedirectResponse
    {
        $inscripcionId = $condicion->inscripcion_id;
        $condicion->update(['activa' => false]);

        return redirect()
            ->route('inscripciones.show', $inscripcionId)
            ->with('success', 'Condición particular desactivada.');
    }

    /**
     * Crear solicitud de cambio
     */
    public function crearSolicitud(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $request->validate([
            'tipo' => 'required|in:comision,modalidad,turno',
            'motivo' => 'required|string',
            'comision_destino_id' => 'nullable|exists:comisiones,id',
            'modalidad_destino' => 'nullable|string',
            'turno_destino' => 'nullable|string',
        ]);

        $inscripcion->solicitudesCambio()->create([
            'tipo' => $request->tipo,
            'motivo' => $request->motivo,
            'comision_destino_id' => $request->comision_destino_id,
            'modalidad_destino' => $request->modalidad_destino,
            'turno_destino' => $request->turno_destino,
            'estado' => 'pendiente',
        ]);

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', 'Solicitud de cambio creada correctamente.');
    }
}
