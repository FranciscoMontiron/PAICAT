@extends('layouts.app')
@section('title','Historial de Asistencias')
@section('content')
<div class="container mx-auto p-4">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
        <a href="{{ route('asistencias.index') }}" class="hover:text-blue-600">Asistencias</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('asistencias.comision', $inscripcionComision->comision->id) }}" class="hover:text-blue-600">
            {{ $inscripcionComision->comision->nombre }}
        </a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-800 font-medium">{{ $inscripcionComision->academicoDato->user->name ?? $inscripcionComision->alumno->name ?? 'Alumno' }}</span>
    </div>

    <div class="bg-white border-b border-gray-200 shadow-sm sm:rounded-lg">
        <div class="p-6">
            
            <!-- Encabezado -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-utn-blue mb-1">
                        {{ $inscripcionComision->academicoDato->user->name ?? $inscripcionComision->alumno->name ?? 'Alumno' }}
                    </h1>
                    <p class="text-gray-600">Comisión: {{ $inscripcionComision->comision->nombre }} - {{ $inscripcionComision->comision->turno }}</p>
                </div>
                <a href="{{ route('asistencias.comision', $inscripcionComision->comision->id) }}" 
                   class="flex items-center gap-2 text-gray-600 hover:text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Volver a Comisión
                </a>
            </div>

            <!-- Tarjeta de porcentaje principal -->
            <div class="mb-6 p-6 rounded-lg {{ $estaEnRiesgo ? 'bg-red-50 border-2 border-red-200' : ($porcentaje >= 85 ? 'bg-green-50 border-2 border-green-200' : 'bg-yellow-50 border-2 border-yellow-200') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium mb-2 {{ $estaEnRiesgo ? 'text-red-700' : ($porcentaje >= 85 ? 'text-green-700' : 'text-yellow-700') }}">
                            Porcentaje de Asistencia
                        </p>
                        <p class="text-5xl font-bold {{ $estaEnRiesgo ? 'text-red-900' : ($porcentaje >= 85 ? 'text-green-900' : 'text-yellow-900') }}">
                            {{ round($porcentaje, 1) }}%
                        </p>
                    </div>
                    @if($estaEnRiesgo)
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="bg-red-600 text-white px-4 py-2 rounded-full text-sm font-bold">
                                ⚠️ EN RIESGO
                            </span>
                        </div>
                    @elseif($porcentaje >= 85)
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="bg-green-600 text-white px-4 py-2 rounded-full text-sm font-bold">
                                ✓ EXCELENTE
                            </span>
                        </div>
                    @else
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-yellow-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="bg-yellow-600 text-white px-4 py-2 rounded-full text-sm font-bold">
                                ⚠ PRECAUCIÓN
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total'] }}</p>
                    <p class="text-sm text-gray-600 mt-1">Total</p>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-900">{{ $estadisticas['presentes'] }}</p>
                    <p class="text-sm text-green-700 mt-1">Presentes</p>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-red-900">{{ $estadisticas['ausentes'] }}</p>
                    <p class="text-sm text-red-700 mt-1">Ausentes</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-900">{{ $estadisticas['tardanzas'] }}</p>
                    <p class="text-sm text-yellow-700 mt-1">Tardanzas</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-blue-900">{{ $estadisticas['justificadas'] }}</p>
                    <p class="text-sm text-blue-700 mt-1">Justificadas</p>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <a href="{{ route('asistencias.justificar', $inscripcionComision->comision->id) }}" 
                   class="flex items-center justify-center gap-2 p-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    Justificar Inasistencia
                </a>
                <button onclick="window.print()" 
                        class="flex items-center justify-center gap-2 p-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    Imprimir Historial
                </button>
            </div>

            <!-- Tabla de historial -->
            <div class="border-t pt-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Historial de Asistencias</h3>
                
                @if($inscripcionComision->asistencias->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b-2 border-gray-300">
                                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inscripcionComision->asistencias as $asistencia)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="border border-gray-300 px-4 py-3 text-sm">
                                            {{ $asistencia->fecha->format('d/m/Y') }}
                                            <span class="text-xs text-gray-500 block">{{ $asistencia->fecha->locale('es')->isoFormat('dddd') }}</span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3">
                                            @php
                                                $estadoConfig = [
                                                    'presente' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Presente'],
                                                    'ausente' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Ausente'],
                                                    'tardanza' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Tardanza'],
                                                    'justificado' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Justificado'],
                                                ];
                                                $config = $estadoConfig[$asistencia->estado] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($asistencia->estado)];
                                            @endphp
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                                {{ $config['label'] }}
                                            </span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-sm text-gray-700">
                                            {{ $asistencia->observaciones ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-lg font-semibold mb-2">No hay registros de asistencia</p>
                        <p class="text-sm">Aún no se ha registrado ninguna asistencia para este alumno</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>

<!-- Estilos para impresión -->
<style>
    @media print {
        .no-print, nav, footer, button, a {
            display: none !important;
        }
        body {
            background: white !important;
        }
    }
</style>
@endsection
@section('title', 'Historial de Asistencias')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Historial de Asistencias</h1>
                <p class="text-gray-600 mt-2">{{ $comision->codigo }} - {{ $comision->nombre }}</p>
            </div>
            <div class="flex gap-3">
                @if(auth()->user()->hasPermission('asistencias.crear'))
                    <a href="{{ route('asistencias.create', $comision) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Pasar Asistencia
                    </a>
                @endif
                <a href="{{ route('asistencias.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
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
                        Alumnos en Riesgo de Deserción
                    </h3>
                    <p class="mt-1 text-sm text-red-700">
                        Hay {{ $estadisticas->where('en_riesgo', true)->count() }} alumno(s) con menos del 75% de asistencia o con 3+ ausencias consecutivas.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla de Asistencias -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Detalle por Alumno</h2>
        </div>

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
                                            <a href="{{ route('asistencias.alumno.historial', [$comision, $stat['inscripcion']]) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                                {{ $stat['inscripcion']->alumno->name }}
                                            </a>
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
                                        <div class="w-16">
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
                <p class="mt-2 text-sm text-gray-500">Aún no se ha registrado ninguna asistencia para esta comisión.</p>
                @if(auth()->user()->hasPermission('asistencias.crear'))
                    <div class="mt-6">
                        <a href="{{ route('asistencias.create', $comision) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
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
@endsection

