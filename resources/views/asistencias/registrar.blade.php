@extends('layouts.app')
@section('title','Registrar Asistencia')
@section('content')
<div class="container mx-auto p-4">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
        <a href="{{ route('asistencias.index') }}" class="hover:text-blue-600">Asistencias</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('asistencias.comision', $comision->id) }}" class="hover:text-blue-600">{{ $comision->nombre }}</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-800 font-medium">Registrar Asistencia</span>
    </div>

    <!-- Mensajes de éxito -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Errores de validación -->
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
            <div class="flex items-start">
                <svg class="h-6 w-6 text-red-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-red-800 font-semibold mb-2">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white border-b border-gray-200 shadow-sm sm:rounded-lg">
        <div class="p-6">
            
            <!-- Encabezado -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-utn-blue mb-1">Registrar Asistencia</h1>
                    <p class="text-gray-600">{{ $comision->nombre }} - {{ $comision->turno }}</p>
                </div>
                <a href="{{ route('asistencias.comision', $comision->id) }}" 
                   class="flex items-center gap-2 text-gray-600 hover:text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>
            </div>

            <form method="POST" action="{{ route('asistencias.guardar', $comision->id) }}" id="asistenciaForm">
                @csrf
                
                <!-- Selector de fecha -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <label for="fecha" class="block text-sm font-semibold text-blue-900 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Fecha de Asistencia:
                    </label>
                    <div class="flex gap-3 items-center">
                        <input type="date" 
                               name="fecha" 
                               id="fecha" 
                               value="{{ old('fecha', $fecha) }}" 
                               max="{{ today()->format('Y-m-d') }}"
                               class="border border-blue-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               onchange="this.form.submit()">
                        <span class="text-sm text-blue-700">
                            @if($fecha === today()->format('Y-m-d'))
                                <span class="bg-blue-100 px-3 py-1 rounded-full font-semibold">📅 Hoy</span>
                            @else
                                Modificando asistencia anterior
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Tabla de asistencias -->
                <div class="overflow-x-auto mb-6">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Listado de Alumnos ({{ $inscripciones->count() }})
                        </h3>
                        
                        <!-- Botones de selección rápida -->
                        <div class="flex gap-2">
                            <button type="button" onclick="marcarTodos('presente')" 
                                    class="px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 text-sm font-medium transition">
                                ✓ Todos Presentes
                            </button>
                            <button type="button" onclick="marcarTodos('ausente')" 
                                    class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium transition">
                                ✗ Todos Ausentes
                            </button>
                        </div>
                    </div>

                    <table class="w-full table-auto border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-4 py-3 text-left font-semibold text-gray-700 w-1/4">Alumno</th>
                                <th class="border border-gray-300 px-4 py-3 text-center font-semibold text-gray-700 w-16">
                                    <span class="text-green-600">✓</span> Presente
                                </th>
                                <th class="border border-gray-300 px-4 py-3 text-center font-semibold text-gray-700 w-16">
                                    <span class="text-red-600">✗</span> Ausente
                                </th>
                                <th class="border border-gray-300 px-4 py-3 text-center font-semibold text-gray-700 w-16">
                                    <span class="text-yellow-600">⏱</span> Tardanza
                                </th>
                                <th class="border border-gray-300 px-4 py-3 text-center font-semibold text-gray-700 w-16">
                                    <span class="text-blue-600">📝</span> Justificado
                                </th>
                                <th class="border border-gray-300 px-4 py-3 text-left font-semibold text-gray-700">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inscripciones as $i)
                                @php
                                    $asistencia = $asistenciasExistentes[$i->id] ?? null;
                                    $nombreAlumno = $i->academicoDato->user->name ?? $i->alumno->name ?? 'Sin nombre';
                                @endphp
                                <tr class="hover:bg-gray-50 transition" data-row="{{ $loop->index }}">
                                    <td class="border border-gray-300 px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-800">{{ $nombreAlumno }}</span>
                                            @if($i->estaEnRiesgo())
                                                <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full font-semibold">
                                                    ⚠ Riesgo
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    @foreach(['presente', 'ausente', 'tardanza', 'justificado'] as $estado)
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            <input type="radio" 
                                                   name="asistencias[{{ $loop->parent->index }}][estado]" 
                                                   value="{{ $estado }}"
                                                   class="w-5 h-5 cursor-pointer radio-{{ $estado }}"
                                                   data-row="{{ $loop->parent->index }}"
                                                   {{ ($asistencia && $asistencia->estado === $estado) ? 'checked' : '' }}
                                                   required>
                                        </td>
                                    @endforeach
                                    <td class="border border-gray-300 px-4 py-3">
                                        <input type="text" 
                                               name="asistencias[{{ $loop->index }}][observaciones]" 
                                               value="{{ old('asistencias.'.$loop->index.'.observaciones', $asistencia->observaciones ?? '') }}" 
                                               placeholder="Ej: Llegó 10 min tarde"
                                               class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <input type="hidden" 
                                               name="asistencias[{{ $loop->index }}][inscripcion_comision_id]" 
                                               value="{{ $i->id }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <a href="{{ route('asistencias.comision', $comision->id) }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-medium shadow-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Guardar Asistencias
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
    // Función para marcar todos con un estado específico
    function marcarTodos(estado) {
        const radios = document.querySelectorAll(`.radio-${estado}`);
        radios.forEach(radio => {
            radio.checked = true;
        });
    }

    // Validación antes de enviar
    document.getElementById('asistenciaForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('tbody tr');
        let allChecked = true;
        
        rows.forEach((row, index) => {
            const radios = row.querySelectorAll('input[type="radio"]');
            const isChecked = Array.from(radios).some(radio => radio.checked);
            
            if (!isChecked) {
                allChecked = false;
            }
        });
        
        if (!allChecked) {
            e.preventDefault();
            alert('Por favor, marca la asistencia de todos los alumnos antes de guardar.');
            return false;
        }
    });
</script>

@endsection