@extends('layouts.app')

@section('title', 'Importar Inscripciones')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Importar Inscripciones Masivas</h1>
        <p class="text-gray-600 mt-1">Importa alumnos desde el sistema de preinscripción al curso de ingreso</p>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
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
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <div>
                <h3 class="text-sm font-medium text-red-800 mb-2">Por favor, corrige los siguientes errores:</h3>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- PASO 1: Filtrar alumnos --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-utn-blue/10 text-utn-blue-dark font-bold text-sm">1</span>
            <h2 class="text-lg font-semibold text-gray-800">Filtrar Alumnos</h2>
        </div>
        <p class="text-sm text-gray-500 mb-4 ml-11">Filtra los alumnos por sus datos de preinscripción.</p>

        <form action="{{ route('inscripciones.importar.show') }}" method="GET" class="ml-11">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <div class="lg:col-span-2">
                    <label for="buscar" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" placeholder="Nombre o DNI..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>
                <div>
                    <label for="modalidad" class="block text-sm font-medium text-gray-700 mb-1">Modalidad</label>
                    <select name="modalidad" id="modalidad" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        <option value="">Todas</option>
                        @foreach($modalidades as $modalidad)
                            <option value="{{ $modalidad }}" {{ request('modalidad') == $modalidad ? 'selected' : '' }}>{{ $modalidad }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="turno_ingreso" class="block text-sm font-medium text-gray-700 mb-1">Turno</label>
                    <select name="turno_ingreso" id="turno_ingreso" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        <option value="">Todos</option>
                        @foreach($turnos as $turno)
                            <option value="{{ $turno }}" {{ request('turno_ingreso') == $turno ? 'selected' : '' }}>{{ $turno }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="anio_ingreso" class="block text-sm font-medium text-gray-700 mb-1">Año Ingreso</label>
                    <select name="anio_ingreso" id="anio_ingreso" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        <option value="">Todos</option>
                        @foreach($aniosDisponibles as $anio)
                            <option value="{{ $anio }}" {{ request('anio_ingreso') == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-utn-blue-darker text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['buscar', 'modalidad', 'turno_ingreso', 'anio_ingreso', 'incluir_incompletos']))
                    <a href="{{ route('inscripciones.importar.show') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                        Limpiar
                    </a>
                    @endif
                </div>
            </div>

            {{-- Checkbox para incluir incompletos --}}
            <div class="mt-4">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="incluir_incompletos" value="1" {{ request('incluir_incompletos') ? 'checked' : '' }}
                        class="h-4 w-4 text-utn-blue-dark border-gray-300 rounded focus:ring-indigo-500"
                        onchange="this.form.submit()">
                    <span class="ml-2 text-sm text-gray-700">Incluir alumnos con formulario incompleto</span>
                </label>
            </div>
        </form>

        @if(request()->hasAny(['buscar', 'modalidad', 'turno_ingreso', 'anio_ingreso']))
        <div class="mt-3 ml-11 flex flex-wrap gap-2 items-center">
            <span class="text-xs text-gray-500">Filtros activos:</span>
            @if(request('buscar'))
            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-utn-blue/10 text-utn-dark rounded">
                "{{ request('buscar') }}"
            </span>
            @endif
            @if(request('modalidad'))
            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-utn-blue/10 text-utn-dark rounded">
                {{ request('modalidad') }}
            </span>
            @endif
            @if(request('turno_ingreso'))
            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-utn-blue/10 text-utn-dark rounded">
                Turno: {{ request('turno_ingreso') }}
            </span>
            @endif
            @if(request('anio_ingreso'))
            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-utn-blue/10 text-utn-dark rounded">
                Año: {{ request('anio_ingreso') }}
            </span>
            @endif
        </div>
        @endif
    </div>

    {{-- Alerta de reinscripciones detectadas --}}
    @if($cantidadReinscripciones > 0)
    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-amber-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-amber-800">
                    {{ $cantidadReinscripciones }} alumno(s) con reinscripción detectada
                </h3>
                <p class="text-sm text-amber-700 mt-1">
                    Los alumnos marcados con <span class="inline-flex items-center px-1.5 py-0.5 text-xs font-semibold rounded bg-amber-100 text-amber-800">Reinscripción</span>
                    ya tuvieron inscripciones canceladas en años anteriores. Al importarlos, se creará una nueva trayectoria preservando el historial previo.
                </p>
                <button type="button" onclick="document.getElementById('modal-reinscripciones').classList.remove('hidden')"
                    class="mt-2 text-sm font-medium text-amber-800 underline hover:text-amber-900">
                    Ver detalle de reinscripciones
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Formulario de importación --}}
    <form action="{{ route('inscripciones.importar') }}" method="POST" id="form-importar">
        @csrf

        {{-- PASO 2: Seleccionar alumnos --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-utn-blue/10 text-utn-blue-dark font-bold text-sm">2</span>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Seleccionar Alumnos</h2>
                            <p class="text-sm text-gray-500">
                                {{ $alumnosDisponibles->total() }} alumnos disponibles
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" id="seleccionar_pagina" class="h-4 w-4 text-utn-blue-dark border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                            <span class="ml-2 text-sm text-gray-700">Seleccionar página</span>
                        </label>
                        @if($alumnosDisponibles->total() > $alumnosDisponibles->perPage())
                        <button type="button" id="btn-seleccionar-todos"
                            class="px-3 py-1 text-xs font-medium bg-utn-blue-darker text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Seleccionar todos ({{ $alumnosDisponibles->total() }})
                        </button>
                        @endif
                        <span id="contador_seleccionados" class="px-3 py-1 bg-utn-blue/10 text-utn-dark text-sm font-semibold rounded-full">
                            0 seleccionados
                        </span>
                    </div>
                    {{-- Campo oculto para importar todos --}}
                    <input type="hidden" name="importar_todos" id="importar_todos" value="0">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                <span class="sr-only">Seleccionar</span>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumno</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modalidad</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turno Ingreso</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año Ingreso</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tabla-alumnos">
                        @forelse($alumnosDisponibles as $alumno)
                        @php
                        $acadDatos = $alumno->academicoDatos->first();
                        $espId = $acadDatos?->especialidad_id;
                        $espNombre = $espId && isset($especialidades[$espId]) ? $especialidades[$espId]->nombre : null;
                        $esReinscripcion = isset($reinscripciones[$alumno->id]);
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer fila-alumno {{ $esReinscripcion ? 'bg-amber-50/50' : '' }}" data-id="{{ $alumno->id }}">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="person_ids[]" value="{{ $alumno->id }}"
                                    class="checkbox-alumno h-4 w-4 text-utn-blue-dark border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-9 w-9">
                                        <div class="h-9 w-9 rounded-full {{ $esReinscripcion ? 'bg-amber-100' : 'bg-utn-blue/10' }} flex items-center justify-center">
                                            <span class="{{ $esReinscripcion ? 'text-amber-700' : 'text-utn-blue-dark' }} font-semibold text-xs">
                                                {{ strtoupper(substr($alumno->nombre ?? '', 0, 1)) }}{{ strtoupper(substr($alumno->apellido ?? '', 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $alumno->apellido }}, {{ $alumno->nombre }}
                                            @if($esReinscripcion)
                                            <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 text-xs font-semibold rounded bg-amber-100 text-amber-800" title="Inscripción cancelada en: {{ collect($reinscripciones[$alumno->id])->pluck('anio_ingreso')->join(', ') }}">
                                                Reinscripción
                                            </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($alumno->email, 25) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ $alumno->documento }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($espNombre)
                                <span class="text-sm text-gray-900">{{ Str::limit($espNombre, 20) }}</span>
                                @else
                                <span class="text-xs text-red-500">Sin datos</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ $acadDatos?->modalidad ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ $acadDatos?->turno_ingreso ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-utn-blue/10 text-utn-dark">
                                    {{ $acadDatos?->ingreso_carrera ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $alumno->formularioDato?->estado === 'Completo' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $alumno->formularioDato?->estado ?? 'Sin datos' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="mt-2 font-medium">No hay alumnos disponibles</p>
                                @if(request()->hasAny(['fecha_desde', 'fecha_hasta', 'buscar']))
                                <p class="text-sm">Prueba ajustando los filtros de búsqueda.</p>
                                @else
                                <p class="text-sm">Todos los alumnos ya tienen inscripción activa para este año.</p>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($alumnosDisponibles->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $alumnosDisponibles->withQueryString()->links() }}
            </div>
            @endif
        </div>

        {{-- Botones de acción --}}
        <div class="mt-6 flex justify-between items-center">
            <a href="{{ route('inscripciones.index') }}"
                class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al listado
            </a>
            <button type="button" id="btn-importar"
                class="px-6 py-2.5 bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span id="btn-texto">Importar Seleccionados</span>
            </button>
        </div>
    </form>
</div>

{{-- Modal de confirmación --}}
<div id="modal-confirmar" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Fondo oscuro --}}
        <div id="modal-overlay" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        {{-- Centrador --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Contenido del modal --}}
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Confirmar importación
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            ¿Estás seguro de que deseas importar <span id="modal-cantidad" class="font-semibold text-green-600">0</span> alumno(s)?
                            Se crearán nuevas inscripciones pendientes de validación.
                        </p>
                    </div>
                    <div class="mt-3 bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">
                            Todos los datos (especialidad, modalidad, turnos, año de ingreso) se tomarán automáticamente de la preinscripción de cada alumno.
                        </p>
                    </div>
                    @if($cantidadReinscripciones > 0)
                    <div class="mt-3 bg-amber-50 rounded-lg p-3 border border-amber-200">
                        <p class="text-xs text-amber-800 font-medium">
                            <svg class="w-3.5 h-3.5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Algunos alumnos seleccionados son reinscripciones. Se creará una nueva trayectoria preservando el historial anterior.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" id="btn-confirmar-importar"
                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-700 text-base font-medium text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto sm:text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Sí, importar
                </button>
                <button type="button" id="btn-cancelar-modal"
                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de detalle de reinscripciones --}}
