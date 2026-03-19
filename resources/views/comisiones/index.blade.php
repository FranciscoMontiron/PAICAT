@extends('layouts.app')
@section('title', 'Comisiones')
@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Comisiones</h1>
            <p class="text-gray-600 mt-1">Administra las comisiones del curso de ingreso</p>
        </div>
        <div class="flex items-center space-x-3">
            @if(auth()->user()->hasPermission('comisiones.crear'))
            <a href="{{ route('auto-crear-comisiones.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Auto-crear
            </a>
            @endif
            @if(auth()->user()->hasPermission('comisiones.editar'))
            <a href="{{ route('asignacion-alumnos.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-5 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Asignación Aleatoria
            </a>
            @endif
            @if(auth()->user()->hasPermission('comisiones.crear'))
            <a href="{{ route('comisiones.create') }}" class="bg-green-700 hover:bg-green-800 text-white font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Comisión
            </a>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Comisiones</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-utn-blue/10 p-3 rounded-full">
                    <svg class="w-6 h-6 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Comisiones Activas</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['activas'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Cupos Totales</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['cupos_totales'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Cupos Ocupados</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['cupos_ocupados'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('comisiones.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
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
                        <option value="{{ $clave }}" {{ request('estado') == $clave ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">
                Filtrar
            </button>
            @if(request()->hasAny(['search', 'anio', 'periodo', 'estado']))
                <a href="{{ route('comisiones.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Limpiar
                </a>
            @endif

            {{-- Toggle archivadas --}}
            @if($verArchivadas)
                <a href="{{ route('comisiones.index', request()->except('archivadas')) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Ver activas
                </a>
            @elseif($stats['archivadas'] > 0)
                <a href="{{ route('comisiones.index', array_merge(request()->all(), ['archivadas' => 1])) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-50 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Archivadas ({{ $stats['archivadas'] }})
                </a>
            @endif
        </form>
    </div>

    <!-- Alerta de comisiones sin docente -->
    @if($stats['sin_docente'] > 0)
    <div class="bg-amber-50 border border-amber-300 rounded-lg p-4 mb-6 flex items-center gap-3">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-amber-800">
                {{ $stats['sin_docente'] }} {{ $stats['sin_docente'] === 1 ? 'comision no tiene' : 'comisiones no tienen' }} docente asignado
            </p>
            <p class="text-xs text-amber-600 mt-0.5">Se muestran primero en el listado para facilitar su asignacion.</p>
        </div>
    </div>
    @endif

    <!-- Alerta de conflictos de aula (solo presenciales) -->
    @if($conflictosAula->count() > 0)
    <div class="bg-red-50 border border-red-300 rounded-lg px-4 py-3 mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm">
            <p class="font-semibold text-red-800 mb-1">{{ $conflictosAula->count() }} {{ $conflictosAula->count() === 1 ? 'conflicto' : 'conflictos' }} de aula</p>
            @foreach($conflictosAula as $conflicto)
                <p class="text-red-700">
                    <strong>{{ $conflicto['aula'] }}</strong> &mdash;
                    {{ $conflicto['total'] }} comisiones en turno {{ $conflicto['turno'] ?? 'sin turno' }}, {{ $conflicto['periodo'] }} {{ $conflicto['anio'] }}:
                    <span class="font-medium">{{ $conflicto['nombres'] }}</span>
                </p>
            @endforeach
        </div>
    </div>
    @endif

    @if($verArchivadas)
    <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mb-6 flex items-center gap-3">
        <svg class="w-6 h-6 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-gray-700">Mostrando comisiones archivadas</p>
            <p class="text-xs text-gray-500 mt-0.5">Estas comisiones estan ocultas del listado principal. Podes desarchivarlas para que vuelvan a aparecer.</p>
        </div>
    </div>
    @endif

    <!-- Tabla de Comisiones -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Comisión</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Período</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Cupos</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($comisiones as $comision)
                    @php
                        $sinDocente = !$comision->docente_id && $comision->docentes_activos_count === 0;
                    @endphp
                    <tr class="{{ $sinDocente ? 'bg-amber-50 hover:bg-amber-100' : 'hover:bg-gray-50' }} transition-colors cursor-pointer group" onclick="window.location='{{ route('comisiones.show', $comision) }}'">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg {{ $sinDocente ? 'bg-amber-100' : 'bg-utn-blue/10' }} flex items-center justify-center flex-shrink-0 relative">
                                    @if($sinDocente)
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">{{ $comision->nombre }}</p>
                                        @if($sinDocente)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                                                </svg>
                                                Sin docente
                                            </span>
                                        @endif
                                        @if($idsEnConflicto->contains($comision->id))
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-300" title="Conflicto de aula: otra comision comparte la misma aula, turno y periodo">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Conflicto aula
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-mono bg-gray-100 text-gray-600">{{ $comision->codigo }}</span>
                                        @if($comision->modalidad)
                                            @php
                                                $modalidadColors = [
                                                    'presencial' => 'bg-blue-100 text-blue-700',
                                                    'virtual' => 'bg-purple-100 text-purple-700',
                                                    'semipresencial' => 'bg-teal-100 text-teal-700',
                                                ];
                                                $mColor = $modalidadColors[strtolower($comision->modalidad)] ?? 'bg-gray-100 text-gray-600';
                                            @endphp
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $mColor }}">{{ $comision->modalidad }}</span>
                                        @endif
                                        @if($comision->turno)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-gray-100 text-gray-600">{{ ucfirst($comision->turno) }}</span>
                                        @endif
                                        @if($comision->docente)
                                            <span class="text-xs text-gray-400">· {{ $comision->docente->nombre_completo }}</span>
                                        @endif
                                    </div>
                                    @if($comision->materias->count() > 0)
                                        <p class="text-xs text-gray-400 mt-1 truncate">
                                            {{ $comision->materias->pluck('nombre')->implode(', ') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-utn-blue/10 text-utn-blue-dark">
                                {{ $comision->periodo }} {{ $comision->anio }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($comision->esVirtual() || is_null($comision->cupo_maximo))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    Sin limite
                                </span>
                            @else
                                <div class="flex items-center justify-center gap-2">
                                    <div class="text-right">
                                        <span class="text-sm font-semibold text-gray-700">{{ $comision->cupo_real }}/{{ $comision->cupo_total }}</span>
                                        @if($comision->extracupos_habilitados && $comision->extracupos > 0)
                                            <span class="text-xs text-orange-600 font-medium ml-0.5" title="Cupo base: {{ $comision->cupo_maximo }} + {{ $comision->extracupos }} extracupos">+{{ $comision->extracupos }}</span>
                                        @endif
                                    </div>
                                    @php
                                        $porcentaje = $comision->porcentaje_ocupacion;
                                        $barColor = $porcentaje >= 100 ? 'bg-red-500' : ($porcentaje >= 85 ? 'bg-yellow-500' : 'bg-green-600');
                                    @endphp
                                    <div class="w-12 bg-gray-200 rounded-full h-1.5">
                                        <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ min($porcentaje, 100) }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            @php
                            $estadoClasses = [
                                'activa' => 'bg-green-100 text-green-800',
                                'cerrada' => 'bg-yellow-100 text-yellow-800',
                                'finalizada' => 'bg-utn-blue/10 text-utn-blue-dark',
                                'cancelada' => 'bg-red-100 text-red-800',
                            ];
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $estadoClasses[$comision->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($comision->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('comisiones.show', $comision) }}" class="text-utn-blue-dark hover:text-utn-dark" title="Ver detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if(auth()->user()->hasPermission('comisiones.editar'))
                                <a href="{{ route('comisiones.edit', $comision) }}" class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endif
                                @if(auth()->user()->hasPermission('comisiones.editar'))
                                    @if($comision->estado === 'activa' && !$comision->archivada)
                                        <span class="text-gray-300 cursor-not-allowed" title="No se puede archivar una comision activa">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                            </svg>
                                        </span>
                                    @else
                                        <form action="{{ route('comisiones.toggleArchivar', $comision) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="{{ $comision->archivada ? 'text-green-600 hover:text-green-800' : 'text-gray-500 hover:text-gray-700' }}" title="{{ $comision->archivada ? 'Desarchivar comision' : 'Archivar comision' }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                @if(auth()->user()->hasPermission('comisiones.eliminar'))
                                <form action="{{ route('comisiones.destroy', $comision) }}" method="POST" class="inline" data-confirm="¿Estás seguro de eliminar esta comisión?" data-confirm-type="danger" data-confirm-title="Eliminar comisión" data-confirm-text="Eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-3 text-gray-500">No se encontraron comisiones con los filtros seleccionados.</p>
                            @if(auth()->user()->hasPermission('comisiones.crear'))
                            <div class="mt-4">
                                <a href="{{ route('comisiones.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-green-700 hover:bg-green-800">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Nueva Comisión
                                </a>
                            </div>
                            @endif
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
