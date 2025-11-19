@extends('layouts.app')
@section('title', 'Historial de Asistencias - Alumno')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Historial Individual</h1>
                <p class="text-gray-600 mt-2">{{ $inscripcion->alumno->name }}</p>
                <p class="text-sm text-gray-500">{{ $comision->codigo }} - {{ $comision->nombre }}</p>
            </div>
            <div class="flex gap-3">
                @if(auth()->user()->hasPermission('asistencias.editar') && $estadisticas['ausentes'] > 0)
                    <a href="{{ route('asistencias.alumno.justificar', [$comision, $inscripcion]) }}" 
                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Justificar Inasistencias
                    </a>
                @endif
                <a href="{{ route('asistencias.historial', $comision) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Alumno -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600 mb-1">Total Clases</p>
            <p class="text-2xl font-bold text-gray-800">{{ $estadisticas['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600 mb-1">Presentes</p>
            <p class="text-2xl font-bold text-green-600">{{ $estadisticas['presentes'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600 mb-1">Tardanzas</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $estadisticas['tardanzas'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600 mb-1">Ausentes</p>
            <p class="text-2xl font-bold text-red-600">{{ $estadisticas['ausentes'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600 mb-1">Justificados</p>
            <p class="text-2xl font-bold text-blue-600">{{ $estadisticas['justificados'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600 mb-1">% Asistencia</p>
            <p class="text-2xl font-bold 
                @if($estadisticas['porcentaje'] >= 75) text-green-600
                @elseif($estadisticas['porcentaje'] >= 50) text-yellow-600
                @else text-red-600 @endif">
                {{ $estadisticas['porcentaje'] }}%
            </p>
        </div>
    </div>

    <!-- Alerta si tiene bajo porcentaje -->
    @if($estadisticas['porcentaje'] < 75 && $estadisticas['total'] > 0)
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Alumno en Riesgo</h3>
                    <p class="mt-1 text-sm text-red-700">
                        El porcentaje de asistencia es menor al 75%. Se recomienda contactar al alumno.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Historial Detallado -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Historial de Clases</h2>
        </div>

        @if($asistencias->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Observaciones
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Registrado Por
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($asistencias as $asistencia)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $asistencia->fecha->format('d/m/Y') }}
                                    <span class="text-xs text-gray-500 block">
                                        {{ $asistencia->fecha->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 text-xs font-semibold rounded
                                        @if($asistencia->estado == 'presente') bg-green-100 text-green-800
                                        @elseif($asistencia->estado == 'ausente') bg-red-100 text-red-800
                                        @elseif($asistencia->estado == 'tardanza') bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($asistencia->estado) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $asistencia->observaciones ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $asistencia->registradoPor->name ?? 'Sistema' }}
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
                <h3 class="mt-4 text-lg font-medium text-gray-900">Sin registros</h3>
                <p class="mt-2 text-sm text-gray-500">Aún no hay registros de asistencia para este alumno.</p>
            </div>
        @endif
    </div>

    <!-- Información Adicional -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Información del Alumno</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-600">Email:</p>
                <p class="text-gray-900 font-medium">{{ $inscripcion->alumno->email }}</p>
            </div>
            <div>
                <p class="text-gray-600">Estado de Inscripción:</p>
                <p class="text-gray-900 font-medium">{{ ucfirst($inscripcion->estado) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

