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

    @if ($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
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
            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold text-sm">1</span>
            <h2 class="text-lg font-semibold text-gray-800">Filtrar Alumnos por Fecha de Registro</h2>
        </div>
        <p class="text-sm text-gray-500 mb-4 ml-11">Filtra los alumnos por el período en que completaron su preinscripción online.</p>

        <form action="{{ route('inscripciones.importar.show') }}" method="GET" class="ml-11">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[180px]">
                    <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>
                <div class="flex-1 min-w-[180px]">
                    <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label for="buscar" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" placeholder="Nombre o DNI..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['fecha_desde', 'fecha_hasta', 'buscar']))
                    <a href="{{ route('inscripciones.importar.show') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                        Limpiar
                    </a>
                    @endif
                </div>
            </div>
        </form>

        @if(request()->hasAny(['fecha_desde', 'fecha_hasta', 'buscar']))
        <div class="mt-3 ml-11 flex flex-wrap gap-2 items-center">
            <span class="text-xs text-gray-500">Filtros:</span>
            @if(request('fecha_desde'))
            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-800 rounded">
                Desde: {{ \Carbon\Carbon::parse(request('fecha_desde'))->format('d/m/Y') }}
            </span>
            @endif
            @if(request('fecha_hasta'))
            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-800 rounded">
                Hasta: {{ \Carbon\Carbon::parse(request('fecha_hasta'))->format('d/m/Y') }}
            </span>
            @endif
            @if(request('buscar'))
            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-800 rounded">
                "{{ request('buscar') }}"
            </span>
            @endif
        </div>
        @endif
    </div>

    {{-- Formulario de importación --}}
    <form action="{{ route('inscripciones.importar') }}" method="POST" id="form-importar">
        @csrf

        {{-- PASO 2: Configuración de inscripción --}}
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold text-sm">2</span>
                <h2 class="text-lg font-semibold text-gray-800">Datos de la Inscripción</h2>
            </div>
            <p class="text-sm text-gray-500 mb-4 ml-11">Estos datos se aplicarán a todos los alumnos que importes.</p>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 ml-11">
                <div>
                    <label for="anio_ingreso" class="block text-sm font-medium text-gray-700 mb-1">Año de Ingreso <span class="text-red-500">*</span></label>
                    <input type="number" name="anio_ingreso" id="anio_ingreso" required
                           value="{{ $anioActual + 1 }}"
                           min="2020" max="2100" step="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="especialidad_id_sysacad" class="block text-sm font-medium text-gray-700 mb-1">Especialidad <span class="text-red-500">*</span></label>
                    <select name="especialidad_id_sysacad" id="especialidad_id_sysacad" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        <option value="">Seleccionar...</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp->id_sysacad }}">{{ $esp->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="modalidad" class="block text-sm font-medium text-gray-700 mb-1">Modalidad <span class="text-red-500">*</span></label>
                    <select name="modalidad" id="modalidad" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        @foreach(\App\Models\Inscripcion::MODALIDADES as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tipo_ingreso" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Ingreso <span class="text-red-500">*</span></label>
                    <select name="tipo_ingreso" id="tipo_ingreso" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        @foreach(\App\Models\Inscripcion::TIPOS_INGRESO as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- PASO 3: Seleccionar alumnos --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold text-sm">3</span>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Seleccionar Alumnos</h2>
                            <p class="text-sm text-gray-500">
                                {{ $alumnosDisponibles->total() }} alumnos disponibles
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" id="seleccionar_pagina" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                            <span class="ml-2 text-sm text-gray-700">Seleccionar página</span>
                        </label>
                        <span id="contador_seleccionados" class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-semibold rounded-full">
                            0 seleccionados
                        </span>
                    </div>
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tabla-alumnos">
                        @forelse($alumnosDisponibles as $alumno)
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer fila-alumno" data-id="{{ $alumno->id }}">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="person_ids[]" value="{{ $alumno->id }}"
                                       class="checkbox-alumno h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-9 w-9">
                                        <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-600 font-semibold text-xs">
                                                {{ strtoupper(substr($alumno->nombre ?? '', 0, 1)) }}{{ strtoupper(substr($alumno->apellido ?? '', 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $alumno->apellido }}, {{ $alumno->nombre }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ $alumno->documento }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ Str::limit($alumno->email, 30) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ $alumno->created_at ? \Carbon\Carbon::parse($alumno->created_at)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $alumno->formularioDato?->estado === 'Completo' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $alumno->formularioDato?->estado ?? 'Sin datos' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al listado
            </a>
            <button type="button" id="btn-importar"
                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
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
                        <p class="text-xs text-gray-600">
                            <strong>Especialidad:</strong> <span id="modal-especialidad" class="text-gray-800">-</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" id="btn-confirmar-importar"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto sm:text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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
    const modalEspecialidad = document.getElementById('modal-especialidad');
    const formImportar = document.getElementById('form-importar');

    // Función para actualizar el contador y estado del botón
    function actualizarEstado() {
        const seleccionados = document.querySelectorAll('.checkbox-alumno:checked').length;
        contador.textContent = seleccionados + ' seleccionados';
        btnImportar.disabled = seleccionados === 0;
        btnTexto.textContent = seleccionados > 0 
            ? 'Importar ' + seleccionados + ' Alumno' + (seleccionados > 1 ? 's' : '')
            : 'Importar Seleccionados';
        
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
        campo.scrollIntoView({ behavior: 'smooth', block: 'center' });
        campo.focus();
        
        // Remover error al cambiar el valor
        campo.addEventListener('change', function limpiarError() {
            campo.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
            var error = campo.parentElement.querySelector('.error-mensaje');
            if (error) error.remove();
            campo.removeEventListener('change', limpiarError);
        }, { once: true });
    }

    // Evento del botón importar - validar y abrir modal
    btnImportar.addEventListener('click', function() {
        var seleccionados = document.querySelectorAll('.checkbox-alumno:checked').length;
        var anioIngreso = document.getElementById('anio_ingreso');
        var especialidad = document.getElementById('especialidad_id_sysacad');
        var modalidad = document.getElementById('modalidad');
        var tipoIngreso = document.getElementById('tipo_ingreso');
        
        // Validar año de ingreso
        if (!anioIngreso.value || anioIngreso.value < 2020) {
            mostrarErrorCampo(anioIngreso, 'Ingresa un año válido');
            return;
        }

        // Validar especialidad
        if (!especialidad.value) {
            mostrarErrorCampo(especialidad, 'Selecciona una especialidad');
            return;
        }

        // Validar modalidad
        if (!modalidad.value) {
            mostrarErrorCampo(modalidad, 'Selecciona una modalidad');
            return;
        }

        // Validar tipo de ingreso
        if (!tipoIngreso.value) {
            mostrarErrorCampo(tipoIngreso, 'Selecciona un tipo de ingreso');
            return;
        }

        // Validar selección de alumnos
        if (seleccionados === 0) {
            // Scroll a la tabla de alumnos
            document.getElementById('tabla-alumnos').scrollIntoView({ behavior: 'smooth', block: 'start' });
            
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
        modalEspecialidad.textContent = especialidad.options[especialidad.selectedIndex].text;
        
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
});
</script>
@endpush
@endsection
