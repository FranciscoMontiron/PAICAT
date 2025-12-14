@extends('layouts.app')
@section('title','Comisión '.$comision->nombre)
@section('content')
<div class="container mx-auto p-4">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
        <a href="{{ route('asistencias.index') }}" class="hover:text-blue-600">Asistencias</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-800 font-medium">{{ $comision->nombre }}</span>
    </div>

    <div class="bg-white border-b border-gray-200 shadow-sm sm:rounded-lg">
        <div class="p-6">
            
            <!-- Encabezado -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-utn-blue">{{ $comision->nombre }}</h1>
                    <p class="text-gray-600 mt-1">{{ $comision->turno }}</p>
                    @if($comision->docente)
                        <p class="text-sm text-gray-500 mt-1">Docente: {{ $comision->docente->name }}</p>
                    @endif
                </div>
                <a href="{{ route('asistencias.index') }}" class="flex items-center gap-2 text-gray-600 hover:text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>
            </div>

            <!-- Tarjetas de resumen -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-700 font-medium mb-1">Total Alumnos</p>
                    <p class="text-3xl font-bold text-blue-900">{{ $totalAlumnos }}</p>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-700 font-medium mb-1">Promedio Asistencia</p>
                    <p class="text-3xl font-bold text-green-900">{{ round($promedioAsistencia, 1) }}%</p>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-700 font-medium mb-1">Alumnos en Riesgo</p>
                    <p class="text-3xl font-bold text-red-900">{{ $alumnosEnRiesgo }}</p>
                </div>
            </div>

            <!-- Botones de acción principales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <a href="{{ route('asistencias.registrar', $comision->id) }}" 
                   class="flex items-center justify-center gap-2 p-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition shadow-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                    Registrar Asistencia Hoy
                </a>
                <a href="{{ route('asistencias.registrar', $comision->id) }}" 
                   class="flex items-center justify-center gap-2 p-4 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition shadow-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Modificar Asistencia
                </a>
                <a href="{{ route('asistencias.justificar', $comision->id) }}" 
                   class="flex items-center justify-center gap-2 p-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition shadow-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    Justificar Inasistencia
                </a>
            </div>

            <!-- Lista de alumnos -->
            <div class="border-t pt-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Lista de Alumnos</h3>
                
                @if($comision->inscripciones->count() > 0)
                    <div class="space-y-2">
                        @foreach($comision->inscripciones as $inscripcion)
                            @php
                                $porcentaje = $inscripcion->calcularPorcentajeAsistencia();
                                $estaEnRiesgo = $inscripcion->estaEnRiesgo();
                                $colorIndicador = $porcentaje >= 85 ? 'bg-green-500' : ($porcentaje >= 75 ? 'bg-yellow-500' : 'bg-red-500');
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full {{ $colorIndicador }}"></div>
                                    <div>
                                        <p class="font-medium text-gray-800">
                                            {{ $inscripcion->academicoDato->user->name ?? $inscripcion->alumno->name ?? 'Sin nombre' }}
                                        </p>
                                        @if($inscripcion->asistencias->count() > 0)
                                            <p class="text-sm text-gray-600">
                                                Asistencia: <span class="font-semibold {{ $estaEnRiesgo ? 'text-red-600' : 'text-green-600' }}">{{ round($porcentaje, 1) }}%</span>
                                                @if($estaEnRiesgo)
                                                    <span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full font-semibold">En riesgo</span>
                                                @endif
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-500">Sin registros de asistencia</p>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('asistencias.historial', $inscripcion->id) }}" 
                                   class="flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium px-3 py-2 rounded hover:bg-blue-50 transition">
                                    Ver Detalle
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p>No hay alumnos inscriptos en esta comisión</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>
@endsection