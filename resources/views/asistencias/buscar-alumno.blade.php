@extends('layouts.app')
@section('title', 'Buscar Alumno - Justificar Inasistencias')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Buscar Alumno</h1>
                <p class="text-gray-600 mt-2">Busca el alumno para justificar sus inasistencias</p>
            </div>
            <a href="{{ route('asistencias.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Buscador -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('asistencias.buscar-alumno') }}" class="flex gap-3">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Buscar por nombre o email del alumno..."
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
                    Alumnos con Ausencias sin Justificar ({{ $alumnos->total() }})
                @endif
            </h2>
        </div>

        @if($alumnos->count() > 0)
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($alumnos as $alumno)
                        <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg transition duration-200">
                            <div class="flex items-center justify-between">
                                <!-- Info del Alumno -->
                                <div class="flex items-center flex-1">
                                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $alumno->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $alumno->email }}</p>
                                        <div class="flex items-center mt-2">
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                                {{ $alumno->total_ausencias }} ausencia(s) sin justificar
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Comisiones del Alumno -->
                                <div class="ml-6">
                                    @if($alumno->inscripcionesComision->where('asistencias', '!=', collect())->count() > 0)
                                        <div class="flex flex-col gap-2">
                                            @foreach($alumno->inscripcionesComision as $inscripcion)
                                                @if($inscripcion->asistencias->count() > 0)
                                                    <a href="{{ route('asistencias.alumno.justificar', [$inscripcion->comision, $inscripcion]) }}" 
                                                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition duration-200 text-sm font-medium flex items-center whitespace-nowrap">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        {{ $inscripcion->comision->codigo }} ({{ $inscripcion->asistencias->count() }})
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Detalle de Comisiones -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-600 mb-2">Comisiones con ausencias:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($alumno->inscripcionesComision as $inscripcion)
                                        @if($inscripcion->asistencias->count() > 0)
                                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                                <span class="font-semibold">{{ $inscripcion->comision->codigo }}</span> - 
                                                {{ $inscripcion->comision->nombre }} 
                                                <span class="text-red-600">({{ $inscripcion->asistencias->count() }} ausencias)</span>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    @endif
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">
                    @if($search)
                        No se encontraron resultados
                    @else
                        ¡Excelente! No hay alumnos con ausencias sin justificar
                    @endif
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if($search)
                        Intenta con otro término de búsqueda.
                    @else
                        Todos los alumnos están al día con sus asistencias.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('asistencias.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        Volver a Asistencias
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Información -->
    @if($alumnos->count() > 0)
        <div class="bg-purple-50 rounded-lg p-6 mt-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-2">Cómo usar esta búsqueda</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• Busca por nombre o email del alumno</li>
                <li>• Click en el botón amarillo de la comisión para justificar ausencias</li>
                <li>• Cada botón muestra entre paréntesis la cantidad de ausencias en esa comisión</li>
                <li>• Solo se muestran alumnos que tienen ausencias sin justificar</li>
            </ul>
        </div>
    @endif
</div>
@endsection

