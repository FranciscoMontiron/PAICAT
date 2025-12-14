@extends('layouts.app')

@section('title', 'Alumnos en Riesgo')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-red-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-red-600">Alumnos en Riesgo por Asistencia</h1>
                    <p class="text-gray-600 mt-1">Alumnos con porcentaje de asistencia menor al 75%</p>
                </div>
            </div>
            <a href="{{ route('asistencias.index') }}" 
               class="flex items-center gap-2 text-gray-600 hover:text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Filtro por comisión -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('asistencias.alertas') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label for="comision_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Filtrar por Comisión
                    </label>
                    <select name="comision_id" id="comision_id" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            onchange="this.form.submit()">
                        <option value="">Todas las comisiones</option>
                        @foreach($comisiones as $c)
                            <option value="{{ $c->id }}" {{ $comisionId == $c->id ? 'selected' : '' }}>
                                {{ $c->codigo }} - {{ $c->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($comisionId)
                    <a href="{{ route('asistencias.alertas') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Limpiar Filtro
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Contenido Principal -->
    @if($alumnosEnRiesgo->count() > 0)
        <!-- Contador de alumnos en riesgo -->
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-red-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="text-red-800 font-semibold">
                        Se encontraron {{ $alumnosEnRiesgo->count() }} alumno{{ $alumnosEnRiesgo->count() != 1 ? 's' : '' }} en riesgo
                    </p>
                    <p class="text-red-700 text-sm">Es necesario tomar acción inmediata para evitar que pierdan por faltas</p>
                </div>
            </div>
        </div>

        <!-- Lista de alumnos en riesgo -->
        <div class="space-y-3 mb-6">
            @foreach($alumnosEnRiesgo as $inscripcion)
                @php
                    $totalAsistencias = $inscripcion->asistencias->count();
                    $presentes = $inscripcion->asistencias->where('estado', 'presente')->count();
                    $tardanzas = $inscripcion->asistencias->where('estado', 'tardanza')->count();
                    $justificados = $inscripcion->asistencias->where('estado', 'justificado')->count();
                    $asistio = $presentes + $tardanzas + $justificados;
                    $porcentaje = $totalAsistencias > 0 ? round(($asistio / $totalAsistencias) * 100, 1) : 0;
                    $nivelRiesgo = $porcentaje < 70 ? 'critico' : ($porcentaje < 75 ? 'alto' : 'medio');
                @endphp
                <div class="rounded-lg p-5 border-2 transition hover:shadow-md
                    {{ $nivelRiesgo === 'critico' ? 'bg-red-50 border-red-300' : 
                       ($nivelRiesgo === 'alto' ? 'bg-orange-50 border-orange-300' : 'bg-yellow-50 border-yellow-300') }}">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $inscripcion->alumno->name }}
                                </h3>
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    {{ $nivelRiesgo === 'critico' ? 'bg-red-600 text-white' : 
                                       ($nivelRiesgo === 'alto' ? 'bg-orange-600 text-white' : 'bg-yellow-600 text-white') }}">
                                    {{ $nivelRiesgo === 'critico' ? '🚨 CRÍTICO' : 
                                       ($nivelRiesgo === 'alto' ? '⚠️ RIESGO ALTO' : '⚠ PRECAUCIÓN') }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-700">
                                <span>
                                    <strong>Comisión:</strong> {{ $inscripcion->comision->codigo ?? '-' }}
                                </span>
                                <span>
                                    <strong>Total Clases:</strong> {{ $totalAsistencias }}
                                </span>
                                <span>
                                    <strong>Ausencias:</strong> {{ $inscripcion->asistencias->where('estado', 'ausente')->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right ml-6">
                            <p class="text-4xl font-bold {{ $nivelRiesgo === 'critico' ? 'text-red-700' : 
                                                             ($nivelRiesgo === 'alto' ? 'text-orange-700' : 'text-yellow-700') }}">
                                {{ $porcentaje }}%
                            </p>
                            <a href="{{ route('asistencias.alumno.historial', [$inscripcion->comision, $inscripcion]) }}" 
                               class="inline-flex items-center gap-1 mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Ver Detalle
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Barra de progreso visual -->
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500
                                {{ $nivelRiesgo === 'critico' ? 'bg-red-600' : 
                                   ($nivelRiesgo === 'alto' ? 'bg-orange-600' : 'bg-yellow-600') }}"
                                 style="width: {{ $porcentaje }}%">
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600 mt-1">
                            <span>0%</span>
                            <span class="font-semibold">Mínimo requerido: 75%</span>
                            <span>100%</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Leyenda -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h4 class="font-semibold text-gray-800 mb-3">Niveles de Riesgo:</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-red-600 text-white rounded-full text-xs font-bold">🚨 CRÍTICO</span>
                    <span class="text-gray-700">&lt; 70% de asistencia</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-orange-600 text-white rounded-full text-xs font-bold">⚠️ ALTO</span>
                    <span class="text-gray-700">70% - 74% de asistencia</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-yellow-600 text-white rounded-full text-xs font-bold">⚠ PRECAUCIÓN</span>
                    <span class="text-gray-700">75% - 84% de asistencia</span>
                </div>
            </div>
        </div>

    @else
        <!-- Estado vacío -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="bg-green-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">¡Excelente!</h3>
            <p class="text-gray-600 mb-1">No hay alumnos en riesgo por asistencia</p>
            <p class="text-sm text-gray-500">
                @if($comisionId)
                    en la comisión seleccionada
                @else
                    en ninguna comisión
                @endif
            </p>
        </div>
    @endif

</div>
@endsection