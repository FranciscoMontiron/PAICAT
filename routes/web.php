<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\ComisionController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AsignacionAlumnosController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\AulaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rutas web de la aplicación PAICAT
|
*/

// Redirigir raíz al login si no está autenticado, al home si está autenticado
Route::get('/', function () {
    return auth()->check() ? redirect()->route('home') : redirect()->route('login');
});

// Rutas de autenticación (públicas)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {

    // Ruta principal - Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Ruta de desarrollador (solo en modo debug)
    Route::get('/developer', [App\Http\Controllers\DeveloperController::class, 'index'])->name('developer');

    // Módulo 1: Inscripciones
    Route::prefix('inscripciones')->name('inscripciones.')->middleware('permission:inscripciones.ver')->group(function () {
        Route::get('/', [InscripcionController::class, 'index'])->name('index');
        Route::get('/create', [InscripcionController::class, 'create'])->middleware('permission:inscripciones.crear')->name('create');
        Route::post('/', [InscripcionController::class, 'store'])->middleware('permission:inscripciones.crear')->name('store');
        Route::get('/buscar-aspirante', [InscripcionController::class, 'buscarAspirante'])->name('buscar-aspirante');
        Route::get('/importar', [InscripcionController::class, 'showImportar'])->middleware('permission:inscripciones.crear')->name('importar.show');
        Route::post('/importar', [InscripcionController::class, 'importar'])->middleware('permission:inscripciones.crear')->name('importar');
        Route::get('/exportar', [InscripcionController::class, 'exportar'])->name('exportar');
        Route::get('/{inscripcion}', [InscripcionController::class, 'show'])->name('show');
        Route::get('/{inscripcion}/edit', [InscripcionController::class, 'edit'])->middleware('permission:inscripciones.editar')->name('edit');
        Route::put('/{inscripcion}', [InscripcionController::class, 'update'])->middleware('permission:inscripciones.editar')->name('update');
        Route::post('/{inscripcion}/validar-documentacion', [InscripcionController::class, 'validarDocumentacion'])->middleware('permission:inscripciones.editar')->name('validar-documentacion');
        Route::post('/{inscripcion}/confirmar', [InscripcionController::class, 'confirmar'])->middleware('permission:inscripciones.editar')->name('confirmar');
        Route::post('/{inscripcion}/cancelar', [InscripcionController::class, 'cancelar'])->middleware('permission:inscripciones.editar')->name('cancelar');
        Route::post('/{inscripcion}/aprobar-cursada', [InscripcionController::class, 'aprobarCursada'])->middleware('permission:inscripciones.editar')->name('aprobar-cursada');
        Route::post('/{inscripcion}/agregar-condicion', [InscripcionController::class, 'agregarCondicion'])->middleware('permission:inscripciones.editar')->name('agregar-condicion');
        Route::delete('/condicion/{condicion}', [InscripcionController::class, 'desactivarCondicion'])->middleware('permission:inscripciones.editar')->name('desactivar-condicion');
        Route::post('/{inscripcion}/crear-solicitud', [InscripcionController::class, 'crearSolicitud'])->middleware('permission:inscripciones.editar')->name('crear-solicitud');
        Route::delete('/{inscripcion}', [InscripcionController::class, 'destroy'])->middleware('permission:inscripciones.eliminar')->name('destroy');
    });

    // Módulo 2: Comisiones
    Route::prefix('comisiones')->name('comisiones.')->middleware('permission:comisiones.ver')->group(function () {
        Route::get('/', [ComisionController::class, 'index'])->name('index');
        Route::get('/create', [ComisionController::class, 'create'])->middleware('permission:comisiones.crear')->name('create');
        Route::post('/', [ComisionController::class, 'store'])->middleware('permission:comisiones.crear')->name('store');
        Route::get('/{comision}', [ComisionController::class, 'show'])->name('show');
        Route::get('/{comision}/edit', [ComisionController::class, 'edit'])->middleware('permission:comisiones.editar')->name('edit');
        Route::put('/{comision}', [ComisionController::class, 'update'])->middleware('permission:comisiones.editar')->name('update');
        Route::delete('/{comision}', [ComisionController::class, 'destroy'])->middleware('permission:comisiones.eliminar')->name('destroy');
        Route::post('/{comision}/estado', [ComisionController::class, 'cambiarEstado'])->middleware('permission:comisiones.editar')->name('cambiarEstado');
        Route::post('/{comision}/docente', [ComisionController::class, 'asignarDocente'])->middleware('permission:comisiones.editar')->name('asignarDocente');
        // Gestión de alumnos en comisiones
        Route::get('/{comision}/alumnos-disponibles', [ComisionController::class, 'alumnosDisponibles'])->middleware('permission:comisiones.editar')->name('alumnosDisponibles');
        Route::post('/{comision}/inscribir-alumno', [ComisionController::class, 'inscribirAlumno'])->middleware('permission:comisiones.editar')->name('inscribirAlumno');
        Route::delete('/{comision}/inscripcion/{inscripcion}', [ComisionController::class, 'desinscribirAlumno'])->middleware('permission:comisiones.editar')->name('desinscribirAlumno');
    });

    // Módulo 2.5: Asignación Aleatoria de Alumnos (RF10)
    Route::prefix('asignacion-alumnos')->name('asignacion-alumnos.')->middleware('permission:comisiones.editar')->group(function () {
        Route::get('/', [AsignacionAlumnosController::class, 'index'])->name('index');
        Route::post('/preview', [AsignacionAlumnosController::class, 'preview'])->name('preview');
        Route::post('/ejecutar', [AsignacionAlumnosController::class, 'ejecutar'])->name('ejecutar');
    });

    // Módulo 3: Asistencias
    Route::prefix('asistencias')->name('asistencias.')->middleware('permission:asistencias.ver')->group(function () {

        // Pantalla principal: listado de comisiones
        Route::get('/', [AsistenciaController::class, 'index'])->name('index');

        // Buscador global de alumnos
        Route::get('/buscar-alumno', [AsistenciaController::class, 'buscarAlumno'])
            ->middleware('permission:asistencias.editar')
            ->name('buscar-alumno');

        // Alertas de alumnos en riesgo
        Route::get('/alertas', [AsistenciaController::class, 'alertas'])->name('alertas');

        // Ver materias de una comisión
        Route::get('/comision/{comision}/materias', [AsistenciaController::class, 'comisionMaterias'])
            ->name('comision.materias');

        // Historial de asistencias por materia
        Route::get('/comision/{comision}/materia/{materia}/historial', [AsistenciaController::class, 'materiaHistorial'])
            ->name('materia.historial');

        // Tomar asistencia por materia
        Route::get('/comision/{comision}/materia/{materia}/tomar', [AsistenciaController::class, 'tomarAsistencia'])
            ->middleware('permission:asistencias.crear')
            ->name('tomar');

        // Alias para tomar asistencia
        Route::get('/comision/{comision}/materia/{materia}/registrar', [AsistenciaController::class, 'tomarAsistencia'])
            ->middleware('permission:asistencias.crear')
            ->name('materia.registrar');

        // Guardar asistencia por materia
        Route::post('/comision/{comision}/materia/{materia}/guardar', [AsistenciaController::class, 'guardarAsistencia'])
            ->middleware('permission:asistencias.crear')
            ->name('guardar');

        // Seleccionar alumno para justificar inasistencias por materia
        Route::get('/comision/{comision}/materia/{materia}/justificar-alumno', [AsistenciaController::class, 'seleccionarAlumnoPorMateria'])
            ->middleware('permission:asistencias.editar')
            ->name('materia.seleccionar-alumno');

        // Editar asistencia individual
        Route::get('/asistencia/{asistencia}/editar', [AsistenciaController::class, 'editarAsistencia'])
            ->middleware('permission:asistencias.editar')
            ->name('editar');

        // Actualizar asistencia individual
        Route::put('/asistencia/{asistencia}/actualizar', [AsistenciaController::class, 'actualizarAsistencia'])
            ->middleware('permission:asistencias.editar')
            ->name('actualizar');

        // Editar asistencia por materia
        Route::get('/comision/{comision}/materia/{materia}/editar', [AsistenciaController::class, 'materiaEditar'])
            ->middleware('permission:asistencias.editar')
            ->name('materia.editar');

        // Actualizar asistencia por materia
        Route::put('/comision/{comision}/materia/{materia}/actualizar', [AsistenciaController::class, 'materiaActualizar'])
            ->middleware('permission:asistencias.editar')
            ->name('materia.actualizar');

        // Listado de asistencia por materia
        Route::get('/por-materia', [AsistenciaController::class, 'porMateria'])->name('por-materia');

        // Seleccionar materia antes de pasar asistencia
        Route::get('/{comision}/seleccionar-materia', [AsistenciaController::class, 'seleccionarMateria'])
            ->middleware('permission:asistencias.crear')
            ->name('seleccionar-materia');

        // Pasar asistencia (crear/modificar del día)
        Route::get('/{comision}/pasar-asistencia', [AsistenciaController::class, 'create'])
            ->middleware('permission:asistencias.crear')
            ->name('create');

        Route::post('/{comision}/pasar-asistencia', [AsistenciaController::class, 'store'])
            ->middleware('permission:asistencias.crear')
            ->name('store');

        // Historial de asistencias de la comisión
        Route::get('/{comision}/historial', [AsistenciaController::class, 'historial'])
            ->name('historial');

        // Editar asistencia de un día específico
        Route::get('/{comision}/editar/{fecha}', [AsistenciaController::class, 'edit'])
            ->middleware('permission:asistencias.editar')
            ->name('edit');

        Route::put('/{comision}/editar/{fecha}', [AsistenciaController::class, 'update'])
            ->middleware('permission:asistencias.editar')
            ->name('update');

        // Justificar inasistencias - seleccionar alumno de la comisión
        Route::get('/{comision}/justificar-inasistencias', [AsistenciaController::class, 'seleccionarAlumno'])
            ->middleware('permission:asistencias.editar')
            ->name('seleccionar-alumno');

        // Historial individual de alumno
        Route::get('/{comision}/alumno/{inscripcion}', [AsistenciaController::class, 'alumnoHistorial'])
            ->name('alumno.historial');

        // Formulario de justificación para un alumno específico
        Route::get('/{comision}/alumno/{inscripcion}/justificar', [AsistenciaController::class, 'justificarForm'])
            ->middleware('permission:asistencias.editar')
            ->name('alumno.justificar');

        // Alias para justificar (usado en buscador)
        Route::get('/{comision}/justificar/{inscripcion}', [AsistenciaController::class, 'justificarForm'])
            ->middleware('permission:asistencias.editar')
            ->name('justificar');

        Route::post('/{comision}/alumno/{inscripcion}/justificar', [AsistenciaController::class, 'justificarStore'])
            ->middleware('permission:asistencias.editar')
            ->name('alumno.justificar.store');
    });


    // Módulo 4: Evaluaciones
    Route::prefix('evaluaciones')->name('evaluaciones.')->middleware('permission:evaluaciones.ver')->group(function () {
        Route::get('/', [EvaluacionController::class, 'index'])->name('index');
        Route::get('/create', [EvaluacionController::class, 'create'])->middleware('permission:evaluaciones.crear')->name('create');
        Route::post('/', [EvaluacionController::class, 'store'])->middleware('permission:evaluaciones.crear')->name('store');
        Route::get('/{evaluacion}/edit', [EvaluacionController::class, 'edit'])->middleware('permission:evaluaciones.editar')->name('edit');
        Route::put('/{evaluacion}', [EvaluacionController::class, 'update'])->middleware('permission:evaluaciones.editar')->name('update');
        Route::delete('/{evaluacion}', [EvaluacionController::class, 'destroy'])->middleware('permission:evaluaciones.eliminar')->name('destroy');

        // Vista de evaluaciones por comisión
        Route::get('/comision/{comision}', [EvaluacionController::class, 'showComision'])->name('comision');

        // NOTAS - Gestión de notas por comisión
        Route::get('/notas/{comision}', [EvaluacionController::class, 'indexNota'])->name('notas.index');
        Route::get('/notas/{comision}/create', [EvaluacionController::class, 'createNota'])->middleware('permission:evaluaciones.crear')->name('notas.create');
        Route::post('/notas/{comision}', [EvaluacionController::class, 'storeNota'])->middleware('permission:evaluaciones.crear')->name('notas.store');
        Route::post('/notasMatrix/{comision}', [EvaluacionController::class, 'storeMatrix'])->middleware('permission:evaluaciones.crear')->name('notas.store-matrix');
        Route::get('/notas/{comision}/{nota}/edit', [EvaluacionController::class, 'editNota'])->middleware('permission:evaluaciones.editar')->name('notas.edit');
        Route::put('/notas/{comision}/{nota}', [EvaluacionController::class, 'updateNota'])->middleware('permission:evaluaciones.editar')->name('notas.update');
        Route::delete('/notas/{comision}/{nota}', [EvaluacionController::class, 'destroyNota'])->middleware('permission:evaluaciones.eliminar')->name('notas.destroy');

        // NOTAS - Historial por alumno
        Route::get('/notas/{comision}/alumno/{inscripcion}', [EvaluacionController::class, 'historialAlumno'])->name('notas.historial-alumno');

        // NOTAS - Recuperatorio
        Route::get('/notas/{comision}/recuperatorio', [EvaluacionController::class, 'createRecuperatorio'])->middleware('permission:evaluaciones.crear')->name('notas.recuperatorio.create');
        Route::post('/notas/{comision}/recuperatorio', [EvaluacionController::class, 'storeRecuperatorio'])->middleware('permission:evaluaciones.crear')->name('notas.recuperatorio.store');

        // NOTAS - Exportar acta
        Route::get('/notas/{comision}/exportar-acta', [EvaluacionController::class, 'exportarActa'])->name('notas.exportar-acta');

        // AJAX - Obtener materias
        Route::get('/getMaterias/{comision}', [EvaluacionController::class, 'getMaterias'])->name('getMaterias');
        // Carga Masiva
        Route::get('/{evaluacion}/carga-masiva', [EvaluacionController::class, 'cargaMasiva'])->name('carga-masiva');
        Route::post('/{evaluacion}/carga-masiva', [EvaluacionController::class, 'storeCargaMasiva'])->name('store-carga-masiva');
    });

    // Módulo 5: Reportes
    Route::prefix('reportes')->name('reportes.')->middleware('permission:reportes.ver')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
    });

    // Módulo: Materias (ABM)
    Route::prefix('materias')->name('materias.')->middleware('permission:comisiones.ver')->group(function () {
        Route::get('/', [App\Http\Controllers\MateriaController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\MateriaController::class, 'create'])->middleware('permission:comisiones.crear')->name('create');
        Route::post('/', [App\Http\Controllers\MateriaController::class, 'store'])->middleware('permission:comisiones.crear')->name('store');
        Route::get('/{materia}', [App\Http\Controllers\MateriaController::class, 'show'])->name('show');
        Route::get('/{materia}/edit', [App\Http\Controllers\MateriaController::class, 'edit'])->middleware('permission:comisiones.editar')->name('edit');
        Route::put('/{materia}', [App\Http\Controllers\MateriaController::class, 'update'])->middleware('permission:comisiones.editar')->name('update');
        Route::delete('/{materia}', [App\Http\Controllers\MateriaController::class, 'destroy'])->middleware('permission:comisiones.eliminar')->name('destroy');
    });

    // Módulo: Municipios (ABM)
    Route::prefix('municipios')->name('municipios.')->middleware('permission:comisiones.ver')->group(function () {
        Route::get('/', [MunicipioController::class, 'index'])->name('index');
        Route::get('/create', [MunicipioController::class, 'create'])->middleware('permission:comisiones.crear')->name('create');
        Route::post('/', [MunicipioController::class, 'store'])->middleware('permission:comisiones.crear')->name('store');
        Route::get('/{municipio}', [MunicipioController::class, 'show'])->name('show');
        Route::get('/{municipio}/edit', [MunicipioController::class, 'edit'])->middleware('permission:comisiones.editar')->name('edit');
        Route::put('/{municipio}', [MunicipioController::class, 'update'])->middleware('permission:comisiones.editar')->name('update');
        Route::patch('/{municipio}/toggle-activo', [MunicipioController::class, 'toggleActivo'])->middleware('permission:comisiones.editar')->name('toggle-activo');
        Route::delete('/{municipio}', [MunicipioController::class, 'destroy'])->middleware('permission:comisiones.eliminar')->name('destroy');
    });

    // Módulo: Aulas (ABM)
    Route::prefix('aulas')->name('aulas.')->middleware('permission:comisiones.ver')->group(function () {
        Route::get('/', [AulaController::class, 'index'])->name('index');
        Route::get('/create', [AulaController::class, 'create'])->middleware('permission:comisiones.crear')->name('create');
        Route::post('/', [AulaController::class, 'store'])->middleware('permission:comisiones.crear')->name('store');
        Route::get('/{aula}', [AulaController::class, 'show'])->name('show');
        Route::get('/{aula}/edit', [AulaController::class, 'edit'])->middleware('permission:comisiones.editar')->name('edit');
        Route::put('/{aula}', [AulaController::class, 'update'])->middleware('permission:comisiones.editar')->name('update');
        Route::patch('/{aula}/toggle-activa', [AulaController::class, 'toggleActiva'])->middleware('permission:comisiones.editar')->name('toggle-activa');
        Route::delete('/{aula}', [AulaController::class, 'destroy'])->middleware('permission:comisiones.eliminar')->name('destroy');
        // API: Obtener aulas por municipio (para AJAX)
        Route::get('/por-municipio/{municipio}', [AulaController::class, 'porMunicipio'])->name('por-municipio');
    });

    // Módulo: Cursadas (gestión de cursadas por comisión)
    Route::prefix('cursadas')->name('cursadas.')->middleware('permission:comisiones.ver')->group(function () {
        // Vista global de todas las cursadas
        Route::get('/', [App\Http\Controllers\CursadaController::class, 'indexGlobal'])->name('global');

        // Rutas anidadas por comisión
        Route::get('/comision/{comision}', [App\Http\Controllers\CursadaController::class, 'index'])->name('index');
        Route::get('/comision/{comision}/{cursada}', [App\Http\Controllers\CursadaController::class, 'show'])->name('show');
        Route::get('/comision/{comision}/{cursada}/edit', [App\Http\Controllers\CursadaController::class, 'edit'])->middleware('permission:comisiones.editar')->name('edit');
        Route::put('/comision/{comision}/{cursada}', [App\Http\Controllers\CursadaController::class, 'update'])->middleware('permission:comisiones.editar')->name('update');
        Route::post('/comision/{comision}/{cursada}/cambiar-estado', [App\Http\Controllers\CursadaController::class, 'cambiarEstado'])->middleware('permission:comisiones.editar')->name('cambiar-estado');
        Route::post('/comision/{comision}/{cursada}/recalcular-nota', [App\Http\Controllers\CursadaController::class, 'recalcularNota'])->middleware('permission:comisiones.editar')->name('recalcular-nota');
        Route::post('/comision/{comision}/sincronizar', [App\Http\Controllers\CursadaController::class, 'sincronizarConComision'])->middleware('permission:comisiones.editar')->name('sincronizar');
    });

    // Módulo 6: Usuarios
    Route::prefix('usuarios')->name('usuarios.')->middleware('permission:usuarios.ver')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::get('/create', [UsuarioController::class, 'create'])->middleware('permission:usuarios.crear')->name('create');
        Route::post('/', [UsuarioController::class, 'store'])->middleware('permission:usuarios.crear')->name('store');
        Route::get('/{usuario}', [UsuarioController::class, 'show'])->name('show');
        Route::get('/{usuario}/edit', [UsuarioController::class, 'edit'])->middleware('permission:usuarios.editar')->name('edit');
        Route::put('/{usuario}', [UsuarioController::class, 'update'])->middleware('permission:usuarios.editar')->name('update');
        Route::delete('/{usuario}', [UsuarioController::class, 'destroy'])->middleware('permission:usuarios.eliminar')->name('destroy');
        Route::post('/{id}/restore', [UsuarioController::class, 'restore'])->middleware('permission:usuarios.eliminar')->name('restore');
    });

    // Módulo: Solicitudes de Cambio
    Route::prefix('solicitudes')->name('solicitudes.')->middleware('permission:comisiones.editar')->group(function () {
        Route::get('/', [App\Http\Controllers\SolicitudCambioController::class, 'index'])->name('index');
        Route::get('/{solicitud}', [App\Http\Controllers\SolicitudCambioController::class, 'show'])->name('show');
        Route::post('/{solicitud}/aprobar', [App\Http\Controllers\SolicitudCambioController::class, 'aprobar'])->name('aprobar');
        Route::post('/{solicitud}/rechazar', [App\Http\Controllers\SolicitudCambioController::class, 'rechazar'])->name('rechazar');
        Route::post('/detectar-trueques', [App\Http\Controllers\SolicitudCambioController::class, 'detectarTrueques'])->name('detectar-trueques');
        Route::get('/comision/{comision}/cupos', [App\Http\Controllers\SolicitudCambioController::class, 'verificarCupos'])->name('verificar-cupos');
    });
}); // Cierre del middleware auth