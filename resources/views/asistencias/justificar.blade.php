@extends('layouts.app')
@section('title', 'Justificar Inasistencias')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Justificar Inasistencias</h1>
                <p class="text-gray-600 mt-2">{{ $inscripcion->alumno->name }}</p>
                <p class="text-sm text-gray-500">{{ $comision->codigo }} - {{ $comision->nombre }}</p>
            </div>
            <a href="{{ route('asistencias.alumno.historial', [$comision, $inscripcion]) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Información -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Selecciona las fechas de ausencia que deseas justificar y proporciona el motivo (ej: certificado médico, trámite oficial, etc.).
                </p>
            </div>
        </div>
    </div>

    @if($ausencias->count() > 0)
        <!-- Formulario -->
        <form action="{{ route('asistencias.alumno.justificar.store', [$comision, $inscripcion]) }}" method="POST" id="justificarForm">
            @csrf

            <!-- Lista de Ausencias -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Ausencias Registradas ({{ $ausencias->count() }})
                    </h2>
                    <div class="flex gap-2">
                        <button type="button" onclick="seleccionarTodas()" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded text-sm font-medium">
                            Seleccionar Todas
                        </button>
                        <button type="button" onclick="deseleccionarTodas()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded text-sm font-medium">
                            Deseleccionar Todas
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($ausencias as $ausencia)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           name="asistencias_ids[]" 
                                           value="{{ $ausencia->id }}"
                                           class="w-5 h-5 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $ausencia->fecha->format('l, d/m/Y') }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $ausencia->fecha->diffForHumans() }}
                                                </p>
                                            </div>
                                            <span class="px-3 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                                Ausente
                                            </span>
                                        </div>
                                        @if($ausencia->observaciones)
                                            <p class="text-sm text-gray-600 mt-2">
                                                <span class="font-medium">Obs:</span> {{ $ausencia->observaciones }}
                                            </p>
                                        @endif
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Motivo de Justificación -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Motivo de la Justificación *</h2>
                </div>
                <div class="p-6">
                    <textarea name="observaciones" 
                              rows="4" 
                              class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500"
                              placeholder="Ej: Certificado médico presentado - Gripe del 05/01/2025 al 10/01/2025"
                              required></textarea>
                    <p class="text-sm text-gray-500 mt-2">
                        Este motivo se aplicará a todas las fechas seleccionadas.
                    </p>
                    @error('observaciones')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('asistencias_ids')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-between">
                <a href="{{ route('asistencias.alumno.historial', [$comision, $inscripcion]) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg transition duration-200 font-medium">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Justificar Seleccionadas
                </button>
            </div>
        </form>
    @else
        <!-- Sin Ausencias -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No hay ausencias para justificar</h3>
            <p class="mt-2 text-sm text-gray-500">
                Este alumno no tiene ausencias sin justificar registradas.
            </p>
            <div class="mt-6">
                <a href="{{ route('asistencias.alumno.historial', [$comision, $inscripcion]) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Ver Historial
                </a>
            </div>
        </div>
    @endif
</div>

<script>
    function seleccionarTodas() {
        const checkboxes = document.querySelectorAll('input[name="asistencias_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deseleccionarTodas() {
        const checkboxes = document.querySelectorAll('input[name="asistencias_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    // Confirmación antes de enviar
    document.getElementById('justificarForm')?.addEventListener('submit', function(e) {
        const checkboxes = document.querySelectorAll('input[name="asistencias_ids[]"]:checked');
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('Debes seleccionar al menos una fecha para justificar.');
            return;
        }
        
        const confirm = window.confirm(`¿Confirmar justificación de ${checkboxes.length} ausencia(s)?`);
        if (!confirm) {
            e.preventDefault();
        }
    });
</script>
@endsection

