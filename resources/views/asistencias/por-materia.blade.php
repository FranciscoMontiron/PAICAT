@extends('layouts.app')
@section('title', 'Asistencia por Materia')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('asistencias.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Asistencia por Materia</h1>
                <p class="text-gray-600">Listado de asistencia filtrado por materia y comision</p>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Filtros</h2>
        </div>
        <form method="GET" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Materia --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Materia</label>
                    <select name="materia_id" id="materiaSelect" class="w-full rounded-lg border-gray-300 text-sm" onchange="this.form.submit()">
                        <option value="">Seleccionar materia...</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}" {{ $materiaId == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }} ({{ $materia->codigo }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Comision (dinámico) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comision</label>
                    <select name="comision_id" class="w-full rounded-lg border-gray-300 text-sm" {{ !$materiaId ? 'disabled' : '' }}>
                        <option value="">Todas las comisiones</option>
                        @foreach($comisionesMateria as $comision)
                            <option value="{{ $comision->id }}" {{ $comisionId == $comision->id ? 'selected' : '' }}>
                                {{ $comision->nombre }} - {{ $comision->turno }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fecha desde --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                    <input type="date" name="fecha_desde" value="{{ $fechaDesde }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>

                {{-- Fecha hasta --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ $fechaHasta }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                    Filtrar
                </button>
                @if($materiaId || $comisionId || $fechaDesde || $fechaHasta)
                    <a href="{{ route('asistencias.por-materia') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm">
                        Limpiar filtros
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($materiaSeleccionada)
        {{-- Info de materia seleccionada --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $materiaSeleccionada->nombre }}</h3>
                    <p class="text-sm text-gray-500">Codigo: {{ $materiaSeleccionada->codigo }} | {{ $comisionesMateria->count() }} comision(es) activa(s)</p>
                </div>
                @if($comisionSeleccionada)
                    <div class="ml-auto px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        {{ $comisionSeleccionada->nombre }} - {{ $comisionSeleccionada->turno }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Estadísticas por alumno --}}
        @if($estadisticasPorAlumno->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Resumen por Alumno</h2>
                    <p class="text-sm text-gray-600">{{ $estadisticasPorAlumno->count() }} alumno(s) con registros de asistencia</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumno</th>
                                @if(!$comisionSeleccionada)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comision</th>
                                @endif
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Presentes</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tardanzas</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ausentes</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Justificados</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">% Asist.</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($estadisticasPorAlumno as $estadistica)
                                <tr class="hover:bg-gray-50 {{ $estadistica['en_riesgo'] ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-medium text-indigo-600">{{ substr($estadistica['alumno_nombre'], 0, 2) }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $estadistica['alumno_nombre'] }}</div>
                                                <div class="text-xs text-gray-500">DNI: {{ $estadistica['alumno_documento'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    @if(!$comisionSeleccionada)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $estadistica['comision'] }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">{{ $estadistica['total'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-green-600 font-medium">{{ $estadistica['presentes'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-yellow-600 font-medium">{{ $estadistica['tardanzas'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-red-600 font-medium">{{ $estadistica['ausentes'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-blue-600 font-medium">{{ $estadistica['justificados'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $estadistica['porcentaje'] >= config('paicat.asistencia_minima', 75) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $estadistica['porcentaje'] }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($estadistica['en_riesgo'])
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                En riesgo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Regular
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Detalle de asistencias --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Detalle de Asistencias</h2>
                    <p class="text-sm text-gray-600">{{ $asistencias->count() }} registro(s) encontrado(s)</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumno</th>
                                @if(!$comisionSeleccionada)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comision</th>
                                @endif
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observaciones</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrado por</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($asistencias->take(100) as $asistencia)
                                @php
                                    $person = $asistencia->inscripcionComision?->inscripcion?->getPerson();
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $asistencia->fecha->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $person ? "{$person->nombre} {$person->apellido}" : 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">DNI: {{ $person?->documento ?? 'N/A' }}</div>
                                    </td>
                                    @if(!$comisionSeleccionada)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $asistencia->inscripcionComision?->comision?->nombre ?? 'N/A' }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $estadoColors = [
                                                'presente' => 'bg-green-100 text-green-800',
                                                'ausente' => 'bg-red-100 text-red-800',
                                                'tardanza' => 'bg-yellow-100 text-yellow-800',
                                                'justificado' => 'bg-blue-100 text-blue-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $estadoColors[$asistencia->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($asistencia->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                        {{ $asistencia->observaciones ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $asistencia->registradoPor?->name ?? 'Sistema' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($asistencias->count() > 100)
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-center text-sm text-gray-500">
                        Mostrando los primeros 100 registros de {{ $asistencias->count() }} total.
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Sin registros de asistencia</h3>
                <p class="mt-2 text-sm text-gray-500">No hay registros de asistencia para los filtros seleccionados.</p>
            </div>
        @endif
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Selecciona una materia</h3>
            <p class="mt-2 text-sm text-gray-500">Selecciona una materia del filtro para ver el listado de asistencia.</p>
        </div>
    @endif
</div>
@endsection
