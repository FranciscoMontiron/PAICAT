@extends('layouts.app')

@section('title', 'Gestión de Asistencias')

@section('content')
@php $asistenciaMinima = \App\Services\ConfiguracionService::get('asistencia_minima', 75); @endphp
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
                    <svg class="h-5 w-5 text-utn-blue-dark" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-utn-blue-dark">
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
                <div class="bg-utn-blue/10 rounded-full p-3">
                    <svg class="w-8 h-8 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Alumnos</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_alumnos'] ?? 0 }}</p>
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
                    <p class="text-3xl font-bold text-utn-blue-dark">{{ $stats['promedio_asistencia'] }}%</p>
                </div>
                <div class="bg-utn-blue/10 rounded-full p-3">
                    <svg class="w-8 h-8 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('asistencias.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           placeholder="Nombre o código de comisión..."
                           class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                </div>
            </div>
            <div class="w-28">
                <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                <select name="anio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach($aniosDisponibles as $anio)
                        <option value="{{ $anio }}" {{ request('anio') == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                <select name="periodo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach(\App\Models\Comision::getTiposIngreso() as $clave => $label)
                        <option value="{{ $label }}" {{ request('periodo') == $label ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach(\App\Models\Comision::getEstados() as $clave => $label)
                        <option value="{{ $clave }}" {{ request('estado') == $clave || (!request()->has('estado') && $clave == 'activa') ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">
                Filtrar
            </button>
            @if(request()->hasAny(['buscar', 'anio', 'periodo', 'estado']))
                <a href="{{ route('asistencias.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Listado de Comisiones -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Comisión</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Período</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Alumnos</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Materias</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Asistencia</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">En Riesgo</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($comisiones as $comision)
                    @php
                        $alumnosCount = $comision->inscripciones->count();
                        $materiasCount = $comision->materias->count();
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer group" onclick="window.location='{{ route('asistencias.comision.materias', $comision) }}'">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-utn-blue/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">{{ $comision->nombre }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $comision->codigo ?? '' }}
                                        {{ $comision->turno ? '&bull; ' . ucfirst($comision->turno) : '' }}
                                        {{ $comision->modalidad ? '&bull; ' . $comision->modalidad : '' }}
                                        @if($comision->estado != 'activa')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                                @if($comision->estado == 'finalizada') bg-gray-100 text-gray-600
                                                @elseif($comision->estado == 'cerrada') bg-yellow-100 text-yellow-700
                                                @else bg-red-100 text-red-700
                                                @endif">
                                                {{ ucfirst($comision->estado) }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-utn-blue/10 text-utn-blue-dark">
                                {{ $comision->periodo }} {{ $comision->anio }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-sm font-semibold text-gray-700">{{ $alumnosCount }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-sm font-semibold text-gray-700">{{ $materiasCount }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-sm font-bold
                                @if($comision->promedio_asistencia >= $asistenciaMinima) text-green-600
                                @elseif($comision->promedio_asistencia >= 50) text-yellow-600
                                @else text-red-600
                                @endif">
                                {{ round($comision->promedio_asistencia, 1) }}%
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-sm font-semibold {{ $comision->alumnos_en_riesgo > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                {{ $comision->alumnos_en_riesgo }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-utn-blue-dark opacity-0 group-hover:opacity-100 transition-opacity">
                                Abrir
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-3 text-gray-500">
                                @if(isset($esDocente) && $esDocente)
                                    No tienes comisiones asignadas actualmente.
                                @else
                                    No se encontraron comisiones con los filtros seleccionados.
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($comisiones->hasPages())
    <div class="mt-6">
        {{ $comisiones->withQueryString()->links() }}
    </div>
    @endif

</div>
@endsection