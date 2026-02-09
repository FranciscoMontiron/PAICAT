@extends('layouts.app')

@section('title', 'Gestión de Asistencias')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Gestión de Asistencias</h1>
                <p class="text-gray-600 mt-2">Selecciona una comisión para gestionar sus asistencias</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('asistencias.alertas') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                    Ver Alertas
                </a>
                @if(auth()->user()->hasPermission('asistencias.editar'))
                <a href="{{ route('asistencias.buscar-alumno') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Buscar Alumno
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Banner informativo para docentes -->
    @if(isset($esDocente) && $esDocente)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Vista del Docente:</strong> Mostrando solo las comisiones en las que estás asignado como docente.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Comisiones Activas</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['comisiones_activas'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Alumnos</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_alumnos'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Asistencia Promedio</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ $stats['promedio_asistencia'] }}%</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Alumnos en Riesgo</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['alumnos_en_riesgo'] }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Filtros</h2>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('asistencias.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Año</label>
                    <select name="anio" class="w-full rounded-lg border-gray-300">
                        <option value="">Todos</option>
                        @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ request('anio') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                    <select name="periodo" class="w-full rounded-lg border-gray-300">
                        <option value="">Todos</option>
                        <option value="Verano" {{ request('periodo') == 'Verano' ? 'selected' : '' }}>Verano</option>
                        <option value="Invierno" {{ request('periodo') == 'Invierno' ? 'selected' : '' }}>Invierno</option>
                        <option value="Anual" {{ request('periodo') == 'Anual' ? 'selected' : '' }}>Anual</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select name="estado" class="w-full rounded-lg border-gray-300">
                        <option value="">Todos</option>
                        <option value="activa" {{ request('estado') == 'activa' || !request()->has('estado') ? 'selected' : '' }}>Activa</option>
                        <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                        <option value="cerrada" {{ request('estado') == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de Comisiones -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Comisiones ({{ $comisiones->total() }})</h2>
        </div>
        <div class="p-6">
            @if($comisiones->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($comisiones as $comision)
                <a href="{{ route('asistencias.comision.materias', $comision) }}" 
                   class="block border-2 border-gray-200 rounded-lg p-6 hover:shadow-xl hover:border-blue-500 transition-all duration-200 group">
                    
                    <!-- Header Comisión -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800 group-hover:text-blue-600">{{ $comision->codigo }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $comision->nombre }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    @if($comision->estado == 'activa') bg-green-100 text-green-800
                                    @elseif($comision->estado == 'finalizada') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($comision->estado) }}
                        </span>
                    </div>

                    <!-- Información -->
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>{{ $comision->docente->name ?? 'Sin docente' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>{{ $comision->inscripciones->count() }} alumnos</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span>{{ $comision->materias->count() }} materias</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ $comision->anio }} - {{ $comision->periodo }} - {{ $comision->turno }}</span>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-200">
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Asistencia</p>
                            <p class="text-lg font-bold 
                                @if($comision->promedio_asistencia >= 75) text-green-600
                                @elseif($comision->promedio_asistencia >= 50) text-yellow-600
                                @else text-red-600 @endif">
                                {{ round($comision->promedio_asistencia, 1) }}%
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500">En Riesgo</p>
                            <p class="text-lg font-bold text-red-600">{{ $comision->alumnos_en_riesgo }}</p>
                        </div>
                    </div>

                    <!-- Arrow Icon -->
                    <div class="mt-4 flex items-center justify-center text-gray-400 group-hover:text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-6">
                {{ $comisiones->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No hay comisiones</h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if(isset($esDocente) && $esDocente)
                    No tienes comisiones asignadas actualmente.
                    @else
                    No hay comisiones que cumplan con los filtros seleccionados.
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection