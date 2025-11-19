@extends('layouts.app')
@section('title', 'Seleccionar Alumno - Justificar Inasistencias')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Justificar Inasistencias</h1>
                <p class="text-gray-600 mt-2">{{ $comision->codigo }} - {{ $comision->nombre }}</p>
            </div>
            <a href="{{ route('asistencias.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Información -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Selecciona el alumno cuyas inasistencias deseas justificar. Solo se muestran alumnos con ausencias sin justificar.
                </p>
            </div>
        </div>
    </div>

    <!-- Listado de Alumnos con Ausencias -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                Alumnos con Ausencias ({{ $inscripciones->count() }})
            </h2>
        </div>

        @if($inscripciones->count() > 0)
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($inscripciones as $inscripcion)
                        <a href="{{ route('asistencias.alumno.justificar', [$comision, $inscripcion]) }}" 
                           class="block border border-gray-200 rounded-lg p-4 hover:shadow-lg hover:border-yellow-500 transition duration-200">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $inscripcion->alumno->name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $inscripcion->alumno->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between bg-red-50 rounded-lg p-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="text-xs text-gray-600">Ausencias sin justificar</p>
                                        <p class="text-2xl font-bold text-red-600">{{ $inscripcion->asistencias->count() }}</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>

                            <div class="mt-3 text-center">
                                <span class="text-xs text-yellow-600 font-medium">
                                    Click para justificar
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Sin Alumnos con Ausencias -->
            <div class="p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">¡Excelente!</h3>
                <p class="mt-2 text-sm text-gray-500">
                    No hay alumnos con ausencias sin justificar en esta comisión.
                </p>
                <div class="mt-6 flex gap-3 justify-center">
                    <a href="{{ route('asistencias.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Volver a Comisiones
                    </a>
                    <a href="{{ route('asistencias.historial', $comision) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Ver Historial
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Nota Informativa -->
    @if($inscripciones->count() > 0)
        <div class="bg-gray-50 rounded-lg p-6 mt-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-2">Información</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• Solo se muestran alumnos que tienen ausencias sin justificar</li>
                <li>• Podrás seleccionar múltiples fechas para justificar de una sola vez</li>
                <li>• Todas las fechas seleccionadas compartirán el mismo motivo de justificación</li>
            </ul>
        </div>
    @endif
</div>
@endsection

