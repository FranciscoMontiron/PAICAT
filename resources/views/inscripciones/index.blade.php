@extends('layouts.app')

@section('title', 'Inscripciones')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Módulo de Inscripciones</h1>
            <p class="text-gray-600 mt-1">Gestiona las inscripciones al curso de ingreso</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(auth()->user()->hasPermission('inscripciones.crear'))
            <a href="{{ route('inscripciones.importar.show') }}"
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Importar Masivo
            </a>
            <a href="{{ route('inscripciones.create') }}"
               class="bg-utn-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Inscripción
            </a>
            @endif
            <a href="{{ route('inscripciones.exportar', request()->query()) }}"
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exportar
            </a>
        </div>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form action="{{ route('inscripciones.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <label for="buscar" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}"
                       placeholder="Nombre, DNI o email..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
            </div>

            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" id="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach(\App\Models\Inscripcion::ESTADOS as $key => $value)
                        <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="anio_ingreso" class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                <select name="anio_ingreso" id="anio_ingreso" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach($aniosDisponibles as $anio)
                        <option value="{{ $anio }}" {{ request('anio_ingreso') == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="especialidad" class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                <select name="especialidad" id="especialidad" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                    <option value="">Todas</option>
                    @foreach($especialidades as $esp)
                        <option value="{{ $esp->id_sysacad }}" {{ request('especialidad') == $esp->id_sysacad ? 'selected' : '' }}>{{ $esp->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-utn-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                <a href="{{ route('inscripciones.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabla de inscripciones --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alumno</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Especialidad</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Año</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-32">Estado</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-12" title="DNI / Título / Analítico">Docs</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-28">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($inscripciones as $inscripcion)
                @php
                    $persona = $personas->get($inscripcion->person_id);
                @endphp
                <tr class="hover:bg-gray-50">
                    {{-- Alumno con avatar, nombre, email y DNI --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-600 font-semibold text-xs">
                                    {{ $persona ? strtoupper(substr($persona->nombre ?? '', 0, 1)) . strtoupper(substr($persona->apellido ?? '', 0, 1)) : '??' }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-900 truncate">
                                    {{ $persona ? ($persona->apellido . ', ' . $persona->nombre) : 'No encontrado' }}
                                </div>
                                <div class="text-xs text-gray-500 truncate">{{ $persona?->email ?? '-' }}</div>
                                <div class="text-xs text-gray-400">DNI: {{ $persona?->documento ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    {{-- Especialidad y modalidad --}}
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900">{{ $inscripcion->especialidad_nombre ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ $inscripcion->modalidad }}</div>
                    </td>
                    {{-- Año --}}
                    <td class="px-4 py-3 text-center">
                        <span class="text-sm font-semibold text-gray-700">{{ $inscripcion->anio_ingreso }}</span>
                    </td>
                    {{-- Estado --}}
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                            @if($inscripcion->estado === 'pendiente') bg-yellow-100 text-yellow-800
                            @elseif($inscripcion->estado === 'documentacion_ok') bg-blue-100 text-blue-800
                            @elseif($inscripcion->estado === 'confirmado') bg-green-100 text-green-800
                            @elseif($inscripcion->estado === 'cancelado') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ \App\Models\Inscripcion::ESTADOS[$inscripcion->estado] ?? $inscripcion->estado }}
                        </span>
                    </td>
                    {{-- Documentación: 3 puntitos verticales --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-col items-center gap-1" title="DNI: {{ $inscripcion->dni_validado ? 'OK' : 'Pendiente' }} | Título: {{ $inscripcion->titulo_validado ? 'OK' : 'Pendiente' }} | Analítico: {{ $inscripcion->analitico_validado ? 'OK' : 'Pendiente' }}">
                            <span class="w-2.5 h-2.5 rounded-full {{ $inscripcion->dni_validado ? 'bg-green-500' : 'bg-gray-300' }}" title="DNI"></span>
                            <span class="w-2.5 h-2.5 rounded-full {{ $inscripcion->titulo_validado ? 'bg-green-500' : 'bg-gray-300' }}" title="Título"></span>
                            <span class="w-2.5 h-2.5 rounded-full {{ $inscripcion->analitico_validado ? 'bg-green-500' : 'bg-gray-300' }}" title="Analítico"></span>
                        </div>
                    </td>
                    {{-- Acciones --}}
                    <td class="px-4 py-3">
                        <div class="flex justify-center gap-1">
                            <a href="{{ route('inscripciones.show', $inscripcion) }}" 
                               class="p-1.5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors" 
                               title="Ver detalle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @if(auth()->user()->hasPermission('inscripciones.editar') && $inscripcion->puedeModificarse())
                            <a href="{{ route('inscripciones.edit', $inscripcion) }}" 
                               class="p-1.5 rounded bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors" 
                               title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            @endif
                            @if($inscripcion->estado === 'pendiente' || $inscripcion->estado === 'documentacion_ok')
                            <a href="{{ route('inscripciones.show', $inscripcion) }}#documentacion" 
                               class="p-1.5 rounded bg-green-100 text-green-600 hover:bg-green-200 transition-colors" 
                               title="Validar documentación">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-2">No hay inscripciones registradas</p>
                        @if(auth()->user()->hasPermission('inscripciones.crear'))
                        <a href="{{ route('inscripciones.create') }}" class="mt-2 inline-block text-utn-blue hover:text-blue-800 text-sm">
                            Registrar primera inscripción
                        </a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        @if($inscripciones->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            {{ $inscripciones->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
