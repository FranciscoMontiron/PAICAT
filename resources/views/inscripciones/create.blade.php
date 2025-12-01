@extends('layouts.app')

@section('title', 'Nueva Inscripción')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Nueva Inscripción</h1>
        <p class="text-gray-600 mt-1">Registrar una nueva inscripción al curso de ingreso</p>
    </div>

    {{-- Mensajes de error --}}
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

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <form action="{{ route('inscripciones.store') }}" method="POST" class="p-6">
            @csrf

            {{-- Sección: Buscar Alumno --}}
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Datos del Alumno</h3>

                <div class="mb-4">
                    <label for="buscar_alumno" class="block text-sm font-medium text-gray-700 mb-2">Buscar Alumno *</label>
                    <div class="relative">
                        <input type="text" id="buscar_alumno"
                               placeholder="Buscar por nombre, apellido, DNI o email..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent"
                               autocomplete="off">
                        <div id="resultados_busqueda" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    <input type="hidden" name="person_id" id="person_id" value="{{ old('person_id', $personaSeleccionada?->id) }}">
                </div>

                {{-- Datos del alumno seleccionado --}}
                <div id="datos_alumno" class="{{ $personaSeleccionada ? '' : 'hidden' }} bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Nombre completo:</span>
                            <p id="alumno_nombre" class="font-medium">{{ $personaSeleccionada ? ($personaSeleccionada->apellido . ', ' . $personaSeleccionada->nombre) : '' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">DNI:</span>
                            <p id="alumno_dni" class="font-medium">{{ $personaSeleccionada?->documento }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Email:</span>
                            <p id="alumno_email" class="font-medium">{{ $personaSeleccionada?->email }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Estado del formulario:</span>
                            <p id="alumno_estado" class="font-medium">{{ $personaSeleccionada?->formularioDato?->estado ?? 'Sin formulario' }}</p>
                        </div>
                    </div>
                    <button type="button" id="cambiar_alumno" class="mt-4 text-sm text-utn-blue hover:text-blue-800">
                        Cambiar alumno
                    </button>
                </div>
            </div>

            {{-- Sección: Datos de la Inscripción --}}
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Datos de la Inscripción</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Año de Ingreso --}}
                    <div>
                        <label for="anio_ingreso" class="block text-sm font-medium text-gray-700 mb-2">Año de Ingreso *</label>
                        <input type="number" name="anio_ingreso" id="anio_ingreso" required
                               value="{{ old('anio_ingreso', date('Y') + 1) }}"
                               min="2020" max="2100" step="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Año en que el alumno ingresará a cursar</p>
                    </div>

                    {{-- Especialidad --}}
                    <div>
                        <label for="especialidad_id_sysacad" class="block text-sm font-medium text-gray-700 mb-2">Especialidad *</label>
                        <select name="especialidad_id_sysacad" id="especialidad_id_sysacad" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                            <option value="">Seleccionar especialidad</option>
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp->id_sysacad }}" {{ old('especialidad_id_sysacad') == $esp->id_sysacad ? 'selected' : '' }}>{{ $esp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Especialidad Alternativa --}}
                    <div>
                        <label for="especialidad_alternativa_id_sysacad" class="block text-sm font-medium text-gray-700 mb-2">Especialidad Alternativa</label>
                        <select name="especialidad_alternativa_id_sysacad" id="especialidad_alternativa_id_sysacad"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                            <option value="">Sin especialidad alternativa</option>
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp->id_sysacad }}" {{ old('especialidad_alternativa_id_sysacad') == $esp->id_sysacad ? 'selected' : '' }}>{{ $esp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Modalidad --}}
                    <div>
                        <label for="modalidad" class="block text-sm font-medium text-gray-700 mb-2">Modalidad *</label>
                        <select name="modalidad" id="modalidad" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                            @foreach($modalidades as $key => $value)
                                <option value="{{ $key }}" {{ old('modalidad', 'Presencial') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tipo de Ingreso --}}
                    <div>
                        <label for="tipo_ingreso" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Ingreso *</label>
                        <select name="tipo_ingreso" id="tipo_ingreso" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                            @foreach($tiposIngreso as $key => $value)
                                <option value="{{ $key }}" {{ old('tipo_ingreso', 'Extensivo') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Turno de Ingreso --}}
                    <div>
                        <label for="turno_ingreso" class="block text-sm font-medium text-gray-700 mb-2">Turno de Ingreso</label>
                        <select name="turno_ingreso" id="turno_ingreso"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                            <option value="">Seleccionar turno</option>
                            @foreach($turnos as $turno)
                                <option value="{{ $turno->nombre }}" {{ old('turno_ingreso') == $turno->nombre ? 'selected' : '' }}>{{ $turno->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Turno de Carrera --}}
                    <div>
                        <label for="turno_carrera" class="block text-sm font-medium text-gray-700 mb-2">Turno de Carrera</label>
                        <select name="turno_carrera" id="turno_carrera"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                            <option value="">Seleccionar turno</option>
                            @foreach($turnos as $turno)
                                <option value="{{ $turno->nombre }}" {{ old('turno_carrera') == $turno->nombre ? 'selected' : '' }}>{{ $turno->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Observaciones --}}
                    <div class="md:col-span-2">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('inscripciones.index') }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-utn-blue text-white rounded-lg hover:bg-blue-800 transition-colors duration-200">
                    Registrar Inscripción
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buscarInput = document.getElementById('buscar_alumno');
    const resultadosDiv = document.getElementById('resultados_busqueda');
    const personIdInput = document.getElementById('person_id');
    const datosAlumnoDiv = document.getElementById('datos_alumno');
    const cambiarAlumnoBtn = document.getElementById('cambiar_alumno');
    let timeout = null;

    buscarInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const termino = this.value.trim();

        if (termino.length < 2) {
            resultadosDiv.classList.add('hidden');
            return;
        }

        timeout = setTimeout(function() {
            fetch(`{{ route('inscripciones.buscar-aspirante') }}?q=${encodeURIComponent(termino)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        resultadosDiv.innerHTML = '<div class="p-3 text-gray-500">No se encontraron resultados</div>';
                    } else {
                        resultadosDiv.innerHTML = data.map(persona => `
                            <div class="p-3 hover:bg-gray-100 cursor-pointer border-b last:border-b-0"
                                 data-id="${persona.id}"
                                 data-nombre="${persona.text}"
                                 data-documento="${persona.documento}"
                                 data-email="${persona.email}"
                                 data-estado="${persona.estado_formulario}">
                                <div class="font-medium">${persona.text}</div>
                                <div class="text-sm text-gray-500">${persona.email} - Estado: ${persona.estado_formulario}</div>
                            </div>
                        `).join('');
                    }
                    resultadosDiv.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultadosDiv.innerHTML = '<div class="p-3 text-red-500">Error al buscar</div>';
                    resultadosDiv.classList.remove('hidden');
                });
        }, 300);
    });

    resultadosDiv.addEventListener('click', function(e) {
        const item = e.target.closest('[data-id]');
        if (item) {
            personIdInput.value = item.dataset.id;
            document.getElementById('alumno_nombre').textContent = item.dataset.nombre;
            document.getElementById('alumno_dni').textContent = item.dataset.documento;
            document.getElementById('alumno_email').textContent = item.dataset.email;
            document.getElementById('alumno_estado').textContent = item.dataset.estado;

            datosAlumnoDiv.classList.remove('hidden');
            buscarInput.value = '';
            buscarInput.classList.add('hidden');
            resultadosDiv.classList.add('hidden');
        }
    });

    cambiarAlumnoBtn.addEventListener('click', function() {
        personIdInput.value = '';
        datosAlumnoDiv.classList.add('hidden');
        buscarInput.classList.remove('hidden');
        buscarInput.focus();
    });

    // Ocultar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!buscarInput.contains(e.target) && !resultadosDiv.contains(e.target)) {
            resultadosDiv.classList.add('hidden');
        }
    });

    // Si ya hay un alumno seleccionado, ocultar el campo de búsqueda
    if (personIdInput.value) {
        buscarInput.classList.add('hidden');
    }
});
</script>
@endpush
@endsection
