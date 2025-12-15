@extends('layouts.app')

@section('title', 'Registrar Recuperatorio - ' . $comision->codigo)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('evaluaciones.index') }}" class="hover:text-utn-blue">Evaluaciones</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('evaluaciones.notas.index', $comision) }}" class="hover:text-utn-blue">Notas {{ $comision->codigo }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-gray-900 font-medium">Recuperatorio</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Registrar Recuperatorio</h1>
        <p class="text-gray-600 mt-1">Comisión: {{ $comision->codigo }} - {{ $comision->nombre }}</p>
    </div>

    {{-- Mensaje de error --}}
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if($inscripciones->isEmpty())
    <div class="bg-green-50 rounded-lg p-8 text-center">
        <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="mt-4 text-lg font-medium text-green-800">¡Todos los alumnos están aprobados!</h3>
        <p class="mt-2 text-green-600">No hay alumnos que necesiten rendir recuperatorio (promedio ≥ 6).</p>
        <a href="{{ route('evaluaciones.notas.index', $comision) }}" 
           class="mt-4 inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Volver a Notas
        </a>
    </div>
    @else
    {{-- Formulario --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <form action="{{ route('evaluaciones.notas.recuperatorio.store', $comision) }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Alumno --}}
                <div>
                    <label for="inscripcion_comision_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Alumno *
                    </label>
                    <select name="inscripcion_comision_id" id="inscripcion_comision_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('inscripcion_comision_id') border-red-500 @enderror">
                        <option value="">Seleccione un alumno...</option>
                        @foreach($inscripciones as $insc)
                            @php
                                $promedio = $insc->calcularPromedioPonderado();
                            @endphp
                            <option value="{{ $insc->id }}" {{ old('inscripcion_comision_id') == $insc->id ? 'selected' : '' }}>
                                {{ $insc->academicoDato->apellido ?? '' }}, {{ $insc->academicoDato->nombre ?? 'Sin nombre' }}
                                (Promedio: {{ $promedio !== null ? number_format($promedio, 2) : '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('inscripcion_comision_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Evaluación a recuperar --}}
                <div>
                    <label for="evaluacion_original_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Evaluación a Recuperar *
                    </label>
                    <select name="evaluacion_original_id" id="evaluacion_original_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('evaluacion_original_id') border-red-500 @enderror">
                        <option value="">Seleccione una evaluación...</option>
                        @foreach($evaluacionesRecuperables as $eval)
                            <option value="{{ $eval->id }}" {{ old('evaluacion_original_id') == $eval->id ? 'selected' : '' }}>
                                {{ $eval->nombre }} ({{ ucfirst($eval->tipo) }} - {{ $eval->fecha ? $eval->fecha->format('d/m/Y') : 'Sin fecha' }})
                            </option>
                        @endforeach
                    </select>
                    @error('evaluacion_original_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nota del recuperatorio --}}
                <div>
                    <label for="nota" class="block text-sm font-medium text-gray-700 mb-2">
                        Nota del Recuperatorio *
                    </label>
                    <input type="number" name="nota" id="nota" 
                           value="{{ old('nota') }}"
                           min="0" max="10" step="0.01" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('nota') border-red-500 @enderror"
                           placeholder="0.00 - 10.00">
                    @error('nota')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Observaciones --}}
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones
                    </label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('observaciones') border-red-500 @enderror"
                              placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Info --}}
            <div class="mt-6 bg-blue-50 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">Información sobre recuperatorios</h4>
                        <ul class="mt-2 text-sm text-blue-700 list-disc list-inside">
                            <li>El recuperatorio reemplaza la nota original si es mayor</li>
                            <li>Se crea automáticamente una evaluación de tipo "Recuperatorio"</li>
                            <li>La nota original quedará marcada como reemplazada</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('evaluaciones.notas.index', $comision) }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-utn-orange text-white rounded-lg hover:bg-orange-600 transition-colors font-medium">
                    Registrar Recuperatorio
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
