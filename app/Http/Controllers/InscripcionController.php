<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInscripcionRequest;
use App\Http\Requests\UpdateInscripcionRequest;
use App\Http\Requests\ValidarDocumentacionRequest;
use App\Models\AlumnosUtn\Person;
use App\Models\Cursada;
use App\Models\Inscripcion;
use App\Models\Trayectoria;
use App\Services\DataNormalizationService;
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

        // Filtro: solo alumnos SIN comisión asignada (ignora comisiones canceladas/trasladadas)
        if ($request->filled('sin_comision') && $request->input('sin_comision') === '1') {
            $query->whereDoesntHave('inscripcionesComision', function ($q) {
                $q->whereIn('estado', ['inscripto', 'confirmado', 'aprobado']);
            });
        }

        // Filtro: solo alumnos CON comisión asignada (ignora comisiones canceladas/trasladadas)
        if ($request->filled('con_comision') && $request->input('con_comision') === '1') {
            $query->whereHas('inscripcionesComision', function ($q) {
                $q->whereIn('estado', ['inscripto', 'confirmado', 'aprobado']);
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

        // Obtener turnos desde configuración
        $turnos = Inscripcion::getTurnos();

        // Obtener modalidades desde configuración
        $modalidades = Inscripcion::getModalidades();

        // Obtener tipos de ingreso desde configuración
        $tiposIngreso = Inscripcion::getTiposIngreso();

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

        // Verificar si ya tiene inscripción activa
        if (Inscripcion::tieneInscripcionActiva($data['person_id'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ya existe una inscripción activa para este alumno.');
        }

        // Verificar que el alumno existe en alumnos_utn
        $persona = Person::on('alumnos_utn')->find($data['person_id']);
        if (!$persona) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'El alumno seleccionado no existe en el sistema.');
        }

        // Normalizar datos antes de guardar
        $normalizador = new DataNormalizationService();
        $data['modalidad'] = $normalizador->normalizarModalidad($data['modalidad'] ?? null);
        $data['turno_ingreso'] = $normalizador->normalizarTurno($data['turno_ingreso'] ?? null);
        $data['turno_carrera'] = $normalizador->normalizarTurno($data['turno_carrera'] ?? null);
        $data['tipo_ingreso'] = $normalizador->normalizarTipoIngreso($data['tipo_ingreso'] ?? null);

        // Crear inscripción
        $inscripcion = Inscripcion::create([
            ...$data,
            'estado' => Inscripcion::ESTADO_PENDIENTE,
            'usuario_registro_id' => auth()->id(),
        ]);

        $mensaje = 'Inscripción registrada exitosamente.';

        // Agregar mensaje de correcciones si hay
        $mensajeCorrecciones = $normalizador->getMensajeCorrecciones();
        if ($mensajeCorrecciones) {
            $mensaje .= ' ' . str_replace("\n", ' ', $mensajeCorrecciones);
        }

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', $mensaje);
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
            'trayectorias' => fn($q) => $q->with('registradoPor')->orderBy('fecha_inicio', 'desc'),
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

        // Cargar notas finales por materia (puestas por el docente)
        $notasFinales = \App\Models\NotaFinalMateria::where('inscripcion_id', $inscripcion->id)
            ->where('comision_id', $comision->id)
            ->get()
            ->keyBy('materia_id');

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
            $sumaNotas = 0;
            $cantidadNotas = 0;

            foreach ($evaluaciones as $evaluacion) {
                $nota = \App\Models\Nota::where('inscripcion_id', $inscripcion->id)
                    ->where('evaluacion_id', $evaluacion->id)
                    ->first();

                $notasMateria[] = [
                    'evaluacion' => $evaluacion,
                    'nota' => $nota,
                ];

                if ($nota && $nota->nota !== null) {
                    $sumaNotas += $nota->nota;
                    $cantidadNotas++;
                }
            }

            // Nota sugerida = promedio de todas las evaluaciones
            $notaSugerida = $cantidadNotas > 0 ? round($sumaNotas / $cantidadNotas, 2) : null;

            // Nota final = la que puso el docente (desde nota_final_materias)
            $notaFinalMateria = $notasFinales->get($materia->id);
            $notaFinal = $notaFinalMateria?->nota_final;

            // Aprobada usando el snapshot guardado al momento de cargar la nota.
            // Si no tiene snapshot (registros anteriores), usa el método del modelo que también lo contempla.
            $aprobada = $notaFinalMateria !== null && $notaFinalMateria->estaAprobada();

            if ($aprobada) {
                $materiasAprobadas++;
            }

            $notasPorMateria[$materia->id] = [
                'materia' => $materia,
                'notas' => $notasMateria,
                'nota_sugerida' => $notaSugerida,
                'nota_final' => $notaFinal,
                'nota_final_materia' => $notaFinalMateria,
                // Snapshot de la nota mínima usada al calificar esta materia.
                // Si hay nota final cargada, tomar el snapshot del registro.
                // Si todavía no hay nota final, usar la config actual (cursada en curso).
                'nota_aprobacion' => $notaFinalMateria
                    ? (float) ($notaFinalMateria->nota_aprobacion_snapshot ?? 6)
                    : (float) \App\Services\ConfiguracionService::get('nota_aprobacion', 6),
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

        // Puede aprobar si todas las materias tienen nota final aprobada y no está ya aprobado
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

        // Obtener turnos desde configuración
        $turnos = Inscripcion::getTurnos();

        $modalidades = Inscripcion::getModalidades();
        $tiposIngreso = Inscripcion::getTiposIngreso();

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

        $data = $request->validated();

        // Normalizar datos antes de actualizar
        $normalizador = new DataNormalizationService();
        if (isset($data['modalidad'])) {
            $data['modalidad'] = $normalizador->normalizarModalidad($data['modalidad']);
        }
        if (isset($data['turno_ingreso'])) {
            $data['turno_ingreso'] = $normalizador->normalizarTurno($data['turno_ingreso']);
        }
        if (isset($data['turno_carrera'])) {
            $data['turno_carrera'] = $normalizador->normalizarTurno($data['turno_carrera']);
        }
        if (isset($data['tipo_ingreso'])) {
            $data['tipo_ingreso'] = $normalizador->normalizarTipoIngreso($data['tipo_ingreso']);
        }

        $inscripcion->update($data);

        $mensaje = 'Inscripción actualizada exitosamente.';

        // Agregar mensaje de correcciones si hay
        $mensajeCorrecciones = $normalizador->getMensajeCorrecciones();
        if ($mensajeCorrecciones) {
            $mensaje .= ' ' . str_replace("\n", ' ', $mensajeCorrecciones);
        }

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', $mensaje);
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

        // Armar detalle de documentos validados
        $documentos = [];
        if ($inscripcion->doc_dni_validado) $documentos[] = 'DNI';
        if ($inscripcion->doc_titulo_validado) $documentos[] = 'Título';
        if ($inscripcion->doc_analitico_validado) $documentos[] = 'Analítico';

        // Actualizar estado según documentación
        if ($inscripcion->documentacionCompleta()) {
            $inscripcion->update([
                'estado' => Inscripcion::ESTADO_DOCUMENTACION_OK,
                'estado_documentacion' => Inscripcion::DOC_VALIDADA,
            ]);
            $mensaje = 'Documentación validada completamente. La inscripción está lista para confirmar.';

            $motivo = 'Documentación validada completamente (' . implode(', ', $documentos) . ')';
            if (!empty($data['observaciones_documentacion'])) {
                $motivo .= '. Obs: ' . $data['observaciones_documentacion'];
            }
            \App\Models\Trayectoria::registrarEvento(
                $inscripcion->id,
                \App\Models\Trayectoria::ESTADO_ACTIVO,
                $motivo,
                null,
                auth()->id()
            );
        } else {
            $updateData = ['estado_documentacion' => Inscripcion::DOC_PENDIENTE];
            if ($inscripcion->estado === Inscripcion::ESTADO_DOCUMENTACION_OK) {
                $updateData['estado'] = Inscripcion::ESTADO_PENDIENTE;
            }
            $inscripcion->update($updateData);
            $mensaje = 'Documentación actualizada. Faltan documentos por validar.';

            $validados = !empty($documentos) ? implode(', ', $documentos) : 'ninguno';
            $motivo = "Documentación actualizada (validados: {$validados}). Faltan documentos.";
            if (!empty($data['observaciones_documentacion'])) {
                $motivo .= ' Obs: ' . $data['observaciones_documentacion'];
            }
            \App\Models\Trayectoria::registrarEvento(
                $inscripcion->id,
                \App\Models\Trayectoria::ESTADO_ACTIVO,
                $motivo,
                null,
                auth()->id()
            );
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

        \App\Models\Trayectoria::registrarEvento(
            $inscripcion->id,
            \App\Models\Trayectoria::ESTADO_ACTIVO,
            'Inscripción confirmada con documentación completa',
            null,
            auth()->id()
        );

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', 'Inscripción confirmada exitosamente.');
    }

    /**
     * Cancelar inscripción
     * - Da de baja al alumno de las comisiones asignadas
     * - Libera los cupos de las comisiones
     * - Registra el evento en la trayectoria
     */
    public function cancelar(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        if (!$inscripcion->puedeCancelarse()) {
            return redirect()
                ->route('inscripciones.show', $inscripcion)
                ->with('error', 'Esta inscripción no puede ser cancelada en su estado actual.');
        }

        $motivoCancelacion = $request->input('motivo_cancelacion', 'Cancelación de inscripción');

        DB::transaction(function () use ($inscripcion, $motivoCancelacion) {
            // 1. Dar de baja de las comisiones activas y liberar cupos
            $inscripcionesComision = $inscripcion->inscripcionesComision()
                ->whereIn('estado', ['inscripto', 'confirmado'])
                ->with('comision')
                ->get();

            foreach ($inscripcionesComision as $inscripcionComision) {
                // Cambiar estado de la inscripción a comisión
                $inscripcionComision->update([
                    'estado' => 'cancelado',
                    'observaciones' => $motivoCancelacion . ' - Fecha: ' . now()->format('d/m/Y H:i'),
                ]);

                // Dar de baja la cursada asociada (si existe y está activa)
                $cursadaActiva = Cursada::where('inscripcion_id', $inscripcion->id)
                    ->where('comision_id', $inscripcionComision->comision_id)
                    ->whereIn('estado', [Cursada::ESTADO_CURSANDO])
                    ->first();

                if ($cursadaActiva) {
                    $cursadaActiva->update([
                        'estado' => Cursada::ESTADO_BAJA,
                        'fecha_fin' => now(),
                        'usuario_cambio_estado_id' => auth()->id(),
                        'fecha_cambio_estado' => now(),
                        'observaciones' => $motivoCancelacion,
                    ]);
                }

                // Decrementar cupo de la comisión
                if ($inscripcionComision->comision) {
                    $inscripcionComision->comision->decrementarCupo();
                }
            }

            // 2. Actualizar estados de la inscripción
            $inscripcion->update([
                'estado' => Inscripcion::ESTADO_CANCELADO,
                'estado_ingreso' => Inscripcion::INGRESO_CANCELADO,
                'observaciones' => $motivoCancelacion,
            ]);

            // 3. Registrar en trayectoria
            \App\Models\Trayectoria::registrarEvento(
                $inscripcion->id,
                \App\Models\Trayectoria::ESTADO_CANCELADO,
                $motivoCancelacion,
                true, // Es voluntaria
                auth()->id()
            );
        });

        return redirect()
            ->route('inscripciones.index')
            ->with('success', 'Inscripción cancelada exitosamente. El alumno fue dado de baja de las comisiones asignadas.');
    }

    /**
     * Reactivar una inscripción cancelada
     * - Cambia el estado a 'confirmado' o 'documentacion_ok' según documentación
     * - Registra el evento de reincorporación en la trayectoria
     * - El alumno deberá ser asignado a una comisión manualmente
     */
    public function reactivar(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        // Solo se pueden reactivar inscripciones canceladas
        if ($inscripcion->estado !== Inscripcion::ESTADO_CANCELADO) {
            return redirect()
                ->route('inscripciones.show', $inscripcion)
                ->with('error', 'Solo se pueden reactivar inscripciones que estén canceladas.');
        }

        $motivoReactivacion = $request->input('motivo_reactivacion', 'Reactivación de inscripción');

        DB::transaction(function () use ($inscripcion, $motivoReactivacion) {
            // Determinar el nuevo estado según la documentación
            $nuevoEstado = $inscripcion->documentacionCompleta() 
                ? Inscripcion::ESTADO_DOCUMENTACION_OK 
                : Inscripcion::ESTADO_PENDIENTE;
            
            $nuevoEstadoIngreso = Inscripcion::INGRESO_INSCRIPTO;

            // Actualizar estados de la inscripción
            $inscripcion->update([
                'estado' => $nuevoEstado,
                'estado_ingreso' => $nuevoEstadoIngreso,
                'observaciones' => $motivoReactivacion . ' - Reactivada el ' . now()->format('d/m/Y H:i'),
            ]);

            // Registrar en trayectoria como reincorporación
            \App\Models\Trayectoria::registrarEvento(
                $inscripcion->id,
                \App\Models\Trayectoria::ESTADO_REINCORPORADO,
                $motivoReactivacion,
                null, // No aplica es_voluntaria para reincorporación
                auth()->id()
            );
        });

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', 'Inscripción reactivada exitosamente. Ahora puede asignar una comisión al alumno.');
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

        // Detectar alumnos que se reinscribirían (tienen inscripciones canceladas previas)
        $personIdsPagina = $alumnosDisponibles->pluck('id')->toArray();
        $reinscripciones = [];

        if (!empty($personIdsPagina)) {
            $inscripcionesCanceladas = Inscripcion::withTrashed()
                ->whereIn('person_id', $personIdsPagina)
                ->where(function ($q) {
                    $q->where('estado', Inscripcion::ESTADO_CANCELADO)
                      ->orWhere('estado_ingreso', Inscripcion::INGRESO_CANCELADO);
                })
                ->orderBy('anio_ingreso', 'desc')
                ->get()
                ->groupBy('person_id');

            foreach ($inscripcionesCanceladas as $personId => $inscripciones) {
                $reinscripciones[$personId] = $inscripciones->map(function ($insc) {
                    return [
                        'anio_ingreso' => $insc->anio_ingreso,
                        'estado' => $insc->estado,
                        'estado_ingreso' => $insc->estado_ingreso,
                        'created_at' => $insc->created_at?->format('d/m/Y'),
                        'especialidad_nombre' => $insc->especialidad_nombre,
                    ];
                })->toArray();
            }
        }

        $cantidadReinscripciones = count($reinscripciones);

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
            'aniosDisponibles',
            'reinscripciones',
            'cantidadReinscripciones'
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
        $reinscriptos = 0;

        // Inicializar servicio de normalización
        $normalizador = new DataNormalizationService();

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

                // Verificar si ya tiene inscripción activa (cursando, aprobado, etc.)
                if (Inscripcion::tieneInscripcionActiva($personId)) {
                    $duplicados++;
                    continue;
                }

                // Normalizar datos
                $modalidadNormalizada = $normalizador->normalizarModalidad($datosAcademicos->modalidad);
                $turnoIngresoNormalizado = $normalizador->normalizarTurno($datosAcademicos->turno_ingreso);
                $turnoCarreraNormalizado = $normalizador->normalizarTurno($datosAcademicos->turno_carrera);
                $tipoIngresoNormalizado = $normalizador->normalizarTipoIngreso($datosAcademicos->tipo_ingreso);

                $datosInscripcion = [
                    'anio_ingreso' => $anioIngreso,
                    'especialidad_id_sysacad' => $datosAcademicos->especialidad_id,
                    'especialidad_alternativa_id_sysacad' => $datosAcademicos->especialidad_alternativa_id,
                    'modalidad' => $modalidadNormalizada,
                    'turno_ingreso' => $turnoIngresoNormalizado,
                    'turno_carrera' => $turnoCarreraNormalizado,
                    'tipo_ingreso' => $tipoIngresoNormalizado,
                    'estado' => Inscripcion::ESTADO_PENDIENTE,
                    'estado_documentacion' => Inscripcion::DOC_PENDIENTE,
                    'estado_ingreso' => Inscripcion::INGRESO_INSCRIPTO,
                    'usuario_registro_id' => auth()->id(),
                ];

                // Verificar si tiene inscripción cancelada existente → REINSCRIPCIÓN
                $inscripcionCancelada = Inscripcion::inscripcionCancelada($personId);

                if ($inscripcionCancelada) {
                    // Restaurar si estaba soft-deleted
                    if ($inscripcionCancelada->trashed()) {
                        $inscripcionCancelada->restore();
                    }

                    // Actualizar la inscripción existente con los nuevos datos
                    $inscripcionCancelada->update(array_merge($datosInscripcion, [
                        'observaciones' => 'Reinscripción - Actualizada con datos del nuevo formulario (año anterior: ' . $inscripcionCancelada->anio_ingreso . ')',
                        'doc_dni_validado' => false,
                        'doc_titulo_validado' => false,
                        'doc_analitico_validado' => false,
                        'observaciones_documentacion' => null,
                        'usuario_validacion_id' => null,
                        'fecha_validacion' => null,
                    ]));

                    // Registrar nueva trayectoria de reinscripción
                    Trayectoria::registrarEvento(
                        $inscripcionCancelada->id,
                        Trayectoria::ESTADO_ACTIVO,
                        'Reinscripción al curso de ingreso ' . $anioIngreso,
                        null,
                        auth()->id()
                    );

                    $reinscriptos++;
                    $importados++;
                } else {
                    // Nueva inscripción (alumno sin registro previo en PAICAT)
                    $inscripcion = Inscripcion::create(array_merge($datosInscripcion, [
                        'person_id' => $personId,
                    ]));

                    // Trayectoria inicial
                    Trayectoria::registrarEvento(
                        $inscripcion->id,
                        Trayectoria::ESTADO_ACTIVO,
                        'Inscripción importada desde formulario de preinscripción',
                        null,
                        auth()->id()
                    );

                    $importados++;
                }
            }

            DB::commit();

            $mensaje = "Importación completada: {$importados} inscripciones procesadas.";
            if ($reinscriptos > 0) {
                $mensaje .= " {$reinscriptos} reinscripciones actualizadas.";
            }
            if ($duplicados > 0) {
                $mensaje .= " {$duplicados} con inscripción activa (omitidos).";
            }
            if ($sinDatosAcademicos > 0) {
                $mensaje .= " {$sinDatosAcademicos} sin datos académicos.";
            }
            if ($errores > 0) {
                $mensaje .= " {$errores} errores.";
            }

            // Agregar mensaje de correcciones si hay
            $mensajeCorrecciones = $normalizador->getMensajeCorrecciones();
            if ($mensajeCorrecciones) {
                $mensaje .= "\n\n" . $mensajeCorrecciones;
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
                Inscripcion::getEstados()[$inscripcion->estado] ?? $inscripcion->estado,
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
     * Guardar nota final de una materia para un alumno (puesta por el docente)
     */
    public function guardarNotaFinalMateria(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $validated = $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'comision_id' => 'required|exists:comisiones,id',
            'nota_final' => 'required|numeric|min:0|max:10',
            'observaciones' => 'nullable|string|max:500',
        ]);

        \App\Models\NotaFinalMateria::updateOrCreate(
            [
                'inscripcion_id' => $inscripcion->id,
                'comision_id' => $validated['comision_id'],
                'materia_id' => $validated['materia_id'],
            ],
            [
                'nota_final' => $validated['nota_final'],
                // Snapshot de la nota mínima vigente al momento de cargar la nota.
                'nota_aprobacion_snapshot' => \App\Services\ConfiguracionService::get('nota_aprobacion', 6),
                'cargado_por' => auth()->id(),
                'observaciones' => $validated['observaciones'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Nota final de materia guardada correctamente.');
    }

    /**
     * Eliminar nota final de una materia
     */
    public function eliminarNotaFinalMateria(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $validated = $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'comision_id' => 'required|exists:comisiones,id',
        ]);

        \App\Models\NotaFinalMateria::where('inscripcion_id', $inscripcion->id)
            ->where('comision_id', $validated['comision_id'])
            ->where('materia_id', $validated['materia_id'])
            ->delete();

        return redirect()->back()->with('success', 'Nota final eliminada.');
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
            'tipo' => 'required|in:comision',
            'motivo' => 'required|string|min:10',
            'comision_destino_id' => 'required|exists:comisiones,id',
        ]);

        // Verificar límite configurable de solicitudes por año
        $maxSolicitudes = \App\Services\ConfiguracionService::get('max_solicitudes_cambio', 3);
        if ($maxSolicitudes > 0) {
            $solicitudesAnio = $inscripcion->solicitudesCambio()
                ->whereYear('created_at', now()->year)
                ->whereNotIn('estado', [\App\Models\SolicitudCambio::ESTADO_CANCELADA])
                ->count();

            if ($solicitudesAnio >= $maxSolicitudes) {
                return back()
                    ->withInput()
                    ->with('error', "Se alcanzó el límite de {$maxSolicitudes} solicitudes de cambio por año para este alumno (ya tiene {$solicitudesAnio}).");
            }
        }

        // Obtener comisión actual del alumno
        $inscripcionComisionActual = $inscripcion->inscripcionesComision()
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->with('comision')
            ->first();

        $comisionActual = $inscripcionComisionActual?->comision;

        if (!$comisionActual) {
            return back()
                ->withInput()
                ->with('error', 'El alumno debe estar asignado a una comisión para solicitar un cambio.');
        }

        if ($comisionActual->id == $request->comision_destino_id) {
            return back()
                ->withInput()
                ->with('error', 'No puede solicitar cambio a la misma comisión en la que ya está inscripto.');
        }

        // Verificar si ya tiene una solicitud pendiente
        $solicitudPendiente = $inscripcion->solicitudesCambio()
            ->where('tipo', 'comision')
            ->whereIn('estado', [
                \App\Models\SolicitudCambio::ESTADO_PENDIENTE,
                \App\Models\SolicitudCambio::ESTADO_EN_REVISION,
                \App\Models\SolicitudCambio::ESTADO_TRUEQUE_DETECTADO,
            ])
            ->exists();

        if ($solicitudPendiente) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe una solicitud de cambio pendiente para este alumno.');
        }

        // Obtener comisión destino para guardar datos de referencia
        $comisionDestino = \App\Models\Comision::find($request->comision_destino_id);

        // Crear la solicitud
        $solicitud = $inscripcion->solicitudesCambio()->create([
            'tipo' => 'comision',
            'motivo' => $request->motivo,
            'comision_origen_id' => $comisionActual->id,
            'comision_destino_id' => $request->comision_destino_id,
            'modalidad_origen' => $comisionActual->modalidad,
            'modalidad_destino' => $comisionDestino?->modalidad,
            'turno_origen' => $comisionActual->turno,
            'turno_destino' => $comisionDestino?->turno,
            'estado' => \App\Models\SolicitudCambio::ESTADO_PENDIENTE,
        ]);

        // Detectar posible trueque automáticamente
        $mensaje = 'Solicitud de cambio de comisión creada correctamente.';
        $trueque = \App\Models\SolicitudCambio::detectarTrueque($solicitud);
        if ($trueque) {
            $solicitud->update([
                'estado' => \App\Models\SolicitudCambio::ESTADO_TRUEQUE_DETECTADO,
                'solicitud_trueque_id' => $trueque->id,
            ]);
            $trueque->update([
                'estado' => \App\Models\SolicitudCambio::ESTADO_TRUEQUE_DETECTADO,
                'solicitud_trueque_id' => $solicitud->id,
            ]);
            $mensaje = '¡Trueque detectado! Se encontró una solicitud inversa compatible. Ambas solicitudes requieren aprobación.';
        }

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', $mensaje);
    }

    /**
     * Mostrar listado de estudiantes inactivos
     */
    public function inactivos(Request $request): View
    {
        $diasLimite = $request->input('dias', \App\Services\ConfiguracionService::get('dias_inactividad', 30));
        $fechaLimite = now()->subDays($diasLimite);

        // Obtener inscripciones cursando con comisión activa
        $inscripcionesCursando = Inscripcion::where('estado_ingreso', Inscripcion::INGRESO_CURSANDO)
            ->whereHas('inscripcionesComision', function ($q) {
                $q->whereIn('estado', ['inscripto', 'confirmado']);
            })
            ->with(['inscripcionesComision' => function ($q) {
                $q->whereIn('estado', ['inscripto', 'confirmado'])->with('comision');
            }])
            ->get();

        $inactivos = collect();

        foreach ($inscripcionesCursando as $inscripcion) {
            $inscripcionComision = $inscripcion->inscripcionesComision->first();
            if (!$inscripcionComision) {
                continue;
            }

            // Buscar última actividad: asistencia o nota
            $ultimaAsistencia = DB::table('asistencias')
                ->where('inscripcion_id', $inscripcion->id)
                ->max('fecha');

            $ultimaNota = DB::table('notas')
                ->where('inscripcion_id', $inscripcion->id)
                ->max('updated_at');

            $ultimaActividad = collect([$ultimaAsistencia, $ultimaNota])
                ->filter()
                ->max();

            // Si no tiene ninguna actividad, usar la fecha de inscripción a comisión
            if (!$ultimaActividad) {
                $ultimaActividad = $inscripcionComision->fecha_inscripcion
                    ?? $inscripcionComision->created_at;
            }

            if ($ultimaActividad && $ultimaActividad < $fechaLimite) {
                $person = $inscripcion->getPerson();
                $inactivos->push([
                    'inscripcion' => $inscripcion,
                    'persona' => $person,
                    'comision' => $inscripcionComision->comision,
                    'ultima_actividad' => \Carbon\Carbon::parse($ultimaActividad),
                    'dias_sin_actividad' => now()->diffInDays($ultimaActividad),
                ]);
            }
        }

        // Ordenar por más días inactivos primero
        $inactivos = $inactivos->sortByDesc('dias_sin_actividad')->values();

        return view('inscripciones.inactivos', compact('inactivos', 'diasLimite'));
    }

    /**
     * Dar de baja estudiantes inactivos seleccionados
     */
    public function bajaInactivos(Request $request): RedirectResponse
    {
        $request->validate([
            'inscripcion_ids' => 'required|array|min:1',
            'inscripcion_ids.*' => 'integer|exists:inscripciones,id',
            'motivo' => 'required|string|min:5',
        ]);

        $count = 0;
        foreach ($request->inscripcion_ids as $id) {
            $inscripcion = Inscripcion::find($id);
            if ($inscripcion && $inscripcion->estado_ingreso === Inscripcion::INGRESO_CURSANDO) {
                $inscripcion->update(['estado_ingreso' => Inscripcion::INGRESO_LIBRE]);

                \App\Models\Trayectoria::registrarEvento(
                    $inscripcion->id,
                    \App\Models\Trayectoria::ESTADO_LIBRE,
                    $request->motivo,
                    false,
                    auth()->id()
                );
                $count++;
            }
        }

        return redirect()
            ->route('inscripciones.inactivos')
            ->with('success', "Se marcaron {$count} estudiantes como 'libre' por inactividad.");
    }

    /**
     * RF15: Aprobar trayectoria de forma excepcional (sin completar todos los espacios)
     */
    public function aprobarExcepcional(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $request->validate([
            'motivo' => 'required|string|min:10',
        ]);

        // No se puede aprobar si ya está aprobado
        if ($inscripcion->estado_ingreso === Inscripcion::INGRESO_APROBADO) {
            return redirect()
                ->route('inscripciones.show', $inscripcion)
                ->with('info', 'La cursada ya fue aprobada anteriormente.');
        }

        // Obtener inscripción a comisión activa
        $inscripcionComision = $inscripcion->inscripcionesComision()
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->first();

        // Actualizar estado de la inscripción
        $inscripcion->update([
            'estado_ingreso' => Inscripcion::INGRESO_APROBADO,
            'estado' => Inscripcion::ESTADO_CONFIRMADO,
        ]);

        // Actualizar inscripción a comisión
        if ($inscripcionComision) {
            $inscripcionComision->update(['estado' => 'aprobado']);
        }

        // Actualizar cursada si existe
        $cursada = \App\Models\Cursada::where('inscripcion_id', $inscripcion->id)
            ->whereIn('estado', [\App\Models\Cursada::ESTADO_CURSANDO])
            ->first();

        if ($cursada) {
            $cursada->cambiarEstado(
                \App\Models\Cursada::ESTADO_APROBADO,
                auth()->id(),
                'Aprobación excepcional: ' . $request->motivo
            );
        }

        // Registrar en trayectoria
        \App\Models\Trayectoria::registrarEvento(
            $inscripcion->id,
            \App\Models\Trayectoria::ESTADO_APROBADO,
            'Aprobación excepcional: ' . $request->motivo,
            null,
            auth()->id()
        );

        return redirect()
            ->route('inscripciones.show', $inscripcion)
            ->with('success', 'Trayectoria aprobada de forma excepcional. Motivo registrado en el historial.');
    }
}
