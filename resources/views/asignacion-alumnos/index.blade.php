@extends('layouts.app')
@section('title', 'Asignación Aleatoria de Alumnos')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Asignación Aleatoria de Alumnos</h1>
            <p class="text-gray-600 mt-1">Asigna estudiantes a comisiones respetando cupos y restricciones de carrera</p>
        </div>
        <a href="{{ route('comisiones.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver a Comisiones
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Alumnos Sin Asignar</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $alumnosSinAsignar }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Cupos Totales Disponibles</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalCuposDisponibles }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Comisiones Disponibles</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $comisiones->count() }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
        <p class="font-bold">Éxito</p>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
        <p class="font-bold">Error</p>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Formulario de selección -->
    <form method="POST" action="{{ route('asignacion-alumnos.preview') }}" id="formAsignacion">
        @csrf

        <!-- Opciones de Asignación -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Opciones de Asignación</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Distribuir por Carrera -->
                <div class="flex items-start gap-3">
                    <input type="checkbox" name="distribuir_por_carrera" id="distribuir_por_carrera" value="1"
                        class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <div>
                        <label for="distribuir_por_carrera" class="font-medium text-gray-700 cursor-pointer">
                            Distribuir equitativamente por carrera
                        </label>
                        <p class="text-sm text-gray-500">
                            Asegura que cada comisión tenga representación de todas las carreras en proporción equitativa.
                        </p>
                    </div>
                </div>

                <!-- Filtrar por Especialidad -->
                <div>
                    <label for="filtrar_especialidad" class="block font-medium text-gray-700 mb-2">
                        Filtrar por carrera específica (opcional)
                    </label>
                    <select name="filtrar_especialidad" id="filtrar_especialidad"
                        class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500">
                        <option value="">Todas las carreras</option>
                        @foreach($especialidades ?? [] as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">
                        Solo asignar alumnos de una carrera específica.
                    </p>
                </div>
            </div>

            @if(isset($alumnosPorCarrera) && $alumnosPorCarrera->count() > 0)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Alumnos sin asignar por carrera:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($alumnosPorCarrera as $espId => $info)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm
                        {{ $espId ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $info['nombre'] }}: <strong class="ml-1">{{ $info['cantidad'] }}</strong>
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Tabla de Comisiones -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Seleccionar Comisiones</h2>
                    <div class="flex items-center space-x-2">
                        <button type="button" id="selectAll" class="text-sm text-blue-600 hover:text-blue-800">
                            Seleccionar todas
                        </button>
                        <span class="text-gray-300">|</span>
                        <button type="button" id="deselectAll" class="text-sm text-gray-600 hover:text-gray-800">
                            Deseleccionar todas
                        </button>
                    </div>
                </div>
            </div>

            @if($comisiones->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materias</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modalidad / Periodo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cupos</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($comisiones as $comision)
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="toggleCheckbox({{ $comision->id }})">
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <input type="checkbox" name="comisiones[]" value="{{ $comision->id }}"
                                    id="comision_{{ $comision->id }}"
                                    class="comision-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-medium text-gray-900">{{ $comision->codigo }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $comision->nombre }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @foreach($comision->materias->take(2) as $materia)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 mr-1 mb-1">
                                    {{ $materia->nombre }}
                                </span>
                                @endforeach
                                @if($comision->materias->count() > 2)
                                <span class="text-xs text-gray-500">+{{ $comision->materias->count() - 2 }} más</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($comision->turno)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ strtolower($comision->turno) === 'mañana' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $comision->turno }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $comision->modalidad }}</div>
                                <div class="text-xs text-gray-500">{{ $comision->periodo }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($comision->docente)
                                <div class="text-sm text-gray-900">{{ $comision->docente->nombre_completo }}</div>
                                @else
                                <span class="text-sm text-gray-400 italic">Sin asignar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium {{ $comision->cupos_disponibles > 10 ? 'text-green-600' : ($comision->cupos_disponibles > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $comision->cupos_disponibles }} disponibles
                                    </span>
                                    <span class="text-sm text-gray-500 ml-1">({{ $comision->cupo_real }}/{{ $comision->cupo_maximo }})</span>
                                </div>
                                <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ min($comision->porcentaje_ocupacion, 100) }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay comisiones disponibles</h3>
                <p class="mt-1 text-sm text-gray-500">Todas las comisiones activas tienen sus cupos llenos.</p>
            </div>
            @endif
        </div>

        <!-- Botón de acción -->
        @if($comisiones->count() > 0)
        <div class="flex justify-end space-x-4">
            <span id="selectionCount" class="text-gray-600 py-2">0 comisiones seleccionadas</span>
            <button type="submit" id="btnPreview" disabled
                class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Vista Previa de Asignación
            </button>
        </div>
        @endif
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.comision-checkbox');
        const checkAll = document.getElementById('checkAll');
        const selectAllBtn = document.getElementById('selectAll');
        const deselectAllBtn = document.getElementById('deselectAll');
        const btnPreview = document.getElementById('btnPreview');
        const selectionCount = document.getElementById('selectionCount');

        function updateUI() {
            const checked = document.querySelectorAll('.comision-checkbox:checked').length;
            const total = checkboxes.length;

            selectionCount.textContent = checked + ' comisiones seleccionadas';
            btnPreview.disabled = checked === 0;
            checkAll.checked = checked === total && total > 0;
            checkAll.indeterminate = checked > 0 && checked < total;
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateUI));

        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateUI();
        });

        selectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = true);
            updateUI();
        });

        deselectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = false);
            updateUI();
        });
    });

    function toggleCheckbox(id) {
        const cb = document.getElementById('comision_' + id);
        cb.checked = !cb.checked;
        cb.dispatchEvent(new Event('change'));
    }
</script>
@endsection