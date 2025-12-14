<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\ComisionController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Auth\AuthController;

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
        
        Route::post('/{comision}/alumno/{inscripcion}/justificar', [AsistenciaController::class, 'justificarStore'])
            ->middleware('permission:asistencias.editar')
            ->name('alumno.justificar.store');
    });

    // Módulo 4: Evaluaciones
    Route::prefix('evaluaciones')->name('evaluaciones.')->middleware('permission:evaluaciones.ver')->group(function () {
        Route::get('/', [EvaluacionController::class, 'index'])->name('index');
    });

    // Módulo 5: Reportes
    Route::prefix('reportes')->name('reportes.')->middleware('permission:reportes.ver')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
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

}); // Cierre del middleware auth
