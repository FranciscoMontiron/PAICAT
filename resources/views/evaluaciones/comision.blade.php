@extends('layouts.app')
@section('title', 'Planilla - ' . $comision->nombre)
@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex mb-3" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm">
            <li>
                <a href="{{ route('evaluaciones.index') }}" class="text-gray-500 hover:text-utn-blue-dark">Evaluaciones</a>
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-700 font-medium">{{ $comision->nombre }}</span>
            </li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $comision->nombre }} </h1>
            
            <p class="text-gray-600 mt-1"> Codigo: {{$comision->codigo }} </p>
         
            
            
            
            <div class="flex items-center gap-3 mt-1 text-gray-600">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-utn-blue/10 text-utn-blue-dark">
                    {{ $comision->periodo }} {{ $comision->anio }} 
                </span>
                @if($comision->turno)
                    <span class="capitalize text-sm">{{ $comision->turno }}</span>
                @endif
                @if($comision->modalidad)
                    <span class="text-sm">&bull; {{ $comision->modalidad }}</span>
                @endif
                @if($comision->municipio)
                    <span class="text-sm">&bull; {{ $comision->municipio->nombre }}</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="descargarExcelMateriaActiva()"
               id="btn-exportar-materia"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm shadow-sm cursor-pointer">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span id="btn-exportar-label">Exportar Excel</span>
            </button>
            @if($materias->count() > 1)
            <a href="{{ route('evaluaciones.exportar-planilla', $comision) }}"
               class="inline-flex items-center px-3 py-2 border border-green-600 text-green-700 rounded-lg hover:bg-green-50 transition-colors font-medium text-xs"
               title="Exportar todas las materias en un solo archivo">
                Exportar Todo
            </a>
            @endif
        </div>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg" id="msg-success">
        <div class="flex items-center justify-between">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
            <button onclick="document.getElementById('msg-success').remove()" class="text-green-500 hover:text-green-700">&times;</button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg" id="msg-error">
        <div class="flex items-center justify-between">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
            <button onclick="document.getElementById('msg-error').remove()" class="text-red-500 hover:text-red-700">&times;</button>
        </div>
    </div>
    @endif

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 uppercase font-medium">Alumnos</p>
            <p class="text-2xl font-bold text-utn-blue-dark">{{ $alumnosCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-xs text-gray-500 uppercase font-medium">Materias</p>
            <p class="text-2xl font-bold text-purple-600">{{ $materias->count() }}</p>
        </div>
        @php
            $totalEvals = 0;
            $totalNotas = 0;
            foreach ($dataMateria as $dm) {
                $totalEvals += $dm['evaluaciones']->count();
                foreach ($dm['notasMap'] as $insc => $evals) {
                    $totalNotas += count($evals);
                }
            }
        @endphp
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-500 uppercase font-medium">Evaluaciones</p>
            <p class="text-2xl font-bold text-green-600">{{ $totalEvals }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-xs text-gray-500 uppercase font-medium">Notas cargadas</p>
            <p class="text-2xl font-bold text-orange-600">{{ $totalNotas }}</p>
        </div>
    </div>

    {{-- Tabs de materias --}}
    @if($materias->count() > 1)
    <div class="mb-4 border-b border-gray-200 bg-white rounded-t-lg shadow-sm px-2">
        <nav class="flex gap-1 overflow-x-auto" role="tablist">
            @foreach($materias as $i => $materia)
                <button type="button"
                        class="tab-btn whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $i === 0 ? 'border-utn-blue text-utn-blue-dark' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                        data-tab="materia-{{ $materia->id }}"
                        role="tab">
                    {{ $materia->nombre }}
                    @php $evCount = $dataMateria[$materia->id]['evaluaciones']->count(); @endphp
                    <span class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs rounded-full {{ $evCount > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $evCount }}</span>
                </button>
            @endforeach
        </nav>
    </div>
    @endif

    {{-- Contenido por materia --}}
    @foreach($materias as $i => $materia)
        @php
            $dm = $dataMateria[$materia->id];
            $evaluaciones = $dm['evaluaciones'];
            $notasMap = $dm['notasMap'];
            $notasFinales = $dm['notasFinales'];
            $promedios = $dm['promedios'];
            $notaAprobacion = \App\Services\ConfiguracionService::get('nota_aprobacion', 6);
        @endphp
        <div class="tab-content {{ $i !== 0 && $materias->count() > 1 ? 'hidden' : '' }}" id="materia-{{ $materia->id }}">
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">

                {{-- Header de materia --}}
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $materia->nombre }}</h3>
                            <p class="text-purple-200 text-xs">{{ $evaluaciones->count() }} evaluaci&oacute;n(es) &bull; {{ $alumnosCount }} alumno(s)</p>
                        </div>
                    </div>
                    @if(auth()->user()->hasPermission('evaluaciones.crear'))
                    <button type="button" onclick="abrirModalNuevaEvaluacion({{ $materia->id }}, '{{ addslashes($materia->nombre) }}')"
                            class="text-white/80 hover:text-white text-sm font-medium flex items-center gap-1 bg-white/10 rounded-lg px-3 py-1.5 hover:bg-white/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nueva Evaluaci&oacute;n
                    </button>
                    @endif
                </div>

                @if($inscripciones->isEmpty())
                    <div class="p-8 text-center text-gray-400">
                        <p>No hay alumnos inscriptos en esta comisi&oacute;n.</p>
                    </div>
                @else
                    {{-- Formulario de planilla --}}
                    <form action="{{ route('evaluaciones.notas-materia.store', $comision) }}" method="POST" id="form-materia-{{ $materia->id }}">
                        @csrf
                        <input type="hidden" name="materia_id" value="{{ $materia->id }}">

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 min-w-[200px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                            Alumno
                                        </th>
                                        @foreach($evaluaciones as $eval)
                                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">
                                                <div class="flex flex-col items-center gap-0.5">
                                                    <span class="font-bold text-gray-700" title="{{ $eval->nombre }}">{{ Str::limit($eval->nombre, 14) }}</span>
                                                    <span class="text-[10px] text-gray-400 normal-case">
                                                        {{ \App\Models\Evaluacion::tiposDisponibles()[$eval->tipo] ?? $eval->tipo }}
                                                        @if($eval->fecha) &bull; {{ $eval->fecha->format('d/m') }} @endif
                                                    </span>
                                                    @if(auth()->user()->hasPermission('evaluaciones.editar'))
                                                    <div class="flex gap-1 mt-0.5">
                                                        <a href="{{ route('evaluaciones.edit', $eval) }}" class="text-gray-400 hover:text-utn-blue-dark" title="Editar evaluaci&oacute;n">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        </a>
                                                        @if(auth()->user()->hasPermission('evaluaciones.eliminar'))
                                                        <button type="button" onclick="confirmarEliminarEval({{ $eval->id }}, '{{ addslashes($eval->nombre) }}')" class="text-gray-400 hover:text-red-500" title="Eliminar evaluaci&oacute;n">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                        @endif
                                                    </div>
                                                    @endif
                                                </div>
                                            </th>
                                        @endforeach
                                        <th class="px-2 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider min-w-[80px] bg-blue-50">
                                            Promedio
                                        </th>
                                        <th class="px-2 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider min-w-[100px] bg-green-50">
                                            Nota Final
                                        </th>
                                        <th class="px-2 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider min-w-[80px] bg-gray-50">
                                            Estado
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach($inscripciones as $ic)
                                        @php
                                            $alumno = $ic->inscripcion?->getPerson() ?? $ic->academicoDato;
                                            $apellido = $alumno->apellido ?? '';
                                            $nombre = $alumno->nombre ?? '';
                                            $dni = $alumno->documento ?? $alumno->dni ?? '-';
                                            $iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
                                            $inscId = $ic->inscripcion_id;
                                            $promedio = $promedios[$inscId] ?? null;
                                            $nf = $notasFinales[$inscId] ?? null;
                                            $notaFinalVal = $nf ? $nf->nota_final : '';
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition-colors alumno-row">
                                            {{-- Nombre del alumno (sticky) --}}
                                            <td class="px-4 py-2 sticky left-0 bg-white z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                        {{ $iniciales ?: '?' }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $apellido }}, {{ $nombre }}</p>
                                                        <p class="text-[11px] text-gray-400">{{ $dni }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            {{-- Notas de evaluaciones --}}
                                            @foreach($evaluaciones as $eval)
                                                @php
                                                    $notaObj = $notasMap[$inscId][$eval->id] ?? null;
                                                    $notaVal = $notaObj ? $notaObj->nota : '';
                                                @endphp
                                                <td class="px-1 py-1 text-center">
                                                    @if(auth()->user()->hasPermission('evaluaciones.crear'))
                                                    <input type="number"
                                                           name="notas[{{ $inscId }}][{{ $eval->id }}]"
                                                           value="{{ $notaVal }}"
                                                           min="0" max="10" step="0.01"
                                                           class="nota-input w-20 text-center text-sm py-1.5 border border-gray-200 rounded-md focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent transition-colors
                                                                  {{ $notaVal !== '' ? ($notaVal >= $notaAprobacion ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200') : 'bg-white' }}"
                                                           data-inscripcion="{{ $inscId }}"
                                                           data-materia="{{ $materia->id }}"
                                                           placeholder="-"
                                                           oninput="actualizarColorNota(this); recalcularPromedio('{{ $materia->id }}', '{{ $inscId }}')">
                                                    @else
                                                        <span class="text-sm {{ $notaVal !== '' ? ($notaVal >= $notaAprobacion ? 'text-green-700 font-medium' : 'text-red-700 font-medium') : 'text-gray-400' }}">
                                                            {{ $notaVal !== '' ? number_format($notaVal, 2) : '-' }}
                                                        </span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            {{-- Promedio (calculado) --}}
                                            <td class="px-2 py-1 text-center bg-blue-50/50">
                                                <span class="text-sm font-semibold promedio-display {{ $promedio !== null ? ($promedio >= $notaAprobacion ? 'text-green-600' : 'text-red-600') : 'text-gray-400' }}"
                                                      id="promedio-{{ $materia->id }}-{{ $inscId }}">
                                                    {{ $promedio !== null ? number_format($promedio, 2) : '-' }}
                                                </span>
                                            </td>
                                            {{-- Nota Final --}}
                                            <td class="px-1 py-1 text-center bg-green-50/30">
                                                @if(auth()->user()->hasPermission('evaluaciones.crear'))
                                                <div class="flex items-center justify-center gap-1">
                                                    <input type="number"
                                                           name="notas_finales[{{ $inscId }}]"
                                                           value="{{ $notaFinalVal }}"
                                                           min="0" max="10" step="0.01"
                                                           class="nota-final-input w-20 text-center text-sm py-1.5 border rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent font-semibold transition-colors
                                                                  {{ $notaFinalVal !== '' ? ($notaFinalVal >= $notaAprobacion ? 'bg-green-100 text-green-800 border-green-300' : 'bg-red-100 text-red-800 border-red-300') : 'bg-white border-gray-200' }}"
                                                           id="nf-{{ $materia->id }}-{{ $inscId }}"
                                                           placeholder="-"
                                                           oninput="actualizarColorNotaFinal(this); actualizarEstado('{{ $materia->id }}', '{{ $inscId }}')">
                                                    <button type="button"
                                                            onclick="usarPromedio('{{ $materia->id }}', '{{ $inscId }}')"
                                                            class="text-blue-400 hover:text-blue-600 flex-shrink-0"
                                                            title="Usar promedio como nota final">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                @else
                                                    <span class="text-sm font-bold {{ $notaFinalVal !== '' ? ($notaFinalVal >= $notaAprobacion ? 'text-green-700' : 'text-red-700') : 'text-gray-400' }}">
                                                        {{ $notaFinalVal !== '' ? number_format($notaFinalVal, 2) : '-' }}
                                                    </span>
                                                @endif
                                            </td>
                                            {{-- Estado --}}
                                            <td class="px-2 py-1 text-center">
                                                <span id="estado-{{ $materia->id }}-{{ $inscId }}"
                                                      class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                                                      @if($notaFinalVal !== '')
                                                          {{ $notaFinalVal >= $notaAprobacion ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}
                                                      @elseif($promedio !== null)
                                                          bg-yellow-100 text-yellow-800
                                                      @else
                                                          bg-gray-100 text-gray-500
                                                      @endif">
                                                    @if($notaFinalVal !== '')
                                                        {{ $notaFinalVal >= $notaAprobacion ? 'Aprobado' : 'Desaprobado' }}
                                                    @elseif($promedio !== null)
                                                        En curso
                                                    @else
                                                        Pendiente
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Barra de acciones --}}
                        @if(auth()->user()->hasPermission('evaluaciones.crear'))
                        <div class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                            <div class="text-xs text-gray-400">
                                Tip: Pod&eacute;s usar Tab para moverte entre celdas. Click en
                                <svg class="w-3 h-3 inline text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                para copiar el promedio como nota final.
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('evaluaciones.exportar-planilla', ['comision' => $comision, 'materia' => $materia->id]) }}"
                                   class="inline-flex items-center text-sm text-green-600 hover:text-green-800 font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Excel
                                </a>
                                <button type="button" onclick="autocargarPromedios('{{ $materia->id }}')"
                                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    Autocargar promedios
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center px-5 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors font-medium text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Guardar {{ $materia->nombre }}
                                </button>
                            </div>
                        </div>
                        @endif
                    </form>
                @endif
            </div>
        </div>
    @endforeach

    @if($materias->isEmpty())
    <div class="bg-white rounded-xl shadow-md p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="mt-3 text-gray-500">Esta comisi&oacute;n no tiene materias asignadas.</p>
    </div>
    @endif
</div>

{{-- Modal: Nueva Evaluación --}}
@if(auth()->user()->hasPermission('evaluaciones.crear'))
<div id="modalNuevaEvaluacion" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="cerrarModalEval()"></div>
        <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Nueva Evaluaci&oacute;n</h3>
                <button type="button" onclick="cerrarModalEval()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('evaluaciones.store') }}" method="POST">
                @csrf
                <input type="hidden" name="comision" value="{{ $comision->id }}">
                <input type="hidden" name="anio" value="{{ $comision->anio }}">
                <input type="hidden" name="materia_id" id="modal_materia_id" value="">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Materia</label>
                        <p class="text-sm font-semibold text-purple-700" id="modal_materia_nombre"></p>
                    </div>
                    <div>
                        <label for="modal_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" name="name" id="modal_name" required placeholder="Ej: Parcial 1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="modal_tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                            <select name="tipo" id="modal_tipo" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                                <option value="">Seleccionar...</option>
                                @foreach (\App\Models\Evaluacion::tiposDisponibles() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="modal_instancia" class="block text-sm font-medium text-gray-700 mb-1">Instancia</label>
                            <select name="instancia" id="modal_instancia"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                                <option value="">Sin instancia</option>
                                @foreach (\App\Models\Evaluacion::instanciasDisponibles() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="modal_fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                            <input type="date" name="fecha" id="modal_fecha" required value="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                        </div>
                        <div>
                            <label for="modal_porcentual" class="block text-sm font-medium text-gray-700 mb-1">Peso %</label>
                            <input type="number" name="porcentual" id="modal_porcentual" value="100" min="0" max="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label for="modal_descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripci&oacute;n</label>
                        <input type="text" name="descripcion" id="modal_descripcion" placeholder="Opcional"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="cerrarModalEval()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">
                        Crear Evaluaci&oacute;n
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal: Confirmar eliminación de evaluación --}}
@if(auth()->user()->hasPermission('evaluaciones.eliminar'))
<div id="modalEliminarEval" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('modalEliminarEval').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-xl shadow-2xl max-w-sm w-full p-6 z-10">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Eliminar Evaluaci&oacute;n</h3>
            <p class="text-sm text-gray-600 mb-4">&iquest;Est&aacute;s seguro de eliminar <strong id="elimEvalNombre"></strong>? Se borrar&aacute;n tambi&eacute;n todas sus notas.</p>
            <form id="formEliminarEval" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modalEliminarEval').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    const NOTA_APROBACION = {{ \App\Services\ConfiguracionService::get('nota_aprobacion', 6) }};
    const EXPORT_BASE_URL = "{{ route('evaluaciones.exportar-planilla', $comision) }}";
    const MATERIAS_NOMBRES = {!! json_encode($materias->pluck('nombre', 'id')) !!};
    let materiaActivaId = {{ $materias->isNotEmpty() ? $materias->first()->id : 'null' }};

    // === EXPORTAR MATERIA ACTIVA ===
    function descargarExcelMateriaActiva() {
        if (materiaActivaId) {
            window.location.href = EXPORT_BASE_URL + '?materia=' + materiaActivaId;
        } else {
            window.location.href = EXPORT_BASE_URL;
        }
    }

    function actualizarExportLabel() {
        const label = document.getElementById('btn-exportar-label');
        if (label && materiaActivaId && MATERIAS_NOMBRES[materiaActivaId]) {
            label.textContent = 'Exportar ' + MATERIAS_NOMBRES[materiaActivaId];
        }
    }

    // === TABS ===
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Desactivar todos los tabs
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-utn-blue', 'text-utn-blue-dark');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

            // Activar el seleccionado
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-utn-blue', 'text-utn-blue-dark');
            document.getElementById(this.dataset.tab).classList.remove('hidden');

            // Actualizar materia activa y botón exportar
            materiaActivaId = this.dataset.tab.replace('materia-', '');
            actualizarExportLabel();
        });
    });

    // Inicializar label
    actualizarExportLabel();

    // === COLOR DE NOTAS ===
    function actualizarColorNota(input) {
        const val = parseFloat(input.value);
        input.classList.remove('bg-green-50', 'text-green-700', 'border-green-200', 'bg-red-50', 'text-red-700', 'border-red-200', 'bg-white');
        if (input.value === '' || isNaN(val)) {
            input.classList.add('bg-white');
        } else if (val >= NOTA_APROBACION) {
            input.classList.add('bg-green-50', 'text-green-700', 'border-green-200');
        } else {
            input.classList.add('bg-red-50', 'text-red-700', 'border-red-200');
        }
    }

    function actualizarColorNotaFinal(input) {
        const val = parseFloat(input.value);
        input.classList.remove('bg-green-100', 'text-green-800', 'border-green-300', 'bg-red-100', 'text-red-800', 'border-red-300', 'bg-white', 'border-gray-200');
        if (input.value === '' || isNaN(val)) {
            input.classList.add('bg-white', 'border-gray-200');
        } else if (val >= NOTA_APROBACION) {
            input.classList.add('bg-green-100', 'text-green-800', 'border-green-300');
        } else {
            input.classList.add('bg-red-100', 'text-red-800', 'border-red-300');
        }
    }

    // === RECALCULAR PROMEDIO ===
    function recalcularPromedio(materiaId, inscId) {
        const form = document.getElementById('form-materia-' + materiaId);
        if (!form) return;
        const inputs = form.querySelectorAll('input.nota-input[data-inscripcion="' + inscId + '"][data-materia="' + materiaId + '"]');
        let sum = 0, count = 0;
        inputs.forEach(inp => {
            const v = parseFloat(inp.value);
            if (!isNaN(v)) { sum += v; count++; }
        });
        const promEl = document.getElementById('promedio-' + materiaId + '-' + inscId);
        if (promEl) {
            if (count > 0) {
                const avg = (sum / count).toFixed(2);
                promEl.textContent = avg;
                promEl.classList.remove('text-gray-400', 'text-green-600', 'text-red-600');
                promEl.classList.add(parseFloat(avg) >= NOTA_APROBACION ? 'text-green-600' : 'text-red-600');
            } else {
                promEl.textContent = '-';
                promEl.classList.remove('text-green-600', 'text-red-600');
                promEl.classList.add('text-gray-400');
            }
        }
    }

    // === USAR PROMEDIO COMO NOTA FINAL ===
    function usarPromedio(materiaId, inscId) {
        const promEl = document.getElementById('promedio-' + materiaId + '-' + inscId);
        const nfEl = document.getElementById('nf-' + materiaId + '-' + inscId);
        if (promEl && nfEl && promEl.textContent !== '-') {
            nfEl.value = promEl.textContent.trim();
            actualizarColorNotaFinal(nfEl);
            actualizarEstado(materiaId, inscId);
        }
    }

    // === AUTOCARGAR PROMEDIOS ===
    function autocargarPromedios(materiaId) {
        const form = document.getElementById('form-materia-' + materiaId);
        if (!form) return;
        form.querySelectorAll('.nota-final-input').forEach(nfInput => {
            // Extraer inscId del name: notas_finales[XXX]
            const match = nfInput.name.match(/notas_finales\[(\d+)\]/);
            if (!match) return;
            const inscId = match[1];
            if (nfInput.value === '') {
                usarPromedio(materiaId, inscId);
            }
        });
    }

    // === ACTUALIZAR ESTADO ===
    function actualizarEstado(materiaId, inscId) {
        const nfEl = document.getElementById('nf-' + materiaId + '-' + inscId);
        const estadoEl = document.getElementById('estado-' + materiaId + '-' + inscId);
        if (!estadoEl) return;

        const val = parseFloat(nfEl?.value);
        const promEl = document.getElementById('promedio-' + materiaId + '-' + inscId);
        const tienePromedio = promEl && promEl.textContent.trim() !== '-';

        estadoEl.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold ';

        if (!isNaN(val) && nfEl.value !== '') {
            if (val >= NOTA_APROBACION) {
                estadoEl.className += 'bg-green-100 text-green-800';
                estadoEl.textContent = 'Aprobado';
            } else {
                estadoEl.className += 'bg-red-100 text-red-800';
                estadoEl.textContent = 'Desaprobado';
            }
        } else if (tienePromedio) {
            estadoEl.className += 'bg-yellow-100 text-yellow-800';
            estadoEl.textContent = 'En curso';
        } else {
            estadoEl.className += 'bg-gray-100 text-gray-500';
            estadoEl.textContent = 'Pendiente';
        }
    }

    // === MODAL NUEVA EVALUACIÓN ===
    function abrirModalNuevaEvaluacion(materiaId, materiaNombre) {
        document.getElementById('modal_materia_id').value = materiaId;
        document.getElementById('modal_materia_nombre').textContent = materiaNombre;
        document.getElementById('modal_name').value = '';
        document.getElementById('modal_tipo').value = '';
        document.getElementById('modal_descripcion').value = '';
        document.getElementById('modalNuevaEvaluacion').classList.remove('hidden');
        setTimeout(() => document.getElementById('modal_name').focus(), 100);
    }

    function cerrarModalEval() {
        document.getElementById('modalNuevaEvaluacion').classList.add('hidden');
    }

    // === ELIMINAR EVALUACION ===
    function confirmarEliminarEval(evalId, evalNombre) {
        document.getElementById('elimEvalNombre').textContent = evalNombre;
        document.getElementById('formEliminarEval').action = '/evaluaciones/' + evalId;
        document.getElementById('modalEliminarEval').classList.remove('hidden');
    }
</script>
@endpush
