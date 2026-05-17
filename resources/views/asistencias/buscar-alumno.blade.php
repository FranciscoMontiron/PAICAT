@extends('layouts.app')
@section('title', 'Buscar Alumno')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Buscar Alumno</h1>
                <p class="text-gray-600 mt-2">
                    @if(isset($esDocente) && $esDocente)
                    Busca alumnos de tus comisiones para ver su historial de asistencias
                    @else
                    Busca alumnos para ver su historial de asistencias
                    @endif
                </p>
            </div>
            <a href="{{ route('asistencias.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Información contextual para docentes -->
    @if(isset($esDocente) && $esDocente)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-utn-blue-dark" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-utn-blue-dark">
                        <strong>Búsqueda limitada:</strong> Solo puedes buscar alumnos que están inscritos en tus comisiones.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Buscador Simple -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('asistencias.buscar-alumno') }}" class="flex gap-3">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Buscar por nombre, apellido, email o DNI..."
                           class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                           autofocus>
                </div>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Buscar
                </button>
                @if($search)
                    <a href="{{ route('asistencias.buscar-alumno') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Resultados -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                @if($search)
                    Resultados de búsqueda ({{ $alumnos->total() }})
                @else
                    @if(isset($esDocente) && $esDocente)
                    Alumnos de tus comisiones ({{ $alumnos->total() }})
                    @else
                    Todos los Alumnos ({{ $alumnos->total() }})
                    @endif
                @endif
            </h2>
        </div>

        @if($alumnos->count() > 0)
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($alumnos as $alumno)
                        <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg transition duration-200">
                            <div class="flex items-start justify-between">
                                <!-- Info del Alumno -->
                                <div class="flex items-start flex-1">
                                    <div class="bg-purple-100 rounded-full p-3 mr-4 flex-shrink-0">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $alumno->name }} {{ $alumno->apellido }}</h3>
                                        <p class="text-sm text-gray-600">{{ $alumno->email }}</p>
                                        <div class="flex items-center gap-3 mt-2">
                                            @if($alumno->dni)
                                                <span class="px-2 py-1 text-xs font-semibold rounded bg-utn-blue/10 text-utn-blue-dark">
                                                    DNI: {{ $alumno->dni }}
                                                </span>
                                            @endif
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                                                {{ $alumno->inscripciones_comision->count() }} comisión(es)
                                            </span>
                                            @if($alumno->total_ausencias > 0)
                                                <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                                    {{ $alumno->total_ausencias }} ausencia(s)
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones para cada comisión -->
                                @if($alumno->inscripciones_comision->count() > 0)
                                    <div class="ml-6">
                                        <div class="flex flex-col gap-2">
                                            @foreach($alumno->inscripciones_comision as $inscripcion)
                                                <a href="{{ route('asistencias.alumno.historial', [$inscripcion->comision, $inscripcion]) }}" 
                                                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200 text-sm font-medium flex items-center justify-center whitespace-nowrap min-w-[180px]">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                    </svg>
                                                    {{ $inscripcion->comision->codigo }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Comisiones del Alumno (solo visual) -->
                            @if($alumno->inscripciones_comision->count() > 0)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <p class="text-xs text-gray-600 mb-2">Comisiones inscritas:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($alumno->inscripciones_comision as $inscripcion)
                                            @php
                                                $ausenciasInsc = $inscripcion->asistencias->where('estado', 'ausente')->count();
                                                $porcentaje = $inscripcion->calcularPorcentajeAsistencia();
                                            @endphp
                                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                                <span class="font-semibold">{{ $inscripcion->comision->codigo }}</span> - 
                                                {{ $inscripcion->comision->nombre }}
                                                <span class="ml-1 {{ $ausenciasInsc > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    ({{ $porcentaje }}%)
                                                </span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                @if($alumnos->hasPages())
                    <div class="mt-6">
                        {{ $alumnos->appends(['search' => $search])->links() }}
                    </div>
                @endif
            </div>
        @else
            <!-- Sin Resultados -->
            <div class="p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($search)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    @endif
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">
                    @if($search)
                        No se encontraron resultados
                    @else
                        @if(isset($esDocente) && $esDocente)
                        No hay alumnos en tus comisiones
                        @else
                        No hay alumnos registrados
                        @endif
                    @endif
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if($search)
                        Intenta con otro término de búsqueda.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Información simple -->
    <div class="bg-purple-50 rounded-lg p-6 mt-6">
        <h3 class="text-sm font-semibold text-gray-800 mb-2">Cómo usar esta búsqueda</h3>
        <ul class="text-sm text-gray-600 space-y-1">
            <li>• Busca por nombre, apellido, email o DNI del alumno</li>
            <li>• Cada botón morado muestra el código de la comisión</li>
            <li>• Click en el botón para ver el historial de asistencias en esa comisión</li>
            @if(isset($esDocente) && $esDocente)
                <li>• Solo puedes ver alumnos de tus comisiones asignadas</li>
            @else
                <li>• Se muestran todos los alumnos registrados en el sistema</li>
            @endif
        </ul>
    </div>
</div>
@endsection