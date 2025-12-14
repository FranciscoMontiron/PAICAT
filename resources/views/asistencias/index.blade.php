@extends('layouts.app')
@section('title', 'Asistencias')
@section('content')
<div class="container mx-auto p-4">

    <!-- Encabezado con botón de alertas -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-utn-blue">Módulo de Asistencias</h1>
        <a href="{{ route('asistencias.alertas') }}" class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Ver Alertas
        </a>
    </div>

    <!-- Lista de comisiones -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($comisiones as $comision)
        <div class="bg-white shadow-sm rounded-lg p-5 border border-gray-200 hover:shadow-md transition">
            
            <!-- Header de la tarjeta -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $comision->nombre }}</h2>
                    <p class="text-sm text-gray-600">{{ $comision->turno }}</p>
                </div>
                @php
                    $inscripcionesActivas = $comision->inscripciones->where('estado', 'confirmada');
                    $alumnosEnRiesgo = $inscripcionesActivas->filter(fn($i) => $i->asistencias->count() > 0 && $i->estaEnRiesgo())->count();
                @endphp
                @if($alumnosEnRiesgo > 0)
                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-semibold">
                        {{ $alumnosEnRiesgo }} en riesgo
                    </span>
                @endif
            </div>

            <!-- Información de la comisión -->
            <div class="space-y-1 mb-4 text-sm text-gray-600">
                <p><strong class="text-gray-700">Alumnos:</strong> {{ $comision->inscripciones_count }}</p>
                <p><strong class="text-gray-700">Profesor:</strong> {{ $comision->docente->name ?? 'Sin asignar' }}</p>
                @if($comision->estado)
                    <p>
                        <strong class="text-gray-700">Estado:</strong>
                        <span class="px-2 py-0.5 rounded text-xs {{ $comision->estado === 'activa' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($comision->estado) }}
                        </span>
                    </p>
                @endif
            </div>

            <!-- Botón principal -->
            <a href="{{ route('asistencias.comision', $comision->id) }}" 
               class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 font-medium">
                Ver Comisión
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </a>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-lg border border-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No hay comisiones disponibles</h3>
            <p class="text-gray-500">Crea una comisión para comenzar a registrar asistencias</p>
        </div>
        @endforelse
    </div>

    <!-- Información del módulo al final -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">Módulo 3</span>
            </div>
            <div class="border border-gray-200 rounded-lg p-5 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Funcionalidades del Sistema</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Registrar asistencia diaria</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Modificar asistencia</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Justificar inasistencias</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Ver historial por alumno</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Calcular porcentaje de asistencia</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Alertar alumnos en riesgo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection