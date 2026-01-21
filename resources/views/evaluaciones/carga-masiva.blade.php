@extends('layouts.app')

@section('title', 'Carga Masiva de Notas: ' . $evaluacion->nombre)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Carga Masiva de Notas</h1>
            <p class="text-gray-600 mt-1">
                {{ $evaluacion->nombre }} - {{ $comision->nombre }}
                @if($evaluacion->materia)
                - {{ $evaluacion->materia->nombre }}
                @endif
            </p>
        </div>
        <a href="{{ route('evaluaciones.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">
            Volver
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('evaluaciones.store-carga-masiva', $evaluacion) }}" method="POST">
        @csrf
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <span class="text-sm text-gray-500">
                    Alumnos inscriptos: {{ $inscripciones->count() }}
                </span>
                <button type="submit" class="bg-utn-blue text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-200">
                    Guardar Notas
                </button>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nota Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nueva Nota</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($inscripciones as $ic)
                    @php
                    $inscripcion = $ic->inscripcion;
                    // Fix: la relación 'alumno' no existe, usar getPerson()
                    $alumno = $inscripcion?->getPerson() ?? $ic->academicoDato;
                    // Propiedades comunes
                    $dni = $alumno->documento ?? $alumno->dni ?? '-';

                    $notaActual = $notas[$inscripcion->id] ?? null;
                    if (!$alumno) continue;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $alumno->apellido }}, {{ $alumno->nombre }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $dni }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($notaActual)
                            <span class="{{ $notaActual->nota >= 4 ? 'text-green-600 font-bold' : 'text-red-600 font-bold' }}">
                                {{ number_format($notaActual->nota, 2) }}
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <input type="number"
                                name="notas[{{ $inscripcion->id }}]"
                                value="{{ $notaActual ? (float)$notaActual->nota : '' }}"
                                min="0"
                                max="10"
                                step="0.01"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-24 sm:text-sm border-gray-300 rounded-md"
                                placeholder="-"
                                tabindex="{{ $loop->iteration }}">
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No hay alumnos inscriptos en esta comisión.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <button type="submit" class="bg-utn-blue text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-200 w-full sm:w-auto">
                    Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>
@endsection