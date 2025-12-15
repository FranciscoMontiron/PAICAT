@extends('layouts.app')
@section('title', 'Notas - ' . $comision->codigo)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- Breadcrumb --}}
    <nav class="mb-4 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('evaluaciones.index') }}" class="text-utn-blue hover:underline">Evaluaciones</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li class="text-gray-500">Notas - {{ $comision->codigo }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notas de la Comisión</h1>
            <p class="text-gray-600 mt-1">
                <strong>{{ $comision->codigo }}</strong> - {{ $comision->nombre }} | {{ $comision->anio }} - {{ $comision->periodo }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            {{-- Exportar Acta --}}
            <a href="{{ route('evaluaciones.notas.exportar-acta', $comision) }}"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exportar Acta
            </a>
            
            @if(auth()->user()->hasPermission('evaluaciones.crear'))
            {{-- Recuperatorio --}}
            <a href="{{ route('evaluaciones.notas.recuperatorio.create', $comision) }}"
                class="bg-utn-orange text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Recuperatorio
            </a>
            
            {{-- Cargar Nota --}}
            <a href="{{ route('evaluaciones.notas.create', $comision) }}"
                class="bg-utn-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Cargar Nota
            </a>
            @endif
        </div>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
        <div class="flex">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

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

    {{-- Resumen por alumno con promedio y condición --}}
    @php
        $inscripciones = \App\Models\InscripcionComision::where('comision_id', $comision->id)
            ->with(['academicoDato', 'notas.evaluacion'])
            ->get()
            ->sortBy(fn($i) => $i->academicoDato->apellido ?? '');
    @endphp
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Resumen por Alumno</h2>
            <span class="text-sm text-gray-500">{{ $inscripciones->count() }} alumnos</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Asistencia</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Condición</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($inscripciones as $inscripcion)
                    @php
                        $promedio = $inscripcion->calcularPromedioPonderado();
                        $asistencia = $inscripcion->calcularPorcentajeAsistencia();
                        $condicion = $inscripcion->determinarCondicion();
                        $cantNotas = $inscripcion->notas->count();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-utn-blue flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">
                                        {{ strtoupper(substr($inscripcion->academicoDato->nombre ?? '', 0, 1)) }}{{ strtoupper(substr($inscripcion->academicoDato->apellido ?? '', 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $inscripcion->academicoDato->apellido ?? '' }}, {{ $inscripcion->academicoDato->nombre ?? 'Sin nombre' }}
                                    </div>
                                    <div class="text-xs text-gray-500">DNI: {{ $inscripcion->academicoDato->documento ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                {{ $cantNotas }} {{ $cantNotas === 1 ? 'nota' : 'notas' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($promedio !== null)
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-white font-bold
                                    @if($promedio >= 6) bg-green-500
                                    @elseif($promedio >= 4) bg-blue-500
                                    @else bg-red-500
                                    @endif">
                                    {{ number_format($promedio, 1) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-sm font-medium {{ $asistencia >= 75 ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $asistencia }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($condicion['color'] === 'green') bg-green-100 text-green-800
                                @elseif($condicion['color'] === 'blue') bg-blue-100 text-blue-800
                                @elseif($condicion['color'] === 'yellow') bg-yellow-100 text-yellow-800
                                @elseif($condicion['color'] === 'red') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $condicion['condicion'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('evaluaciones.notas.historial-alumno', [$comision, $inscripcion]) }}"
                               class="text-indigo-600 hover:text-indigo-900 inline-flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Ver Historial
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <p>No hay alumnos inscriptos en esta comisión</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabla de notas individuales --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Últimas Notas Cargadas</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumno</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evaluación</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Carga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observaciones</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($notas as $nota)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $nota->inscripcionComision->academicoDato->apellido ?? '' }}, 
                            {{ $nota->inscripcionComision->academicoDato->nombre ?? '' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $nota->evaluacion->nombre ?? 'N/A' }}</div>
                            <span class="px-2 py-0.5 text-xs rounded
                                @if($nota->evaluacion->tipo === 'parcial') bg-blue-100 text-blue-700
                                @elseif($nota->evaluacion->tipo === 'recuperatorio') bg-yellow-100 text-yellow-700
                                @elseif($nota->evaluacion->tipo === 'examen_final') bg-purple-100 text-purple-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $nota->evaluacion->tipo ?? 'otro')) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $notaValor = $nota->nota;
                            @endphp
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-white font-bold
                                @if($notaValor >= 6) bg-green-500
                                @elseif($notaValor >= 4) bg-blue-500
                                @else bg-red-500
                                @endif">
                                {{ number_format($notaValor, 1) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $nota->fecha_carga?->format('d/m/Y H:i') ?? 'N/A' }}
                            <div class="text-xs text-gray-400">{{ $nota->cargadoPor->name ?? 'Sistema' }}</div>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $nota->observaciones }}">
                            {{ $nota->observaciones ?? '-' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if(auth()->user()->hasPermission('evaluaciones.editar'))
                                <a href="{{ route('evaluaciones.notas.edit', [$comision, $nota]) }}" 
                                   class="text-utn-blue hover:text-blue-800 mr-3">Editar</a>
                            @endif
                            @if(auth()->user()->hasPermission('evaluaciones.eliminar'))
                                <form action="{{ route('evaluaciones.notas.destroy', [$comision, $nota]) }}" 
                                      method="POST" class="inline" 
                                      onsubmit="return confirm('¿Eliminar esta nota?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2">No hay notas registradas en esta comisión</p>
                            @if(auth()->user()->hasPermission('evaluaciones.crear'))
                            <a href="{{ route('evaluaciones.notas.create', $comision) }}" class="mt-4 inline-block text-utn-blue hover:text-blue-800">
                                Cargar la primera nota
                            </a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        {{-- Paginación --}}
        @if($notas->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $notas->links() }}
        </div>
        @endif
    </div>

    {{-- Botón volver --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('evaluaciones.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver a Evaluaciones
        </a>
        
        {{-- Leyenda de colores --}}
        <div class="flex items-center gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-green-500"></span> Promocionado (≥6)
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span> Regular (4-5.99)
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-red-500"></span> Desaprobado (&lt;4)
            </span>
        </div>
    </div>
</div>
@endsection
