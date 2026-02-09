@extends('layouts.app')
@section('title', 'Historial de Asistencias - ' . $materia->nombre)
@section('content')
<div class="container mx-auto px-4 py-6">
    
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-6">
        <a href="{{ route('asistencias.index') }}" class="hover:text-blue-600 transition">Asistencias</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('asistencias.comision.materias', $comision) }}" class="hover:text-blue-600 transition">{{ $comision->codigo }}</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-800 font-medium">{{ $materia->nombre }}</span>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 bg-indigo-100 rounded-lg">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $materia->nombre }}</h1>
                        <p class="text-gray-600 mt-1">{{ $comision->codigo }} - {{ $comision->nombre }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $materia->codigo }} @if($materia->carga_horaria)| {{ $materia->carga_horaria }} hs semanales @endif</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                @if(auth()->user()->hasPermission('asistencias.crear') && $comision->estado == 'activa')
                    <a href="{{ route('asistencias.materia.registrar', [$comision, $materia]) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Pasar Asistencia
                    </a>
                @endif
                <a href="{{ route('asistencias.comision.materias', $comision) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Alumnos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $estadisticas->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-yellow-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Clases Registradas</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ $estadisticas->first()['total'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Asistencia Promedio</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $estadisticas->count() > 0 ? round($estadisticas->avg('porcentaje'), 1) : 0 }}%
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-red-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">En Riesgo</p>
                    <p class="text-2xl font-bold text-red-600">
                        {{ $estadisticas->where('en_riesgo', true)->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tasa Presentes</p>
                    <p class="text-2xl font-bold text-purple-600">
                        {{ $estadisticas->count() > 0 && $estadisticas->sum('total') > 0 ? round(($estadisticas->sum('presentes') / $estadisticas->sum('total')) * 100, 1) : 0 }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas de Riesgo -->
    @if($estadisticas->where('en_riesgo', true)->count() > 0)
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Alumnos en Riesgo de Deserción en esta Materia
                    </h3>
                    <p class="mt-1 text-sm text-red-700">
                        Hay {{ $estadisticas->where('en_riesgo', true)->count() }} alumno(s) con menos del 75% de asistencia en {{ $materia->nombre }}.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filtros de Fecha -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" 
                           class="w-full rounded-lg border-gray-300">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}" 
                           class="w-full rounded-lg border-gray-300">
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Filtrar
                </button>
                @if(request('fecha_desde') || request('fecha_hasta'))
                    <a href="{{ route('asistencias.materia.historial', [$comision, $materia]) }}" 
                       class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Sección: Lista de Alumnos (colapsable) -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 cursor-pointer" onclick="toggleSection('alumnosSection', 'alumnosChevron')">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-semibold text-gray-800">Lista de Alumnos</h2>
                    <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                        {{ $estadisticas->count() }} alumnos
                    </span>
                </div>
                <svg id="alumnosChevron" class="w-5 h-5 text-gray-500 transition-transform transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <p class="text-sm text-gray-600 mt-1">Haz clic para ver/ocultar el detalle por alumno</p>
        </div>
        <div id="alumnosSection" class="p-6">
            @if($estadisticas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Alumno
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Clases
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Presentes
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tardanzas
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ausentes
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Justificados
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    % Asistencia
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($estadisticas->sortByDesc('en_riesgo') as $stat)
                                <tr class="hover:bg-gray-50 {{ $stat['en_riesgo'] ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($stat['en_riesgo'])
                                                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $stat['inscripcion']->alumno->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $stat['inscripcion']->alumno->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                        {{ $stat['total'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                                            {{ $stat['presentes'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">
                                            {{ $stat['tardanzas'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                            {{ $stat['ausentes'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                            {{ $stat['justificados'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center">
                                            <div class="w-20">
                                                <div class="text-sm font-bold
                                                    @if($stat['porcentaje'] >= 75) text-green-600
                                                    @elseif($stat['porcentaje'] >= 50) text-yellow-600
                                                    @else text-red-600 @endif">
                                                    {{ $stat['porcentaje'] }}%
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                    <div class="h-1.5 rounded-full
                                                        @if($stat['porcentaje'] >= 75) bg-green-600
                                                        @elseif($stat['porcentaje'] >= 50) bg-yellow-600
                                                        @else bg-red-600 @endif"
                                                        style="width: {{ $stat['porcentaje'] }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($stat['en_riesgo'])
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                                En Riesgo
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                                                Normal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col gap-1 items-center">
                                            <a href="{{ route('asistencias.alumno.historial', [$comision, $stat['inscripcion']]) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Ver detalle
                                            </a>
                                            @if($stat['ausentes'] > 0)
                                                <a href="{{ route('asistencias.alumno.justificar', [$comision, $stat['inscripcion']]) }}?materia_id={{ $materia->id }}" 
                                                   class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                                                    Justificar faltas
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No hay registros de asistencia</h3>
                    <p class="mt-2 text-sm text-gray-500">Aún no se ha registrado ninguna asistencia para esta materia.</p>
                    @if(auth()->user()->hasPermission('asistencias.crear'))
                        <div class="mt-6">
                            <a href="{{ route('asistencias.materia.registrar', [$comision, $materia]) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Registrar Primera Asistencia
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Sección: Historial por Fecha (colapsable) -->
    @if($fechasClases->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 cursor-pointer" onclick="toggleSection('fechasSection', 'fechasChevron')">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-semibold text-gray-800">Historial por Fecha</h3>
                    <span class="px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-800">
                        {{ $fechasClases->count() }} clases
                    </span>
                </div>
                <svg id="fechasChevron" class="w-5 h-5 text-gray-500 transition-transform transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <p class="text-sm text-gray-600 mt-1">Haz clic para ver/ocultar el detalle por fecha</p>
        </div>
        <div id="fechasSection" class="hidden p-6">
            <div class="space-y-4">
                @foreach($fechasClases as $fecha => $asistencias)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</h4>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($fecha)->diffForHumans() }} - {{ $asistencias->count() }} registros</p>
                        </div>
                        @if(auth()->user()->hasPermission('asistencias.editar'))
                        <a href="{{ route('asistencias.materia.editar', [$comision, $materia, 'fecha' => $fecha]) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Editar
                        </a>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="text-center p-2 bg-green-50 rounded">
                            <p class="text-xs text-green-700">Presentes</p>
                            <p class="text-lg font-bold text-green-800">{{ $asistencias->where('estado', 'presente')->count() }}</p>
                        </div>
                        <div class="text-center p-2 bg-yellow-50 rounded">
                            <p class="text-xs text-yellow-700">Tardanzas</p>
                            <p class="text-lg font-bold text-yellow-800">{{ $asistencias->where('estado', 'tardanza')->count() }}</p>
                        </div>
                        <div class="text-center p-2 bg-red-50 rounded">
                            <p class="text-xs text-red-700">Ausentes</p>
                            <p class="text-lg font-bold text-red-800">{{ $asistencias->where('estado', 'ausente')->count() }}</p>
                        </div>
                        <div class="text-center p-2 bg-blue-50 rounded">
                            <p class="text-xs text-blue-700">Justificados</p>
                            <p class="text-lg font-bold text-blue-800">{{ $asistencias->where('estado', 'justificado')->count() }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Botones de expansión rápida -->
    <div class="flex justify-center gap-4 mb-6">
        <button onclick="expandAll()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"></path>
            </svg>
            Expandir Todo
        </button>
        <button onclick="collapseAll()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
            </svg>
            Contraer Todo
        </button>
    </div>

    <!-- Leyenda -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Leyenda</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-700"><span class="font-semibold">% Asistencia:</span> (Presentes + Tardanzas + Justificados) / Total Clases</p>
                <p class="text-gray-700 mt-2"><span class="font-semibold">En Riesgo:</span> Menos del 75% de asistencia o 3+ ausencias consecutivas</p>
            </div>
            <div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-green-600 rounded-full mr-2"></span>
                        <span class="text-gray-700">≥75% Asistencia</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-yellow-600 rounded-full mr-2"></span>
                        <span class="text-gray-700">50-74% Asistencia</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-red-600 rounded-full mr-2"></span>
                        <span class="text-gray-700"><50% Asistencia</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función genérica para alternar secciones
function toggleSection(sectionId, chevronId) {
    const section = document.getElementById(sectionId);
    const chevron = document.getElementById(chevronId);
    section.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
    
    // Guardar estado en localStorage
    const isHidden = section.classList.contains('hidden');
    localStorage.setItem(sectionId, isHidden ? 'collapsed' : 'expanded');
}

// Función para expandir todas las secciones
function expandAll() {
    document.getElementById('alumnosSection')?.classList.remove('hidden');
    document.getElementById('fechasSection')?.classList.remove('hidden');
    document.getElementById('alumnosChevron')?.classList.add('rotate-180');
    document.getElementById('fechasChevron')?.classList.add('rotate-180');
    
    // Guardar estados
    localStorage.setItem('alumnosSection', 'expanded');
    localStorage.setItem('fechasSection', 'expanded');
}

// Función para contraer todas las secciones
function collapseAll() {
    document.getElementById('alumnosSection')?.classList.add('hidden');
    document.getElementById('fechasSection')?.classList.add('hidden');
    document.getElementById('alumnosChevron')?.classList.remove('rotate-180');
    document.getElementById('fechasChevron')?.classList.remove('rotate-180');
    
    // Guardar estados
    localStorage.setItem('alumnosSection', 'collapsed');
    localStorage.setItem('fechasSection', 'collapsed');
}

// Cargar estados guardados al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const alumnosState = localStorage.getItem('alumnosSection');
    const fechasState = localStorage.getItem('fechasSection');
    
    // Aplicar estado de lista de alumnos
    if (alumnosState === 'collapsed') {
        document.getElementById('alumnosSection')?.classList.add('hidden');
        document.getElementById('alumnosChevron')?.classList.remove('rotate-180');
    } else if (alumnosState === 'expanded') {
        document.getElementById('alumnosSection')?.classList.remove('hidden');
        document.getElementById('alumnosChevron')?.classList.add('rotate-180');
    }
    
    // Aplicar estado de historial por fecha
    if (fechasState === 'collapsed') {
        document.getElementById('fechasSection')?.classList.add('hidden');
        document.getElementById('fechasChevron')?.classList.remove('rotate-180');
    } else if (fechasState === 'expanded') {
        document.getElementById('fechasSection')?.classList.remove('hidden');
        document.getElementById('fechasChevron')?.classList.add('rotate-180');
    }
});
</script>

<style>
.rotate-180 {
    transform: rotate(180deg);
}
.transition-transform {
    transition: transform 0.3s ease-in-out;
}
</style>
@endsection