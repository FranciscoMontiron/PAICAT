@extends('layouts.app')

@section('title', 'Detalle de Inscripción')

@section('content')
@php
    $notaAprobacion = \App\Services\ConfiguracionService::get('nota_aprobacion', 6);
    $asistenciaMinima = \App\Services\ConfiguracionService::get('asistencia_minima', 75);
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
        <div class="flex">
            <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if(session('info'))
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
        <div class="flex">
            <svg class="h-5 w-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-utn-blue-dark">{{ session('info') }}</p>
        </div>
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- HEADER: Nombre del alumno + estados + acciones               --}}
    {{-- ============================================================ --}}
    <div class="bg-white shadow-md rounded-xl p-6 mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            {{-- Info del alumno --}}
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-utn-blue flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                    {{ $persona ? strtoupper(substr($persona->nombre, 0, 1) . substr($persona->apellido, 0, 1)) : '?' }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $persona ? "{$persona->apellido}, {$persona->nombre}" : 'Alumno no encontrado' }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 mt-1">
                        @if($persona)
                        <span class="text-sm text-gray-500">DNI {{ $persona->documento }}</span>
                        <span class="text-gray-300">|</span>
                        @endif
                        <span class="text-sm text-gray-500">Inscripci&oacute;n #{{ $inscripcion->id }}</span>
                        <span class="text-gray-300">|</span>
                        <span class="text-sm text-gray-500">A&ntilde;o {{ $inscripcion->anio_ingreso }}</span>
                    </div>
                </div>
            </div>

            {{-- Badges de estado + acciones --}}
            <div class="flex flex-wrap items-center gap-3">
                {{-- Badge estado inscripción --}}
                <span class="px-3 py-1.5 text-sm font-medium rounded-full
                    @if($inscripcion->estado === 'pendiente') bg-yellow-100 text-yellow-800
                    @elseif($inscripcion->estado === 'documentacion_ok') bg-utn-blue/10 text-utn-blue-dark
                    @elseif($inscripcion->estado === 'confirmado') bg-green-100 text-green-800
                    @elseif($inscripcion->estado === 'cancelado') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ \App\Models\Inscripcion::getEstados()[$inscripcion->estado] ?? $inscripcion->estado }}
                </span>

                {{-- Badge estado ingreso --}}
                @if($inscripcion->estado_ingreso)
                <span class="px-3 py-1.5 text-sm font-medium rounded-full
                    @switch($inscripcion->estado_ingreso)
                        @case('inscripto') bg-utn-blue/10 text-utn-blue-dark @break
                        @case('cursando') bg-utn-blue/10 text-utn-dark @break
                        @case('aprobado') bg-green-100 text-green-800 @break
                        @case('desaprobado') bg-red-100 text-red-800 @break
                        @case('libre') bg-orange-100 text-orange-800 @break
                        @case('baja') bg-gray-100 text-gray-800 @break
                        @case('cancelado') bg-red-100 text-red-800 @break
                        @default bg-gray-100 text-gray-800
                    @endswitch">
                    {{ \App\Models\Inscripcion::getEstadosIngreso()[$inscripcion->estado_ingreso] ?? $inscripcion->estado_ingreso }}
                </span>
                @endif

                {{-- Botones --}}
                <div class="flex gap-2 ml-2">
                    @if(auth()->user()->hasPermission('inscripciones.editar') && $inscripcion->puedeModificarse())
                    <a href="{{ route('inscripciones.edit', $inscripcion) }}"
                       class="inline-flex items-center gap-1.5 bg-utn-blue-darker text-white px-4 py-2 rounded-lg hover:bg-utn-dark-light transition-colors text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </a>
                    @endif
                    <a href="{{ route('inscripciones.index') }}"
                       class="inline-flex items-center gap-1.5 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver
                    </a>
                </div>
            </div>
        </div>

        {{-- Acciones de estado --}}
        @if(auth()->user()->hasPermission('inscripciones.editar'))
        @if($inscripcion->estado === 'documentacion_ok' || $inscripcion->puedeCancelarse() || $inscripcion->estado === 'cancelado')
        <div class="flex flex-wrap gap-2 mt-5 pt-5 border-t border-gray-100">
            @if($inscripcion->estado === 'documentacion_ok')
            <form action="{{ route('inscripciones.confirmar', $inscripcion) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-800 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Confirmar Inscripci&oacute;n
                </button>
            </form>
            @endif

            @if($inscripcion->puedeCancelarse())
            <button type="button" onclick="document.getElementById('modal-cancelar').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Cancelar Inscripci&oacute;n
            </button>
            @endif

            @if($inscripcion->estado === 'cancelado')
            <button type="button" onclick="document.getElementById('modal-reactivar').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 bg-utn-blue-darker text-white px-4 py-2 rounded-lg hover:bg-utn-dark-light transition-colors text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reactivar Inscripci&oacute;n
            </button>
            @endif
        </div>
        @endif
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- GRID PRINCIPAL                                                --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ======================================================== --}}
        {{-- COLUMNA PRINCIPAL (2/3)                                   --}}
        {{-- ======================================================== --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- DATOS DEL ALUMNO --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Datos del Alumno
                    </h2>
                </div>
                <div class="p-6">
                    @if($persona)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-5">
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Nombre completo</span>
                            <p class="text-sm font-semibold text-gray-900">{{ $persona->apellido }}, {{ $persona->nombre }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">DNI</span>
                            <p class="text-sm font-semibold text-gray-900">{{ $persona->documento }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Email</span>
                            <p class="text-sm text-gray-900">{{ $persona->email ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Tel&eacute;fono</span>
                            <p class="text-sm text-gray-900">{{ $persona->telefono_celular ?? $persona->telefono_fijo ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Fecha de nacimiento</span>
                            <p class="text-sm text-gray-900">{{ $persona->nacimiento_fecha?->format('d/m/Y') ?? 'No registrada' }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Estado sistema alumnos</span>
                            <span class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $persona->__estado === 'Verificado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $persona->__estado }}
                            </span>
                        </div>
                    </div>

                    @if($persona->secundariaDato)
                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Datos del Secundario</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-4">
                            <div>
                                <span class="block text-xs text-gray-400 mb-0.5">T&iacute;tulo</span>
                                <p class="text-sm text-gray-900">{{ $persona->secundariaDato->titulo ?? 'No registrado' }}</p>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 mb-0.5">Instituci&oacute;n</span>
                                <p class="text-sm text-gray-900">{{ $persona->secundariaDato->institucion ?? 'No registrada' }}</p>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 mb-0.5">A&ntilde;o egreso</span>
                                <p class="text-sm text-gray-900">{{ $persona->secundariaDato->anio_egreso ?? 'No registrado' }}</p>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 mb-0.5">Promedio</span>
                                <p class="text-sm text-gray-900">{{ $persona->secundariaDato->promedio ?? 'No registrado' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-6 text-gray-400">
                        <p>No se encontraron datos del alumno en el sistema.</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- DATOS DE LA INSCRIPCION --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Datos de la Inscripci&oacute;n
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-5">
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Especialidad</span>
                            <p class="text-sm font-semibold text-gray-900">{{ $especialidad?->nombre ?? 'No especificada' }}</p>
                        </div>
                        @if($especialidadAlternativa)
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Especialidad alternativa</span>
                            <p class="text-sm text-gray-900">{{ $especialidadAlternativa->nombre }}</p>
                        </div>
                        @endif
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Modalidad</span>
                            <p class="text-sm text-gray-900">{{ $inscripcion->modalidad ?? 'No especificada' }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Tipo de ingreso</span>
                            <p class="text-sm text-gray-900">{{ $inscripcion->tipo_ingreso ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Turno de ingreso</span>
                            <p class="text-sm text-gray-900">{{ $inscripcion->turno_ingreso ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Turno de carrera</span>
                            <p class="text-sm text-gray-900">{{ $inscripcion->turno_carrera ?? 'No especificado' }}</p>
                        </div>
                    </div>

                    @if($inscripcion->observaciones)
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <span class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Observaciones</span>
                        <p class="text-sm text-gray-700">{{ $inscripcion->observaciones }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- TRAYECTORIA ACADEMICA                                     --}}
            {{-- ======================================================== --}}
            @if($inscripcion->estado === 'cancelado')
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-red-50 border-b border-red-100">
                    <h2 class="text-base font-semibold text-red-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Inscripci&oacute;n Cancelada
                    </h2>
                </div>
                <div class="p-6 text-center text-gray-500">
                    <p>El alumno fue dado de baja de las comisiones asignadas.</p>
                    <p class="text-xs text-gray-400 mt-1">Consulte el Historial de Trayectoria para m&aacute;s detalles.</p>
                </div>
            </div>

            @elseif(!empty($tieneComision) && $comision)

            {{-- COMISION ASIGNADA --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
                    <h2 class="text-base font-semibold text-utn-blue-dark flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Comisi&oacute;n Asignada
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-4">
                        <div>
                            <span class="block text-xs font-medium text-utn-blue-dark uppercase tracking-wide mb-1">Nombre</span>
                            <p class="text-sm font-semibold text-gray-900">{{ $comision->nombre }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-utn-blue-dark uppercase tracking-wide mb-1">Periodo</span>
                            <p class="text-sm text-gray-900">{{ $comision->periodo ?? 'N/A' }} {{ $comision->anio }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-utn-blue-dark uppercase tracking-wide mb-1">Turno</span>
                            <p class="text-sm text-gray-900">{{ $comision->turno ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-utn-blue-dark uppercase tracking-wide mb-1">Modalidad</span>
                            <p class="text-sm text-gray-900">{{ $comision->modalidad ?? 'N/A' }}</p>
                        </div>
                        @if($comision->municipio)
                        <div>
                            <span class="block text-xs font-medium text-utn-blue-dark uppercase tracking-wide mb-1">Sede</span>
                            <p class="text-sm text-gray-900">{{ $comision->municipio->nombre }}</p>
                        </div>
                        @endif
                        @if($comision->aula)
                        <div>
                            <span class="block text-xs font-medium text-utn-blue-dark uppercase tracking-wide mb-1">Aula</span>
                            <p class="text-sm text-gray-900">{{ $comision->aula->nombre }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RENDIMIENTO ACADEMICO --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Rendimiento Acad&eacute;mico
                    </h2>
                </div>
                <div class="p-6">
                    {{-- Resumen de rendimiento --}}
                    @if(!empty($resumenNotas))
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 rounded-xl p-5 text-center">
                            <span class="text-4xl font-bold {{ ($resumenNotas['materias_aprobadas'] ?? 0) == ($resumenNotas['total_materias'] ?? 0) && ($resumenNotas['total_materias'] ?? 0) > 0 ? 'text-green-600' : 'text-gray-800' }}">
                                {{ $resumenNotas['materias_aprobadas'] ?? 0 }}/{{ $resumenNotas['total_materias'] ?? 0 }}
                            </span>
                            <p class="text-sm text-gray-500 mt-1">Materias Aprobadas</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-5 text-center">
                            <span class="text-4xl font-bold {{ ($resumenNotas['porcentaje_asistencia'] ?? 0) >= $asistenciaMinima ? 'text-green-600' : 'text-orange-600' }}">
                                {{ $resumenNotas['porcentaje_asistencia'] ?? 100 }}%
                            </span>
                            <p class="text-sm text-gray-500 mt-1">Asistencia</p>
                        </div>
                    </div>
                    @endif

                    {{-- Tabla de materias y notas --}}
                    @if(!empty($materias) && $materias->count() > 0)
                    <div class="overflow-x-auto border border-gray-200 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Materia</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Evaluaciones</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Nota Sugerida</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Nota Final</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($materias as $materia)
                                @php
                                    $infoMateria = $notasPorMateria[$materia->id] ?? null;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $materia->nombre }}</div>
                                        @if($materia->codigo)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $materia->codigo }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($infoMateria && !empty($infoMateria['notas']))
                                        <div class="flex flex-wrap justify-center gap-1.5">
                                            @foreach($infoMateria['notas'] as $notaInfo)
                                                @if($notaInfo['nota'] && $notaInfo['nota']->nota !== null)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $notaInfo['nota']->nota >= ($infoMateria['nota_aprobacion'] ?? $notaAprobacion) ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                                    {{ $notaInfo['evaluacion']->tipo_nombre ?? 'Eval' }}: {{ number_format($notaInfo['nota']->nota, 1) }}
                                                </span>
                                                @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs bg-gray-50 text-gray-400">
                                                    {{ $notaInfo['evaluacion']->tipo_nombre ?? 'Eval' }}: -
                                                </span>
                                                @endif
                                            @endforeach
                                        </div>
                                        @else
                                        <span class="text-xs text-gray-300">Sin evaluaciones</span>
                                        @endif
                                    </td>
                                    {{-- Nota Sugerida (promedio de evaluaciones) --}}
                                    <td class="px-5 py-4 text-center">
                                        @if($infoMateria && $infoMateria['nota_sugerida'] !== null)
                                        <span class="text-sm font-medium text-gray-500" title="Promedio de todas las evaluaciones">
                                            {{ number_format($infoMateria['nota_sugerida'], 1) }}
                                        </span>
                                        @else
                                        <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    {{-- Nota Final (puesta por el docente) --}}
                                    <td class="px-5 py-4 text-center">
                                        @if($infoMateria && $infoMateria['nota_final'] !== null)
                                        <span class="text-xl font-bold {{ $infoMateria['nota_final'] >= ($infoMateria['nota_aprobacion'] ?? $notaAprobacion) ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($infoMateria['nota_final'], 1) }}
                                        </span>
                                        @if(auth()->user()->hasPermission('evaluaciones.editar'))
                                        <button type="button"
                                            onclick="abrirModalNotaFinal({{ $materia->id }}, '{{ addslashes($materia->nombre) }}', {{ $infoMateria['nota_final'] }}, {{ $infoMateria['nota_sugerida'] ?? 'null' }})"
                                            class="ml-1 text-gray-400 hover:text-utn-blue-dark text-xs" title="Editar nota final">
                                            <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        @endif
                                        @else
                                            @if(auth()->user()->hasPermission('evaluaciones.editar'))
                                            <button type="button"
                                                onclick="abrirModalNotaFinal({{ $materia->id }}, '{{ addslashes($materia->nombre) }}', null, {{ $infoMateria['nota_sugerida'] ?? 'null' }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-utn-blue/10 text-utn-blue-dark hover:bg-utn-blue/20 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Cargar nota
                                            </button>
                                            @else
                                            <span class="text-gray-300">-</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($infoMateria && !empty($infoMateria['aprobada']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Aprobada
                                        </span>
                                        @elseif($infoMateria && $infoMateria['nota_final'] !== null)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Desaprobada</span>
                                        @elseif($infoMateria && !empty($infoMateria['notas']))
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">En curso</span>
                                        @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-gray-400 text-sm text-center py-6">No hay materias registradas para esta comisi&oacute;n.</p>
                    @endif

                    {{-- Botón aprobar cursada --}}
                    @if(!empty($puedeAprobarCursada) && auth()->user()->hasPermission('inscripciones.editar'))
                    <div class="mt-6 bg-green-50 border border-green-200 rounded-xl p-5">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                            <div>
                                <h4 class="text-green-800 font-semibold flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Todas las materias aprobadas
                                </h4>
                                <p class="text-green-600 text-sm mt-1">El alumno complet&oacute; todas las materias del curso de ingreso.</p>
                            </div>
                            <form action="{{ route('inscripciones.aprobar-cursada', $inscripcion) }}" method="POST" data-confirm="¿Confirma aprobar la cursada de este alumno?" data-confirm-title="Aprobar cursada" data-confirm-text="Aprobar">
                                @csrf
                                <button type="submit" class="bg-green-700 text-white px-6 py-2.5 rounded-lg hover:bg-green-800 transition-colors font-medium whitespace-nowrap">
                                    Aprobar Cursada
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    {{-- Botón aprobación excepcional (RF15) --}}
                    @if(auth()->user()->hasPermission('inscripciones.editar') && $inscripcion->estado_ingreso !== \App\Models\Inscripcion::INGRESO_APROBADO && !empty($tieneComision))
                    <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl p-5">
                        <div class="flex flex-col md:flex-row items-start justify-between gap-4">
                            <div>
                                <h4 class="text-amber-800 font-semibold flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    Aprobaci&oacute;n Excepcional
                                </h4>
                                <p class="text-amber-600 text-sm mt-1">
                                    Permite aprobar la trayectoria sin que el alumno haya rendido todos los espacios curriculares.
                                </p>
                            </div>
                            <button type="button" onclick="document.getElementById('modalAprobarExcepcional').classList.remove('hidden')"
                                class="bg-amber-600 text-white px-5 py-2.5 rounded-lg hover:bg-amber-700 transition-colors text-sm font-medium whitespace-nowrap">
                                Aprobar Excepcionalmente
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @else
            {{-- NO TIENE COMISION ASIGNADA --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-base font-semibold text-gray-800">Trayectoria Acad&eacute;mica</h2>
                </div>
                <div class="p-10 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Sin comisi&oacute;n asignada</h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">El alumno a&uacute;n no ha sido asignado a ninguna comisi&oacute;n del curso de ingreso.</p>
                    @if(auth()->user()->hasPermission('comisiones.editar'))
                    <div class="mt-6">
                        <a href="{{ route('asignacion-alumnos.index') }}" class="inline-flex items-center px-5 py-2.5 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors text-sm font-medium">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Asignar a comisi&oacute;n
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ======================================================== --}}
            {{-- HISTORIAL DE TRAYECTORIA (columna principal)              --}}
            {{-- ======================================================== --}}
            @if($inscripcion->trayectorias && $inscripcion->trayectorias->count() > 0)
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Historial de Trayectoria
                        <span class="text-xs font-normal text-gray-400 ml-1">({{ $inscripcion->trayectorias->count() }} evento{{ $inscripcion->trayectorias->count() > 1 ? 's' : '' }})</span>
                    </h2>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-gray-200"></div>
                        <div class="space-y-5">
                            @foreach($inscripcion->trayectorias as $trayectoria)
                            @php
                            $colors = [
                                'activo' => 'bg-green-500',
                                'pausado' => 'bg-yellow-500',
                                'libre' => 'bg-orange-500',
                                'baja' => 'bg-red-500',
                                'reincorporado' => 'bg-blue-500',
                                'aprobado' => 'bg-emerald-500',
                                'desaprobado' => 'bg-red-500',
                                'cancelado' => 'bg-red-600',
                            ];

                            $motivo = $trayectoria->motivo ?? '';
                            $esCambioComision = str_contains($motivo, 'Cambio de comisi');
                            $esCambioModalidad = str_contains($motivo, 'Cambio de modalidad');
                            $esCambioTurno = str_contains($motivo, 'Cambio de turno');
                            $esAprobacionExcepcional = str_contains($motivo, 'excepcional');
                            $esAsignacion = str_contains($motivo, 'Asignado a comisi');
                            $esBajaInactividad = str_contains($motivo, 'Baja por inactividad');
                            $esDocValidada = str_contains($motivo, 'Documentaci') && str_contains($motivo, 'validada');
                            $esDocActualizada = str_contains($motivo, 'Documentaci') && str_contains($motivo, 'actualizada');
                            $esConfirmacion = str_contains($motivo, 'confirmada con documentaci');

                            $badgeColor = '';
                            $etiquetaEvento = '';
                            if ($esCambioComision) {
                                $etiquetaEvento = 'Cambio de Comisi&oacute;n';
                                $badgeColor = 'bg-utn-blue/10 text-utn-blue-dark';
                            } elseif ($esCambioModalidad) {
                                $etiquetaEvento = 'Cambio de Modalidad';
                                $badgeColor = 'bg-purple-100 text-purple-700';
                            } elseif ($esCambioTurno) {
                                $etiquetaEvento = 'Cambio de Turno';
                                $badgeColor = 'bg-cyan-100 text-cyan-700';
                            } elseif ($esAprobacionExcepcional) {
                                $etiquetaEvento = 'Aprobaci&oacute;n Excepcional';
                                $badgeColor = 'bg-amber-100 text-amber-700';
                            } elseif ($esDocValidada) {
                                $etiquetaEvento = 'Documentaci&oacute;n Validada';
                                $badgeColor = 'bg-teal-100 text-teal-700';
                            } elseif ($esDocActualizada) {
                                $etiquetaEvento = 'Documentaci&oacute;n Parcial';
                                $badgeColor = 'bg-yellow-100 text-yellow-700';
                            } elseif ($esConfirmacion) {
                                $etiquetaEvento = 'Inscripci&oacute;n Confirmada';
                                $badgeColor = 'bg-green-100 text-green-700';
                            } elseif ($esAsignacion) {
                                $etiquetaEvento = 'Asignaci&oacute;n';
                                $badgeColor = 'bg-green-100 text-green-700';
                            } elseif ($esBajaInactividad) {
                                $etiquetaEvento = 'Baja por Inactividad';
                                $badgeColor = 'bg-red-100 text-red-700';
                            }
                            @endphp
                            <div class="relative pl-10">
                                <div class="absolute left-2 top-1.5 w-4 h-4 rounded-full {{ $colors[$trayectoria->estado] ?? 'bg-gray-400' }} border-2 border-white shadow-sm"></div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-semibold text-gray-900 capitalize text-sm">{{ \App\Models\Trayectoria::getEstados()[$trayectoria->estado] ?? $trayectoria->estado }}</span>
                                            @if($badgeColor)
                                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $badgeColor }}">{!! $etiquetaEvento !!}</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $trayectoria->fecha_inicio->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($trayectoria->motivo)
                                    <p class="text-sm text-gray-600 mt-1">{{ $trayectoria->motivo }}</p>
                                    @endif
                                    <div class="flex flex-wrap items-center gap-3 mt-2">
                                        @if($trayectoria->registradoPor)
                                        <p class="text-xs text-gray-400">Por: {{ $trayectoria->registradoPor->nombre_completo ?? 'Usuario' }}</p>
                                        @endif
                                        @if($trayectoria->fecha_fin)
                                        <p class="text-xs text-gray-400">Hasta: {{ $trayectoria->fecha_fin->format('d/m/Y') }}</p>
                                        @else
                                        <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded font-medium">Vigente</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- ======================================================== --}}
        {{-- COLUMNA LATERAL (1/3)                                     --}}
        {{-- ======================================================== --}}
        <div class="space-y-8">

            {{-- VALIDACION DE DOCUMENTACION --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Documentaci&oacute;n
                    </h2>
                </div>
                <div class="p-6">
                    @if($inscripcion->estado === 'cancelado')
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 text-red-800">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium text-sm">Inscripci&oacute;n Cancelada</span>
                        </div>
                    </div>
                    @if($inscripcion->observaciones)
                    <p class="text-sm text-gray-500 mt-3"><strong>Motivo:</strong> {{ $inscripcion->observaciones }}</p>
                    @endif

                    @elseif($inscripcion->estado_documentacion === 'confirmada')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center gap-2 text-green-800">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium text-sm">Documentaci&oacute;n Confirmada</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach(['DNI' => true, 'Título secundario' => true, 'Analítico' => true] as $doc => $ok)
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                            <span class="text-sm text-gray-700">{{ $doc }} validado</span>
                        </div>
                        @endforeach
                    </div>

                    @elseif(auth()->user()->hasPermission('inscripciones.editar'))
                    <form action="{{ route('inscripciones.validar-documentacion', $inscripcion) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            @php
                            $docs = [
                                'doc_dni_validado' => 'DNI validado',
                                'doc_titulo_validado' => 'Título secundario validado',
                                'doc_analitico_validado' => 'Analítico validado',
                            ];
                            @endphp
                            @foreach($docs as $field => $label)
                            <label class="flex items-center gap-3 p-3 rounded-lg border {{ $inscripcion->$field ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-white' }} cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="{{ $field }}" value="1" {{ $inscripcion->$field ? 'checked' : '' }}
                                       class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                            </label>
                            @endforeach

                            <div>
                                <label for="observaciones_documentacion" class="block text-xs font-medium text-gray-500 mb-1">Observaciones</label>
                                <textarea name="observaciones_documentacion" id="observaciones_documentacion" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                          placeholder="Observaciones opcionales...">{{ $inscripcion->observaciones_documentacion }}</textarea>
                            </div>

                            <button type="submit" class="w-full bg-utn-blue text-white px-4 py-2.5 rounded-lg hover:bg-utn-dark transition-colors text-sm font-medium">
                                Guardar Validaci&oacute;n
                            </button>
                        </div>
                    </form>

                    @else
                    <div class="space-y-3">
                        @php
                        $docsReadonly = [
                            'doc_dni_validado' => 'DNI',
                            'doc_titulo_validado' => 'Título',
                            'doc_analitico_validado' => 'Analítico',
                        ];
                        @endphp
                        @foreach($docsReadonly as $field => $label)
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $inscripcion->$field ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            <span class="text-sm text-gray-700">{{ $label }} {{ $inscripcion->$field ? 'validado' : 'pendiente' }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($inscripcion->usuario_validacion_id)
                    <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400">
                        <p>Validado por: {{ $inscripcion->usuarioValidacion?->nombre_completo ?? 'Usuario eliminado' }}</p>
                        <p>Fecha: {{ $inscripcion->fecha_validacion?->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- CONDICIONES PARTICULARES --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Condiciones Particulares
                    </h2>
                    @if(auth()->user()->hasPermission('inscripciones.editar'))
                    <button onclick="document.getElementById('modalCondicion').classList.remove('hidden')"
                        class="text-xs bg-utn-blue/5 text-utn-blue-dark hover:bg-utn-blue/10 px-3 py-1.5 rounded-lg font-medium transition-colors">
                        + Agregar
                    </button>
                    @endif
                </div>
                <div class="p-6">
                    @if($inscripcion->condicionesParticulares && $inscripcion->condicionesParticulares->where('activa', true)->count() > 0)
                    <div class="space-y-3">
                        @foreach($inscripcion->condicionesParticulares->where('activa', true) as $condicion)
                        <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-medium text-purple-900 text-sm">{{ $condicion->titulo }}</p>
                                    <p class="text-xs text-purple-500 capitalize mt-0.5">{{ str_replace('_', ' ', $condicion->tipo) }}</p>
                                </div>
                                @if(auth()->user()->hasPermission('inscripciones.editar'))
                                <form action="{{ route('inscripciones.desactivar-condicion', $condicion) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-purple-300 hover:text-purple-600 transition-colors" title="Desactivar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                            @if($condicion->descripcion)
                            <p class="text-xs text-purple-700 mt-2">{{ Str::limit($condicion->descripcion, 100) }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-400 text-center py-4 text-sm">Sin condiciones registradas.</p>
                    @endif
                </div>
            </div>

            {{-- SOLICITUDES DE CAMBIO --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                @php
                    $tieneSolicitudPendiente = $inscripcion->solicitudesCambio()
                        ->whereIn('estado', ['pendiente', 'en_revision', 'trueque_detectado'])
                        ->exists();
                @endphp
                <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Solicitudes de Cambio
                    </h2>
                    @if(auth()->user()->hasPermission('inscripciones.editar') || auth()->user()->hasPermission('inscripciones.ver'))
                        @if(!empty($tieneComision) && !$tieneSolicitudPendiente)
                            <button onclick="document.getElementById('modalSolicitudCambio').classList.remove('hidden')"
                                class="text-xs bg-utn-blue/5 text-utn-blue-dark hover:bg-utn-blue/10 px-3 py-1.5 rounded-lg font-medium transition-colors">
                                + Nueva
                            </button>
                        @elseif($tieneSolicitudPendiente)
                            <span class="text-xs text-yellow-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                En proceso
                            </span>
                        @endif
                    @endif
                </div>
                <div class="p-6">
                    @if($inscripcion->solicitudesCambio && $inscripcion->solicitudesCambio->count() > 0)
                    <div class="space-y-3">
                        @foreach($inscripcion->solicitudesCambio->sortByDesc('created_at')->take(5) as $solicitud)
                        <div class="p-3 rounded-lg {{ $solicitud->estado === 'trueque_detectado' ? 'bg-purple-50 border border-purple-200' : 'bg-gray-50 border border-gray-100' }}">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="text-sm font-medium text-gray-900">{{ \App\Models\SolicitudCambio::getTipos()[$solicitud->tipo] ?? ucfirst($solicitud->tipo) }}</span>
                                    @if($solicitud->tipo === 'comision' && $solicitud->comisionDestino)
                                        <span class="text-xs text-gray-400 ml-1">&rarr; {{ $solicitud->comisionDestino->nombre }}</span>
                                    @endif
                                </div>
                                @php
                                $estadoColors = [
                                    'pendiente' => 'bg-yellow-100 text-yellow-700',
                                    'en_revision' => 'bg-utn-blue/10 text-utn-blue-dark',
                                    'aprobada' => 'bg-green-100 text-green-700',
                                    'rechazada' => 'bg-red-100 text-red-700',
                                    'cancelada' => 'bg-gray-100 text-gray-600',
                                    'trueque_detectado' => 'bg-purple-100 text-purple-700',
                                ];
                                @endphp
                                <span class="px-2 py-0.5 text-xs rounded-full font-medium flex-shrink-0 {{ $estadoColors[$solicitud->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ \App\Models\SolicitudCambio::getEstados()[$solicitud->estado] ?? ucfirst($solicitud->estado) }}
                                </span>
                            </div>
                            @if($solicitud->solicitud_trueque_id)
                            <p class="text-xs text-purple-600 mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                Trueque detectado
                            </p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">{{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
                            @if($solicitud->estado === 'rechazada' && $solicitud->motivo_rechazo)
                            <p class="text-xs text-red-500 mt-1">Motivo: {{ Str::limit($solicitud->motivo_rechazo, 60) }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-400 text-center py-4 text-sm">Sin solicitudes de cambio.</p>
                    @endif
                </div>
            </div>

            {{-- INFORMACION DE AUDITORIA --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Auditor&iacute;a
                    </h2>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div>
                        <span class="block text-xs text-gray-400 mb-0.5">Registrado por</span>
                        <p class="font-medium text-gray-700">{{ $inscripcion->usuarioRegistro?->nombre_completo ?? 'Usuario eliminado' }}</p>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400 mb-0.5">Fecha de creaci&oacute;n</span>
                        <p class="text-gray-700">{{ $inscripcion->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400 mb-0.5">&Uacute;ltima modificaci&oacute;n</span>
                        <p class="text-gray-700">{{ $inscripcion->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ================================================================ --}}
{{-- MODALES                                                          --}}
{{-- ================================================================ --}}

{{-- Modal de cancelación --}}
<div id="modal-cancelar" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-2xl">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Cancelar Inscripci&oacute;n</h3>
        <form action="{{ route('inscripciones.cancelar', $inscripcion) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="motivo_cancelacion" class="block text-sm font-medium text-gray-700 mb-2">Motivo de cancelaci&oacute;n</label>
                <textarea name="motivo_cancelacion" id="motivo_cancelacion" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                          placeholder="Ingrese el motivo de la cancelación..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-cancelar').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">
                    Volver
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                    Confirmar Cancelaci&oacute;n
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal de reactivación --}}
<div id="modal-reactivar" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-2xl">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Reactivar Inscripci&oacute;n</h3>
        <p class="text-sm text-gray-600 mb-4">
            Al reactivar esta inscripci&oacute;n, el alumno volver&aacute; a estar inscripto y deber&aacute; ser asignado a una comisi&oacute;n manualmente.
        </p>
        <form action="{{ route('inscripciones.reactivar', $inscripcion) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="motivo_reactivacion" class="block text-sm font-medium text-gray-700 mb-2">Motivo de reactivaci&oacute;n</label>
                <textarea name="motivo_reactivacion" id="motivo_reactivacion" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                          placeholder="Ingrese el motivo de la reactivación..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-reactivar').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">
                    Volver
                </button>
                <button type="submit" class="px-4 py-2 bg-utn-blue-darker text-white rounded-lg hover:bg-utn-dark-light text-sm font-medium">
                    Confirmar Reactivaci&oacute;n
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Agregar Condición Particular --}}
<div id="modalCondicion" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="px-6 py-4 border-b bg-gray-50 rounded-t-xl">
            <h3 class="text-lg font-semibold text-gray-900">Agregar Condici&oacute;n Particular</h3>
        </div>
        <form action="{{ route('inscripciones.agregar-condicion', $inscripcion) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <select name="tipo" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue-dark">
                        @foreach(\App\Models\CondicionParticular::getTipos() as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">T&iacute;tulo</label>
                    <input type="text" name="titulo" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue-dark" placeholder="Ej: Hipoacusia leve">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripci&oacute;n</label>
                    <textarea name="descripcion" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue-dark" placeholder="Describa la condición..."></textarea>
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="requiere_adecuacion" value="1" class="rounded border-gray-300 text-utn-blue-dark focus:ring-utn-blue-dark">
                        <span class="text-sm text-gray-700">Requiere adecuaciones pedag&oacute;gicas</span>
                    </label>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button type="button" onclick="document.getElementById('modalCondicion').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-utn-blue-darker text-white rounded-lg hover:bg-utn-dark text-sm font-medium">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Solicitud de Cambio --}}
<div id="modalSolicitudCambio" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="px-6 py-4 border-b bg-gray-50 rounded-t-xl">
            <h3 class="text-lg font-semibold text-gray-900">Solicitar Cambio</h3>
        </div>
        <div class="p-6">
            @if(!empty($tieneComision) && $comision)
                <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-utn-blue-dark">
                        <span class="font-medium">Comisi&oacute;n actual:</span> {{ $comision->nombre }}
                        ({{ $comision->turno ?? 'Sin turno' }} - {{ $comision->modalidad ?? 'Sin modalidad' }})
                    </p>
                </div>

                <form action="{{ route('inscripciones.crear-solicitud', $inscripcion) }}" method="POST">
                    @csrf
                    <input type="hidden" name="tipo" value="comision">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Comisi&oacute;n Destino <span class="text-red-500">*</span></label>
                            <select name="comision_destino_id" id="comisionDestinoSelect" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue-dark">
                                <option value="">Seleccionar comisi&oacute;n...</option>
                                @if(isset($comisionesDisponibles) && $comisionesDisponibles->count() > 0)
                                    @foreach($comisionesDisponibles as $comisionDisp)
                                        @if($comisionDisp->id !== $comision->id)
                                            <option value="{{ $comisionDisp->id }}"
                                                    data-cupos="{{ $comisionDisp->cupos_disponibles }}"
                                                    data-virtual="{{ $comisionDisp->esVirtual() ? '1' : '0' }}"
                                                    data-turno="{{ $comisionDisp->turno }}"
                                                    data-modalidad="{{ $comisionDisp->modalidad }}"
                                                    data-periodo="{{ $comisionDisp->periodo }}">
                                                {{ $comisionDisp->nombre }} - {{ $comisionDisp->turno ?? '' }} {{ $comisionDisp->modalidad ?? '' }}
                                                @if($comisionDisp->esVirtual())
                                                    (Sin l&iacute;mite)
                                                @elseif($comisionDisp->cupos_disponibles !== null)
                                                    ({{ $comisionDisp->cupos_disponibles }} cupos)
                                                @endif
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            <p id="alertaCupos" class="hidden mt-1 text-sm text-orange-600">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Esta comisi&oacute;n tiene pocos cupos disponibles.
                            </p>
                            <div id="infoCambios" class="hidden mt-2 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                <p class="text-xs font-medium text-amber-800 mb-1">Al cambiar a esta comisi&oacute;n se actualizar&aacute;n:</p>
                                <ul id="listaCambios" class="text-xs text-amber-700 space-y-0.5"></ul>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo <span class="text-red-500">*</span></label>
                            <textarea name="motivo" required rows="3" minlength="10" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue-dark" placeholder="Explique el motivo del cambio (m&iacute;nimo 10 caracteres)..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="document.getElementById('modalSolicitudCambio').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-utn-blue-darker text-white rounded-lg hover:bg-utn-dark text-sm font-medium">Enviar Solicitud</button>
                    </div>
                </form>
            @else
                <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <p class="text-sm font-medium text-yellow-800">No se puede solicitar cambio</p>
                    <p class="text-sm text-yellow-700 mt-1">El alumno debe estar asignado a una comisi&oacute;n.</p>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="button" onclick="document.getElementById('modalSolicitudCambio').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium">Cerrar</button>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal aprobación excepcional --}}
@if(auth()->user()->hasPermission('inscripciones.editar') && $inscripcion->estado_ingreso !== \App\Models\Inscripcion::INGRESO_APROBADO && !empty($tieneComision))
<div id="modalAprobarExcepcional" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full">
        <div class="px-6 py-4 border-b bg-amber-50 rounded-t-xl">
            <h3 class="text-lg font-semibold text-gray-900">Aprobaci&oacute;n Excepcional de Trayectoria</h3>
        </div>
        <form action="{{ route('inscripciones.aprobar-excepcional', $inscripcion) }}" method="POST">
            @csrf
            <div class="p-6">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-5">
                    <p class="text-sm text-amber-800">
                        <strong>Atenci&oacute;n:</strong> Esta acci&oacute;n aprobar&aacute; la trayectoria del alumno aunque no haya completado
                        todas las materias. Se registrar&aacute; el motivo y el usuario que autoriz&oacute; la aprobaci&oacute;n.
                    </p>
                </div>

                @if(!empty($resumenNotas))
                <div class="mb-5 text-sm text-gray-600">
                    <p>Estado actual: <strong>{{ $resumenNotas['materias_aprobadas'] }}/{{ $resumenNotas['total_materias'] }}</strong> materias aprobadas</p>
                </div>
                @endif

                <div>
                    <label for="motivo_excepcional" class="block text-sm font-medium text-gray-700 mb-1">
                        Motivo de la aprobaci&oacute;n excepcional <span class="text-red-500">*</span>
                    </label>
                    <textarea name="motivo" id="motivo_excepcional" rows="3" required minlength="10"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm"
                        placeholder="Ej: Resolución del Consejo Directivo N° XXX, situación particular del alumno..."></textarea>
                    <p class="text-xs text-gray-400 mt-1">M&iacute;nimo 10 caracteres. Quedar&aacute; registrado en el historial.</p>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button type="button" onclick="document.getElementById('modalAprobarExcepcional').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 text-sm font-medium">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 text-sm font-medium">
                    Confirmar Aprobaci&oacute;n Excepcional
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Modal Nota Final Materia --}}
@if(!empty($tieneComision) && $comision && auth()->user()->hasPermission('evaluaciones.editar'))
<div id="modalNotaFinal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Nota Final de Materia</h3>
                <button onclick="document.getElementById('modalNotaFinal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                Materia: <strong id="modalNotaFinalMateriaName"></strong>
            </p>

            <div id="notaSugeridaContainer" class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <span class="text-xs text-blue-600 font-medium uppercase">Nota Sugerida (promedio evaluaciones)</span>
                        <p class="text-lg font-bold text-blue-800" id="notaSugeridaValor">-</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('inscripciones.nota-final-materia', $inscripcion) }}" method="POST">
                @csrf
                <input type="hidden" name="materia_id" id="modalNotaFinalMateriaId">
                <input type="hidden" name="comision_id" value="{{ $comision->id }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nota Final <span class="text-red-500">*</span></label>
                    <input type="number" name="nota_final" id="modalNotaFinalInput" min="0" max="10" step="0.01" required
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue-dark text-lg font-bold text-center"
                        placeholder="0.00">
                    <p class="text-xs text-gray-400 mt-1">Ingrese la nota final de la materia (0 a 10)</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="observaciones" rows="2" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue-dark text-sm" placeholder="Opcional..."></textarea>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <button type="button" onclick="document.getElementById('modalNotaFinal').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                        Cancelar
                    </button>
                    <div class="flex gap-2">
                        <button type="button" id="btnUsarSugerida"
                            class="px-4 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 font-medium transition-colors">
                            Usar Sugerida
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm bg-utn-blue text-white rounded-lg hover:bg-utn-dark font-medium transition-colors">
                            Guardar Nota Final
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    function abrirModalNotaFinal(materiaId, materiaNombre, notaActual, notaSugerida) {
        document.getElementById('modalNotaFinalMateriaId').value = materiaId;
        document.getElementById('modalNotaFinalMateriaName').textContent = materiaNombre;

        var input = document.getElementById('modalNotaFinalInput');
        input.value = notaActual !== null ? notaActual : '';

        var sugeridaContainer = document.getElementById('notaSugeridaContainer');
        var sugeridaValor = document.getElementById('notaSugeridaValor');
        var btnSugerida = document.getElementById('btnUsarSugerida');

        if (notaSugerida !== null && notaSugerida !== undefined) {
            sugeridaContainer.classList.remove('hidden');
            sugeridaValor.textContent = parseFloat(notaSugerida).toFixed(1);
            btnSugerida.classList.remove('hidden');
            btnSugerida.onclick = function() {
                input.value = parseFloat(notaSugerida).toFixed(2);
            };
        } else {
            sugeridaContainer.classList.add('hidden');
            btnSugerida.classList.add('hidden');
        }

        document.getElementById('modalNotaFinal').classList.remove('hidden');
    }
</script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const comisionSelect = document.getElementById('comisionDestinoSelect');
        const alertaCupos = document.getElementById('alertaCupos');
        const infoCambios = document.getElementById('infoCambios');
        const listaCambios = document.getElementById('listaCambios');

        // Datos actuales de la inscripción
        const datosActuales = {
            modalidad: '{{ $comision->modalidad ?? '' }}',
            turno: '{{ $comision->turno ?? '' }}',
            periodo: '{{ $comision->periodo ?? '' }}'
        };

        if (comisionSelect) {
            comisionSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (!selectedOption.value) {
                    if (alertaCupos) alertaCupos.classList.add('hidden');
                    if (infoCambios) infoCambios.classList.add('hidden');
                    return;
                }

                const cupos = parseInt(selectedOption.dataset.cupos);
                const esVirtual = selectedOption.dataset.virtual === '1';

                // Alerta de pocos cupos
                if (alertaCupos) {
                    if (!esVirtual && !isNaN(cupos) && cupos <= 3 && cupos > 0) {
                        alertaCupos.classList.remove('hidden');
                    } else {
                        alertaCupos.classList.add('hidden');
                    }
                }

                // Mostrar cambios que se aplicarán
                if (infoCambios && listaCambios) {
                    const cambios = [];
                    const turnoDestino = selectedOption.dataset.turno || '';
                    const modalidadDestino = selectedOption.dataset.modalidad || '';
                    const periodoDestino = selectedOption.dataset.periodo || '';

                    if (modalidadDestino && modalidadDestino !== datosActuales.modalidad) {
                        cambios.push('Modalidad: ' + datosActuales.modalidad + ' \u2192 ' + modalidadDestino);
                    }
                    if (turnoDestino && turnoDestino !== datosActuales.turno) {
                        cambios.push('Turno: ' + datosActuales.turno + ' \u2192 ' + turnoDestino);
                    }
                    if (periodoDestino && periodoDestino !== datosActuales.periodo) {
                        cambios.push('Tipo ingreso: ' + datosActuales.periodo + ' \u2192 ' + periodoDestino);
                    }

                    if (cambios.length > 0) {
                        listaCambios.innerHTML = cambios.map(function(c) { return '<li>\u2022 ' + c + '</li>'; }).join('');
                        infoCambios.classList.remove('hidden');
                    } else {
                        infoCambios.classList.add('hidden');
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
