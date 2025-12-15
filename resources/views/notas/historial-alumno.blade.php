@extends('layouts.app')

@section('title', 'Historial de Notas - ' . ($inscripcion->academicoDato->apellido ?? 'Alumno'))

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('evaluaciones.index') }}" class="hover:text-utn-blue">Evaluaciones</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('evaluaciones.notas.index', $comision) }}" class="hover:text-utn-blue">Notas {{ $comision->codigo }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-gray-900 font-medium">Historial del Alumno</li>
        </ol>
    </nav>

    {{-- Header con info del alumno --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-full bg-utn-blue flex items-center justify-center">
                    <span class="text-white text-xl font-bold">
                        {{ strtoupper(substr($inscripcion->academicoDato->nombre ?? '', 0, 1)) }}{{ strtoupper(substr($inscripcion->academicoDato->apellido ?? '', 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $inscripcion->academicoDato->apellido ?? '' }}, {{ $inscripcion->academicoDato->nombre ?? 'Alumno' }}
                    </h1>
                    <p class="text-gray-600">DNI: {{ $inscripcion->academicoDato->documento ?? 'N/A' }}</p>
                    <p class="text-gray-500 text-sm">Comisión: {{ $comision->codigo }} - {{ $comision->nombre }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('evaluaciones.notas.index', $comision) }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    ← Volver a Notas
                </a>
            </div>
        </div>
    </div>

    {{-- Cards de resumen --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        {{-- Promedio Ponderado --}}
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-utn-blue">
            <p class="text-sm text-gray-600">Promedio Ponderado</p>
            <p class="text-3xl font-bold {{ $promedioPonderado >= 6 ? 'text-green-600' : ($promedioPonderado >= 4 ? 'text-blue-600' : 'text-red-600') }}">
                {{ $promedioPonderado !== null ? number_format($promedioPonderado, 2) : '-' }}
            </p>
        </div>

        {{-- Promedio Simple --}}
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-gray-400">
            <p class="text-sm text-gray-600">Promedio Simple</p>
            <p class="text-3xl font-bold text-gray-700">
                {{ $promedioSimple !== null ? number_format($promedioSimple, 2) : '-' }}
            </p>
        </div>

        {{-- Asistencia --}}
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-purple-500">
            <p class="text-sm text-gray-600">Asistencia</p>
            <p class="text-3xl font-bold {{ $porcentajeAsistencia >= 75 ? 'text-green-600' : 'text-yellow-600' }}">
                {{ $porcentajeAsistencia }}%
            </p>
        </div>

        {{-- Condición Final --}}
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 
            @if($condicion['color'] === 'green') border-green-500
            @elseif($condicion['color'] === 'blue') border-blue-500
            @elseif($condicion['color'] === 'yellow') border-yellow-500
            @elseif($condicion['color'] === 'red') border-red-500
            @else border-gray-500
            @endif">
            <p class="text-sm text-gray-600">Condición Final</p>
            <p class="text-xl font-bold 
                @if($condicion['color'] === 'green') text-green-600
                @elseif($condicion['color'] === 'blue') text-blue-600
                @elseif($condicion['color'] === 'yellow') text-yellow-600
                @elseif($condicion['color'] === 'red') text-red-600
                @else text-gray-600
                @endif">
                {{ $condicion['condicion'] }}
            </p>
            <p class="text-xs text-gray-500 mt-1">{{ $condicion['descripcion'] }}</p>
        </div>
    </div>

    {{-- Tabla de notas --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Historial de Notas</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Evaluación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Peso</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cargado por</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Observaciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($notas as $nota)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        {{ $nota->evaluacion->nombre ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($nota->evaluacion->tipo === 'parcial') bg-blue-100 text-blue-800
                            @elseif($nota->evaluacion->tipo === 'recuperatorio') bg-yellow-100 text-yellow-800
                            @elseif($nota->evaluacion->tipo === 'examen_final') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $nota->evaluacion->tipo ?? 'otro')) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $nota->evaluacion->fecha ? $nota->evaluacion->fecha->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-center text-gray-500">
                        {{ $nota->evaluacion->peso_porcentual ?? 100 }}%
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-white font-bold text-lg
                            @if($nota->nota >= 6) bg-green-500
                            @elseif($nota->nota >= 4) bg-blue-500
                            @else bg-red-500
                            @endif">
                            {{ number_format($nota->nota, 1) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $nota->cargadoPor->name ?? 'Sistema' }}
                        <div class="text-xs text-gray-400">{{ $nota->fecha_carga->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                        {{ $nota->observaciones ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-2">No hay notas registradas para este alumno</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Evaluaciones faltantes --}}
    @if($evaluacionesFaltantes->isNotEmpty())
    <div class="bg-yellow-50 rounded-lg shadow-md p-6 border border-yellow-200">
        <h3 class="text-lg font-semibold text-yellow-800 mb-3">
            <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            Evaluaciones Pendientes ({{ $evaluacionesFaltantes->count() }})
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($evaluacionesFaltantes as $eval)
            <div class="bg-white rounded p-3 border border-yellow-100">
                <p class="font-medium text-gray-800">{{ $eval->nombre }}</p>
                <p class="text-sm text-gray-500">
                    {{ ucfirst($eval->tipo) }} - {{ $eval->fecha ? $eval->fecha->format('d/m/Y') : 'Sin fecha' }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
