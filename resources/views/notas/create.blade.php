@extends('layouts.app')
@section('title', 'Cargar Nota')
@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-4 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('evaluaciones.index') }}" class="text-utn-blue hover:underline">Evaluaciones</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="{{ route('evaluaciones.notas.index', $comision) }}" class="text-utn-blue hover:underline">Notas - {{ $comision->nombre }}</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li class="text-gray-500">Nueva Nota</li>
        </ol>
    </nav>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Cargar Nueva Nota</h1>
        <p class="text-gray-600 mt-1">Comisión: <strong>{{ $comision->nombre }}</strong></p>
    </div>

    {{-- Mensajes de error --}}
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
        <form action="{{ route('evaluaciones.notas.store', $comision) }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Alumno --}}
                <div>
                    <label for="inscripcion_comision_id" class="block text-sm font-medium text-gray-700 mb-2">Alumno *</label>
                    <select name="inscripcion_comision_id" id="inscripcion_comision_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('inscripcion_comision_id') border-red-500 @enderror">
                        <option value="">Seleccione un alumno...</option>
                        @foreach ($inscripciones as $inscripcion)
                            @php
                                // Obtener datos del alumno desde inscripcion o academico_dato
                                if ($inscripcion->inscripcion) {
                                    $person = $inscripcion->inscripcion->getPerson();
                                    $nombre = $person?->nombre ?? 'Sin nombre';
                                    $apellido = $person?->apellido ?? '';
                                    $dni = $person?->documento ?? 'N/A';
                                } elseif ($inscripcion->academicoDato) {
                                    $nombre = $inscripcion->academicoDato->nombre ?? 'Sin nombre';
                                    $apellido = $inscripcion->academicoDato->apellido ?? '';
                                    $dni = $inscripcion->academicoDato->dni ?? 'N/A';
                                } else {
                                    $nombre = 'Sin nombre';
                                    $apellido = '';
                                    $dni = 'N/A';
                                }
                            @endphp
                            <option value="{{ $inscripcion->id }}"
                                {{ old('inscripcion_comision_id') == $inscripcion->id ? 'selected' : '' }}>
                                {{ $apellido }}, {{ $nombre }} (DNI: {{ $dni }})
                            </option>
                        @endforeach
                    </select>
                    @error('inscripcion_comision_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Evaluación --}}
                <div>
                    <label for="evaluacion_id" class="block text-sm font-medium text-gray-700 mb-2">Evaluación *</label>
                    <select name="evaluacion_id" id="evaluacion_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('evaluacion_id') border-red-500 @enderror">
                        <option value="">Seleccione una evaluación...</option>
                        @foreach ($evaluaciones as $evaluacion)
                            <option value="{{ $evaluacion->id }}"
                                {{ old('evaluacion_id') == $evaluacion->id ? 'selected' : '' }}>
                                {{ $evaluacion->nombre }} ({{ $evaluacion->fecha?->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('evaluacion_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nota --}}
                <div>
                    <label for="nota" class="block text-sm font-medium text-gray-700 mb-2">Nota (0-10) *</label>
                    <input type="number" name="nota" id="nota" value="{{ old('nota') }}" required
                           step="0.01" min="0" max="10"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('nota') border-red-500 @enderror">
                    @error('nota')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Observaciones --}}
                <div class="md:col-span-2">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('evaluaciones.notas.index', $comision) }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-utn-blue text-white rounded-lg hover:bg-blue-800 transition-colors duration-200">
                    Guardar Nota
                </button>
            </div>
        </form>
    </div>
</div>

@endsection