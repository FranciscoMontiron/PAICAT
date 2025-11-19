@extends('layouts.app')
@section('title', 'Pasar Asistencia')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Pasar Asistencia</h1>
                <p class="text-gray-600 mt-2">{{ $comision->codigo }} - {{ $comision->nombre }}</p>
            </div>
            <a href="{{ route('asistencias.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('asistencias.store', $comision) }}" method="POST" id="asistenciaForm">
        @csrf

        <!-- Info y Fecha -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Información de la Clase</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de la Clase *</label>
                        <input type="date" 
                               name="fecha" 
                               value="{{ $fecha }}" 
                               max="{{ date('Y-m-d') }}"
                               class="w-full rounded-lg border-gray-300"
                               required>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Docente</p>
                        <p class="text-gray-900">{{ $comision->docente->name ?? 'Sin asignar' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Total Alumnos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $inscripciones->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de Alumnos -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Listado de Alumnos</h2>
                <div class="flex gap-2">
                    <button type="button" onclick="marcarTodos('presente')" class="bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded text-sm font-medium">
                        Marcar Todos Presentes
                    </button>
                    <button type="button" onclick="marcarTodos('ausente')" class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded text-sm font-medium">
                        Marcar Todos Ausentes
                    </button>
                </div>
            </div>

            @if($inscripciones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Alumno
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Presente
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ausente
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tardanza
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Justificado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Observaciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($inscripciones as $index => $inscripcion)
                                @php
                                    $asistencia = $inscripcion->asistencias->first();
                                    $estadoActual = $asistencia->estado ?? 'presente';
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $inscripcion->alumno->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $inscripcion->alumno->email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="radio" 
                                               name="asistencias[{{ $index }}][estado]" 
                                               value="presente"
                                               class="w-5 h-5 text-green-600 focus:ring-green-500"
                                               {{ $estadoActual == 'presente' ? 'checked' : '' }}
                                               required>
                                        <input type="hidden" name="asistencias[{{ $index }}][inscripcion_id]" value="{{ $inscripcion->id }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="radio" 
                                               name="asistencias[{{ $index }}][estado]" 
                                               value="ausente"
                                               class="w-5 h-5 text-red-600 focus:ring-red-500"
                                               {{ $estadoActual == 'ausente' ? 'checked' : '' }}>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="radio" 
                                               name="asistencias[{{ $index }}][estado]" 
                                               value="tardanza"
                                               class="w-5 h-5 text-yellow-600 focus:ring-yellow-500"
                                               {{ $estadoActual == 'tardanza' ? 'checked' : '' }}>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="radio" 
                                               name="asistencias[{{ $index }}][estado]" 
                                               value="justificado"
                                               class="w-5 h-5 text-blue-600 focus:ring-blue-500"
                                               {{ $estadoActual == 'justificado' ? 'checked' : '' }}>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" 
                                               name="asistencias[{{ $index }}][observaciones]" 
                                               value="{{ $asistencia->observaciones ?? '' }}"
                                               placeholder="Observaciones..."
                                               class="w-full rounded border-gray-300 text-sm">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Botones de Acción -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ $inscripciones->count() }}</span> alumnos inscritos
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('asistencias.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition duration-200 font-medium">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Asistencia
                        </button>
                    </div>
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No hay alumnos inscritos</h3>
                    <p class="mt-2 text-sm text-gray-500">Esta comisión no tiene alumnos inscritos actualmente.</p>
                </div>
            @endif
        </div>
    </form>
</div>

<script>
    function marcarTodos(estado) {
        const radios = document.querySelectorAll(`input[type="radio"][value="${estado}"]`);
        radios.forEach(radio => {
            radio.checked = true;
        });
    }

    // Confirmación antes de enviar
    document.getElementById('asistenciaForm').addEventListener('submit', function(e) {
        const fecha = document.querySelector('input[name="fecha"]').value;
        const confirm = window.confirm(`¿Confirmar asistencia para el día ${fecha}?`);
        if (!confirm) {
            e.preventDefault();
        }
    });
</script>
@endsection