@if($cantidadReinscripciones > 0)
<div id="modal-reinscripciones" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-reinscripciones-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('modal-reinscripciones').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-reinscripciones-title">
                        Alumnos con reinscripción ({{ $cantidadReinscripciones }})
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Estos alumnos ya tuvieron inscripciones canceladas. Al importarlos se crea una nueva inscripción como nueva trayectoria, preservando el historial.
                    </p>
                    <div class="mt-4 max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Alumno</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">DNI</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Inscripciones canceladas</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($alumnosDisponibles as $alumno)
                                    @if(isset($reinscripciones[$alumno->id]))
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-900">
                                            {{ $alumno->apellido }}, {{ $alumno->nombre }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-gray-600">
                                            {{ $alumno->documento }}
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($reinscripciones[$alumno->id] as $inscAnterior)
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded bg-red-100 text-red-800">
                                                    {{ $inscAnterior['anio_ingreso'] }}
                                                    @if($inscAnterior['especialidad_nombre'])
                                                    - {{ Str::limit($inscAnterior['especialidad_nombre'], 15) }}
                                                    @endif
                                                </span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="document.getElementById('modal-reinscripciones').classList.add('hidden')"
                    class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectPagina = document.getElementById('seleccionar_pagina');
        const checkboxes = document.querySelectorAll('.checkbox-alumno');
        const contador = document.getElementById('contador_seleccionados');
        const btnImportar = document.getElementById('btn-importar');
        const btnTexto = document.getElementById('btn-texto');
        const filas = document.querySelectorAll('.fila-alumno');

        // Modal elements
        const modal = document.getElementById('modal-confirmar');
        const modalOverlay = document.getElementById('modal-overlay');
        const btnConfirmar = document.getElementById('btn-confirmar-importar');
        const btnCancelar = document.getElementById('btn-cancelar-modal');
        const modalCantidad = document.getElementById('modal-cantidad');
        const formImportar = document.getElementById('form-importar');

        // Función para actualizar el contador y estado del botón
        function actualizarEstado() {
            const seleccionados = document.querySelectorAll('.checkbox-alumno:checked').length;
            contador.textContent = seleccionados + ' seleccionados';
            btnImportar.disabled = seleccionados === 0;
            btnTexto.textContent = seleccionados > 0 ?
                'Importar ' + seleccionados + ' Alumno' + (seleccionados > 1 ? 's' : '') :
                'Importar Seleccionados';

            // Actualizar estado del checkbox "seleccionar página"
            if (checkboxes.length > 0) {
                const todosSeleccionados = seleccionados === checkboxes.length;
                selectPagina.checked = todosSeleccionados;
                selectPagina.indeterminate = seleccionados > 0 && !todosSeleccionados;
            }
        }

        // Seleccionar/deseleccionar toda la página
        if (selectPagina) {
            selectPagina.addEventListener('change', function() {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = selectPagina.checked;
                });
                actualizarEstado();
            });
        }

        // Evento en cada checkbox individual
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', actualizarEstado);
        });

        // Click en la fila selecciona el checkbox
        filas.forEach(function(fila) {
            fila.addEventListener('click', function(e) {
                // No hacer nada si se clickeó directamente en el checkbox
                if (e.target.type === 'checkbox') return;

                var checkbox = this.querySelector('.checkbox-alumno');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    actualizarEstado();
                }
            });
        });

        // Estado inicial
        actualizarEstado();

        // Funciones del modal
        function abrirModal() {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function cerrarModal() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Función para mostrar error en un campo
        function mostrarErrorCampo(campo, mensaje) {
            // Remover error previo si existe
            var errorPrevio = campo.parentElement.querySelector('.error-mensaje');
            if (errorPrevio) errorPrevio.remove();

            // Agregar clase de error al campo
            campo.classList.add('border-red-500', 'ring-2', 'ring-red-200');

            // Crear mensaje de error
            var errorDiv = document.createElement('p');
            errorDiv.className = 'error-mensaje text-red-600 text-xs mt-1 flex items-center gap-1';
            errorDiv.innerHTML = '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>' + mensaje;
            campo.parentElement.appendChild(errorDiv);

            // Scroll suave al campo
            campo.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            campo.focus();

            // Remover error al cambiar el valor
            campo.addEventListener('change', function limpiarError() {
                campo.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                var error = campo.parentElement.querySelector('.error-mensaje');
                if (error) error.remove();
                campo.removeEventListener('change', limpiarError);
            }, {
                once: true
            });
        }

        // Evento del botón importar - validar y abrir modal
        btnImportar.addEventListener('click', function() {
            var seleccionados = document.querySelectorAll('.checkbox-alumno:checked').length;

            // Validar selección de alumnos
            if (seleccionados === 0) {
                // Scroll a la tabla de alumnos
                document.getElementById('tabla-alumnos').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // Mostrar mensaje temporal
                var tablaContainer = document.getElementById('tabla-alumnos').closest('.bg-white');
                var alertExistente = tablaContainer.querySelector('.alerta-seleccion');
                if (!alertExistente) {
                    var alerta = document.createElement('div');
                    alerta.className = 'alerta-seleccion bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4';
                    alerta.innerHTML = '<div class="flex items-center"><svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><span class="text-sm text-yellow-700">Debes seleccionar al menos un alumno para importar</span></div>';
                    tablaContainer.querySelector('.p-4').insertBefore(alerta, tablaContainer.querySelector('.p-4').firstChild);

                    // Remover después de 5 segundos
                    setTimeout(function() {
                        alerta.remove();
                    }, 5000);
                }
                return;
            }

            // Todo válido - actualizar información en el modal
            modalCantidad.textContent = seleccionados;

            abrirModal();
        });

        // Confirmar importación
        btnConfirmar.addEventListener('click', function() {
            cerrarModal();
            formImportar.submit();
        });

        // Cerrar modal
        btnCancelar.addEventListener('click', cerrarModal);
        modalOverlay.addEventListener('click', cerrarModal);

        // Cerrar con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                cerrarModal();
            }
        });

        // Botón "Seleccionar todos" (todas las páginas)
        const btnSeleccionarTodos = document.getElementById('btn-seleccionar-todos');
        const inputImportarTodos = document.getElementById('importar_todos');
        let todosSeleccionados = false;

        if (btnSeleccionarTodos) {
            btnSeleccionarTodos.addEventListener('click', function() {
                todosSeleccionados = !todosSeleccionados;

                if (todosSeleccionados) {
                    // Seleccionar todos
                    inputImportarTodos.value = '1';
                    checkboxes.forEach(cb => cb.checked = true);
                    selectPagina.checked = true;
                    btnSeleccionarTodos.textContent = 'Deseleccionar todos';
                    btnSeleccionarTodos.classList.remove('bg-utn-blue-darker', 'hover:bg-indigo-700');
                    btnSeleccionarTodos.classList.add('bg-red-600', 'hover:bg-red-700');
                    contador.textContent = '{{ $alumnosDisponibles->total() }} seleccionados (todos)';
                    btnTexto.textContent = 'Importar {{ $alumnosDisponibles->total() }} Alumnos';
                    btnImportar.disabled = false;
                } else {
                    // Deseleccionar todos
                    inputImportarTodos.value = '0';
                    checkboxes.forEach(cb => cb.checked = false);
                    selectPagina.checked = false;
                    btnSeleccionarTodos.textContent = 'Seleccionar todos ({{ $alumnosDisponibles->total() }})';
                    btnSeleccionarTodos.classList.remove('bg-red-600', 'hover:bg-red-700');
                    btnSeleccionarTodos.classList.add('bg-utn-blue-darker', 'hover:bg-indigo-700');
                    actualizarEstado();
                }
            });
        }

        // Actualizar estado cuando se cambia checkbox individual (deselecciona "todos")
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                if (todosSeleccionados && !this.checked) {
                    todosSeleccionados = false;
                    inputImportarTodos.value = '0';
                    if (btnSeleccionarTodos) {
                        btnSeleccionarTodos.textContent = 'Seleccionar todos ({{ $alumnosDisponibles->total() }})';
                        btnSeleccionarTodos.classList.remove('bg-red-600', 'hover:bg-red-700');
                        btnSeleccionarTodos.classList.add('bg-utn-blue-darker', 'hover:bg-indigo-700');
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection