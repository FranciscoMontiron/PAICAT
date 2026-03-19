@extends('layouts.app')
@section('title', 'Reporte de Desercion')
@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('reportes.index') }}" class="text-utn-blue-dark hover:text-utn-blue-dark flex items-center gap-1 text-sm mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver a reportes
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Desercion y Abandono</h1>
            <p class="text-gray-600 mt-1">Alumnos que dejaron de asistir a comisiones activas</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Posibles desertores</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_desertores'] }}</p>
                    <p class="text-xs text-gray-400">+{{ $stats['dias_limite'] }} dias sin asistir</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Nunca asistieron</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['nunca_asistieron'] }}</p>
                    <p class="text-xs text-gray-400">Inscriptos sin asistencia</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Umbral configurado</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['dias_limite'] }} dias</p>
                    <p class="text-xs text-gray-400">Sin asistencia registrada</p>
                </div>
                <div class="bg-utn-blue/10 p-3 rounded-full">
                    <svg class="w-6 h-6 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('reportes.desercion') }}" class="flex flex-wrap gap-4 items-end">
            <div class="w-36">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dias sin asistir</label>
                <input type="number" name="dias" value="{{ request('dias', 14) }}" min="1" max="365"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Comision</label>
                <select name="comision_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todas</option>
                    @foreach($comisiones as $c)
                        <option value="{{ $c->id }}" {{ request('comision_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                <select name="especialidad_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todas</option>
                    @foreach($especialidades as $e)
                        <option value="{{ $e->id_sysacad }}" {{ request('especialidad_id') == $e->id_sysacad ? 'selected' : '' }}>{{ $e->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">Filtrar</button>
            @if(request()->hasAny(['comision_id', 'especialidad_id']) || request('dias') != 14)
            <a href="{{ route('reportes.desercion') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Limpiar</a>
            @endif
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Comision</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Especialidad</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Ultima asistencia</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Dias ausente</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($query as $alumno)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg {{ $alumno->ultima_fecha ? 'bg-red-100' : 'bg-yellow-100' }} flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold {{ $alumno->ultima_fecha ? 'text-red-600' : 'text-yellow-600' }}">
                                    {{ strtoupper(substr($alumno->apellido, 0, 1) . substr($alumno->nombre, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $alumno->apellido }}, {{ $alumno->nombre }}</p>
                                <span class="text-xs text-gray-400">DNI: {{ $alumno->documento }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600">{{ $alumno->comision }}</td>
                    <td class="px-4 py-4 text-sm text-gray-600">{{ $alumno->especialidad }}</td>
                    <td class="px-4 py-4 text-center">
                        @if($alumno->ultima_fecha)
                        <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($alumno->ultima_fecha)->format('d/m/Y') }}</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Nunca asistio</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($alumno->dias_ausente)
                        @php
                            $color = $alumno->dias_ausente > 30 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                            {{ $alumno->dias_ausente }} dias
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-3 text-gray-500">No se encontraron alumnos con los criterios seleccionados.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($query->hasPages())
    <div class="mt-6">
        {{ $query->links() }}
    </div>
    @endif
</div>
@endsection
