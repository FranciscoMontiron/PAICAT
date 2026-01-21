@extends('layouts.app')
@section('title', 'Trayectorias Estudiantiles')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Trayectorias Estudiantiles</h1>
            <p class="text-gray-600 mt-1">Gestión del historial y seguimiento de estudiantes</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('trayectorias.inactivos') }}" class="inline-flex items-center px-4 py-2 bg-amber-100 text-amber-800 rounded-lg hover:bg-amber-200 transition-colors font-medium text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Ver Inactivos
            </a>
            <a href="{{ route('solicitudes.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                Solicitudes de Cambio
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Inscriptos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Activos</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['activos'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Bajas</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['bajas'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Libres</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['libres'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-amber-100 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                <select name="anio" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Todos</option>
                    @for($y = date('Y'); $y >= date('Y')-2; $y--)
                    <option value="{{ $y }}" {{ request('anio') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado Trayectoria</label>
                <select name="estado_trayectoria" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado_trayectoria') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="pausado" {{ request('estado_trayectoria') == 'pausado' ? 'selected' : '' }}>Pausado</option>
                    <option value="libre" {{ request('estado_trayectoria') == 'libre' ? 'selected' : '' }}>Libre</option>
                    <option value="baja" {{ request('estado_trayectoria') == 'baja' ? 'selected' : '' }}>Baja</option>
                    <option value="aprobado" {{ request('estado_trayectoria') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                Filtrar
            </button>
            @if(request()->hasAny(['anio', 'estado_trayectoria']))
            <a href="{{ route('trayectorias.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">
                Limpiar filtros
            </a>
            @endif
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modalidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Trayectoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condiciones</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($inscripciones as $inscripcion)
                    @php
                    $person = $inscripcion->getPerson();
                    $trayectoria = $inscripcion->trayectoriaActual;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-indigo-600">{{ substr($person->nombre ?? 'N', 0, 1) }}{{ substr($person->apellido ?? 'A', 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $person->nombre ?? 'N/A' }} {{ $person->apellido ?? '' }}</div>
                                    <div class="text-sm text-gray-500">DNI: {{ $person->documento ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $inscripcion->especialidad_nombre ?? 'Sin asignar' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $inscripcion->modalidad == 'Presencial' ? 'bg-blue-100 text-blue-800' : 
                                       ($inscripcion->modalidad == 'Virtual' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $inscripcion->modalidad ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($trayectoria)
                            @php
                            $estadoColors = [
                            'activo' => 'bg-green-100 text-green-800',
                            'pausado' => 'bg-yellow-100 text-yellow-800',
                            'libre' => 'bg-orange-100 text-orange-800',
                            'baja' => 'bg-red-100 text-red-800',
                            'aprobado' => 'bg-emerald-100 text-emerald-800',
                            'desaprobado' => 'bg-red-100 text-red-800',
                            ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $estadoColors[$trayectoria->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($trayectoria->estado) }}
                            </span>
                            @else
                            <span class="text-gray-400 text-sm">Sin registro</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($inscripcion->condicionesParticulares && $inscripcion->condicionesParticulares->where('activa', true)->count() > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                {{ $inscripcion->condicionesParticulares->where('activa', true)->count() }} activa(s)
                            </span>
                            @else
                            <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('trayectorias.show', $inscripcion) }}" class="text-indigo-600 hover:text-indigo-900">
                                Ver detalle →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No se encontraron inscripciones con los filtros aplicados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($inscripciones->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $inscripciones->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection