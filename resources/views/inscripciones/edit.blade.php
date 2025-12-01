@extends('layouts.app')

@section('title', 'Editar Inscripción')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Editar Inscripción</h1>
        <p class="text-gray-600 mt-1">Modificar datos de la inscripción #{{ $inscripcion->id }}</p>
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

    {{-- Datos del alumno (solo lectura) --}}
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Alumno</h3>
        <div class="flex items-center">
            <div class="flex-shrink-0 h-12 w-12">
                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-indigo-600 font-semibold">
                        {{ $persona ? strtoupper(substr($persona->nombre ?? '', 0, 1)) . strtoupper(substr($persona->apellido ?? '', 0, 1)) : '??' }}
                    </span>
                </div>
            </div>
            <div class="ml-4">
                <div class="text-lg font-medium text-gray-900">
                    {{ $persona ? ($persona->apellido . ', ' . $persona->nombre) : 'Alumno no encontrado' }}
                </div>
                <div class="text-sm text-gray-500">DNI: {{ $persona?->documento ?? 'N/A' }} - {{ $persona?->email ?? 'Sin email' }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <form action="{{ route('inscripciones.update', $inscripcion) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Especialidad --}}
                <div>
                    <label for="especialidad_id_sysacad" class="block text-sm font-medium text-gray-700 mb-2">Especialidad *</label>
                    <select name="especialidad_id_sysacad" id="especialidad_id_sysacad" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                        <option value="">Seleccionar especialidad</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp->id_sysacad }}" {{ old('especialidad_id_sysacad', $inscripcion->especialidad_id_sysacad) == $esp->id_sysacad ? 'selected' : '' }}>{{ $esp->nombre }}</option>
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
                            <option value="{{ $esp->id_sysacad }}" {{ old('especialidad_alternativa_id_sysacad', $inscripcion->especialidad_alternativa_id_sysacad) == $esp->id_sysacad ? 'selected' : '' }}>{{ $esp->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Modalidad --}}
                <div>
                    <label for="modalidad" class="block text-sm font-medium text-gray-700 mb-2">Modalidad *</label>
                    <select name="modalidad" id="modalidad" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                        @foreach($modalidades as $key => $value)
                            <option value="{{ $key }}" {{ old('modalidad', $inscripcion->modalidad) == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo de Ingreso --}}
                <div>
                    <label for="tipo_ingreso" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Ingreso *</label>
                    <select name="tipo_ingreso" id="tipo_ingreso" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                        @foreach($tiposIngreso as $key => $value)
                            <option value="{{ $key }}" {{ old('tipo_ingreso', $inscripcion->tipo_ingreso) == $key ? 'selected' : '' }}>{{ $value }}</option>
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
                            <option value="{{ $turno->nombre }}" {{ old('turno_ingreso', $inscripcion->turno_ingreso) == $turno->nombre ? 'selected' : '' }}>{{ $turno->nombre }}</option>
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
                            <option value="{{ $turno->nombre }}" {{ old('turno_carrera', $inscripcion->turno_carrera) == $turno->nombre ? 'selected' : '' }}>{{ $turno->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Observaciones --}}
                <div class="md:col-span-2">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">{{ old('observaciones', $inscripcion->observaciones) }}</textarea>
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('inscripciones.show', $inscripcion) }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-utn-blue text-white rounded-lg hover:bg-blue-800 transition-colors duration-200">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
