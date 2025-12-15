@extends('layouts.app')
@section('title', 'Editar Nota')
@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-4 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('evaluaciones.index') }}" class="text-utn-blue hover:underline">Evaluaciones</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="{{ route('evaluaciones.notas.index', $comision) }}" class="text-utn-blue hover:underline">Notas - {{ $comision->nombre }}</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li class="text-gray-500">Editar Nota</li>
        </ol>
    </nav>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Editar Nota</h1>
        @php
            // Obtener datos del alumno desde inscripcion o academico_dato
            $inscripcionComision = $nota->inscripcionComision;
            if ($inscripcionComision->inscripcion) {
                $person = $inscripcionComision->inscripcion->getPerson();
                $nombreCompleto = $person ? $person->apellido . ', ' . $person->nombre : 'Sin nombre';
            } elseif ($inscripcionComision->academicoDato) {
                $nombreCompleto = ($inscripcionComision->academicoDato->apellido ?? '') . ', ' . ($inscripcionComision->academicoDato->nombre ?? 'Sin nombre');
            } else {
                $nombreCompleto = 'Sin datos';
            }
        @endphp
        <p class="text-gray-600 mt-1">
            Alumno: <strong>{{ $nombreCompleto }}</strong>
        </p>
        <p class="text-gray-600">
            Evaluación: <strong>{{ $nota->evaluacion->nombre ?? 'N/A' }}</strong>
        </p>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <form action="{{ route('evaluaciones.notas.update', [$comision, $nota]) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Nota --}}
                <div>
                    <label for="nota" class="block text-sm font-medium text-gray-700 mb-2">Nota (0-10) *</label>
                    <input type="number" name="nota" id="nota" 
                           value="{{ old('nota', $nota->nota) }}" required
                           step="0.01" min="0" max="10"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('nota') border-red-500 @enderror">
                    @error('nota')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info de última modificación --}}
                <div class="flex items-end">
                    <div class="text-sm text-gray-500">
                        <p>Última modificación: {{ $nota->updated_at?->format('d/m/Y H:i') }}</p>
                        <p>Cargado por: {{ $nota->cargadoPor->name ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="md:col-span-2">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('observaciones') border-red-500 @enderror">{{ old('observaciones', $nota->observaciones) }}</textarea>
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
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
