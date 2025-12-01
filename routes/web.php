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

    // Módulo 1: Inscripciones (todos los roles pueden ver)
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

    // Módulo 2: Comisiones (Admin, Coordinador, Docente)
    Route::prefix('comisiones')->name('comisiones.')->middleware('permission:comisiones.ver')->group(function () {
        Route::get('/', [ComisionController::class, 'index'])->name('index');
        // TODO: Agregar rutas CRUD cuando se implementen con sus respectivos permisos
    });

    // Módulo 3: Asistencias (todos los roles)
    Route::prefix('asistencias')->name('asistencias.')->middleware('permission:asistencias.ver')->group(function () {
        Route::get('/', [AsistenciaController::class, 'index'])->name('index');
        // TODO: Agregar rutas CRUD cuando se implementen con sus respectivos permisos
    });

    // Módulo 4: Evaluaciones y Notas (todos los roles)
    Route::prefix('evaluaciones')->name('evaluaciones.')->middleware('permission:evaluaciones.ver')->group(function () {
        Route::get('/', [EvaluacionController::class, 'index'])->name('index');
        // TODO: Agregar rutas CRUD cuando se implementen con sus respectivos permisos
    });

    // Módulo 5: Reportes y Estadísticas (Admin, Coordinador, Docente)
    Route::prefix('reportes')->name('reportes.')->middleware('permission:reportes.ver')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        // TODO: Agregar rutas para generación de reportes con permission:reportes.generar
    });

    // Módulo 6: Usuarios y Permisos (Admin y Coordinador)
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
