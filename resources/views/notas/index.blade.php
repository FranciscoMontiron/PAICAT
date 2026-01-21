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
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Matriz de Notas (Sábana) --}}
    <form action="{{ route('evaluaciones.notas.store-matrix', $comision) }}" method="POST">
        @csrf
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Sábana de Notas</h2>
                    <p class="text-xs text-gray-500">Edita las notas directamente y guarda los cambios.</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500 hidden sm:inline">{{ $inscripciones->count() }} alumnos</span>
                    <button type="submit" class="bg-utn-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Guardar Cambios
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 shadow-sm">Alumno</th>
                            @foreach($evaluaciones as $eval)
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]" title="{{ $eval->nombre }}">
                                <div class="flex flex-col items-center">
                                    <span class="font-bold text-gray-700">{{ Str::limit($eval->nombre, 12) }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $eval->fecha?->format('d/m') ?? '-' }}</span>
                                    <span class="text-[9px] px-1 rounded-sm bg-gray-200 text-gray-600 mt-1 inline-block">
                                        {{ $eval->tipo == 'examen_final' ? 'FINAL' : ($eval->tipo == 'recuperatorio' ? 'REC' : 'PARCIAL') }}
                                    </span>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($inscripciones as $ic)
                        @php
                        // Fix: la relación 'alumno' no existe, usar getPerson()
                        $alumno = $ic->inscripcion?->getPerson() ?? $ic->academicoDato;
                        $apellido = $alumno->apellido ?? '';
                        $nombre = $alumno->nombre ?? '';
                        $dni = $alumno->documento ?? $alumno->dni ?? '-';
                        $iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
                        @endphp
                        <tr class="hover:bg-gray-50 group transition-colors">
                            <td class="px-6 py-3 whitespace-nowrap sticky left-0 bg-white group-hover:bg-gray-50 z-10 border-r border-gray-100">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600 border border-slate-300">
                                        {{ $iniciales }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $apellido }}, {{ $nombre }}</div>
                                        <div class="text-xs text-gray-500">{{ $dni }}</div>
                                    </div>
                                </div>
                            </td>
                            @foreach($evaluaciones as $eval)
                            @php
                            $notaKey = $ic->inscripcion_id . '_' . $eval->id;
                            $nota = $notasMap[$notaKey] ?? null;
                            $valor = $nota ? (float)$nota->nota : '';

                            // Estilo según nota
                            $inputClass = "border-gray-300";
                            if($valor !== '') {
                            if($valor >= 6) $inputClass = "border-green-300 bg-green-50 text-green-800 font-bold focus:ring-green-500 focus:border-green-500";
                            elseif($valor >= 4) $inputClass = "border-blue-300 bg-blue-50 text-blue-800 font-bold focus:ring-blue-500 focus:border-blue-500";
                            else $inputClass = "border-red-300 bg-red-50 text-red-800 font-bold focus:ring-red-500 focus:border-red-500";
                            }
                            @endphp
                            <td class="px-2 py-3 whitespace-nowrap text-center">
                                <input type="number"
                                    name="notas[{{ $ic->inscripcion_id }}][{{ $eval->id }}]"
                                    value="{{ $valor }}"
                                    min="0" max="10" step="0.01"
                                    class="w-20 text-center text-sm rounded-md shadow-sm focus:ring-2 focus:ring-opacity-50 p-1 transition-all {{ $inputClass }}"
                                    placeholder="-"
                                    tabindex="{{ $loop->parent->iteration * 100 + $loop->iteration }}">
                            </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $evaluaciones->count() + 1 }}" class="px-6 py-12 text-center text-gray-500">
                                <p class="text-lg">No hay alumnos inscriptos</p>
                                <p class="text-sm">Inscribe alumnos para comenzar a cargar notas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                <button type="submit" class="bg-utn-blue text-white px-6 py-2 rounded-lg hover:bg-blue-800 font-medium shadow-md transition-all transform hover:scale-105">
                    Guardar Todas las Notas
                </button>
            </div>
        </div>
    </form>

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
                @forelse($notasLog as $nota)
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
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $nombreCompleto }}
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
        @if($notasLog->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $notasLog->links() }}
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