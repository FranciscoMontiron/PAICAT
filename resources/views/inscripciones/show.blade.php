@extends('layouts.app')

@section('title', 'Detalle de Inscripción')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detalle de Inscripción</h1>
            <p class="text-gray-600 mt-1">Inscripción #{{ $inscripcion->id }} - Año {{ $inscripcion->anio_ingreso }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('inscripciones.index') }}"
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
            @if(auth()->user()->hasPermission('inscripciones.editar') && $inscripcion->puedeModificarse())
            <a href="{{ route('inscripciones.edit', $inscripcion) }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            @endif
        </div>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna principal --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Estado de la inscripción --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Estado de la Inscripción</h2>
                    <span class="px-3 py-1 text-sm rounded-full
                        @if($inscripcion->estado === 'pendiente') bg-yellow-100 text-yellow-800
                        @elseif($inscripcion->estado === 'documentacion_ok') bg-blue-100 text-blue-800
                        @elseif($inscripcion->estado === 'confirmado') bg-green-100 text-green-800
                        @elseif($inscripcion->estado === 'cancelado') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ \App\Models\Inscripcion::ESTADOS[$inscripcion->estado] ?? $inscripcion->estado }}
                    </span>
                </div>

                {{-- Acciones de estado --}}
                @if(auth()->user()->hasPermission('inscripciones.editar'))
                <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t">
                    @if($inscripcion->estado === 'documentacion_ok')
                    <form action="{{ route('inscripciones.confirmar', $inscripcion) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                            Confirmar Inscripción
                        </button>
                    </form>
                    @endif

                    @if($inscripcion->puedeCancelarse())
                    <button type="button" onclick="document.getElementById('modal-cancelar').classList.remove('hidden')"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm">
                        Cancelar Inscripción
                    </button>
                    @endif
                </div>
                @endif
            </div>

            {{-- Datos del alumno --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Datos del Alumno</h2>

                @if($persona)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">Nombre completo</span>
                        <p class="font-medium">{{ $persona->apellido }}, {{ $persona->nombre }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">DNI</span>
                        <p class="font-medium">{{ $persona->documento }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Email</span>
                        <p class="font-medium">{{ $persona->email }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Teléfono</span>
                        <p class="font-medium">{{ $persona->telefono_celular ?? $persona->telefono_fijo ?? 'No registrado' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Fecha de nacimiento</span>
                        <p class="font-medium">{{ $persona->nacimiento_fecha?->format('d/m/Y') ?? 'No registrada' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Estado en sistema de alumnos</span>
                        <p class="font-medium">
                            <span class="px-2 py-1 text-xs rounded-full {{ $persona->__estado === 'Verificado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $persona->__estado }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($persona->secundariaDato)
                <div class="mt-6 pt-4 border-t">
                    <h3 class="text-md font-semibold text-gray-700 mb-3">Datos del Secundario</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Título</span>
                            <p class="font-medium">{{ $persona->secundariaDato->titulo ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Institución</span>
                            <p class="font-medium">{{ $persona->secundariaDato->institucion ?? 'No registrada' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Año de egreso</span>
                            <p class="font-medium">{{ $persona->secundariaDato->anio_egreso ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Promedio</span>
                            <p class="font-medium">{{ $persona->secundariaDato->promedio ?? 'No registrado' }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @else
                <div class="text-center py-8 text-gray-500">
                    <p>No se encontraron datos del alumno en el sistema de alumnos.</p>
                </div>
                @endif
            </div>

            {{-- Datos de la inscripción --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Datos de la Inscripción</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">Año de ingreso</span>
                        <p class="font-medium">{{ $inscripcion->anio_ingreso }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Especialidad</span>
                        <p class="font-medium">{{ $especialidad?->nombre ?? 'No especificada' }}</p>
                    </div>
                    @if($especialidadAlternativa)
                    <div>
                        <span class="text-sm text-gray-500">Especialidad alternativa</span>
                        <p class="font-medium">{{ $especialidadAlternativa->nombre }}</p>
                    </div>
                    @endif
                    <div>
                        <span class="text-sm text-gray-500">Modalidad</span>
                        <p class="font-medium">{{ $inscripcion->modalidad }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Tipo de ingreso</span>
                        <p class="font-medium">{{ $inscripcion->tipo_ingreso }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Turno de ingreso</span>
                        <p class="font-medium">{{ $inscripcion->turno_ingreso ?? 'No especificado' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Turno de carrera</span>
                        <p class="font-medium">{{ $inscripcion->turno_carrera ?? 'No especificado' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Fecha de inscripción</span>
                        <p class="font-medium">{{ $inscripcion->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if($inscripcion->observaciones)
                <div class="mt-4 pt-4 border-t">
                    <span class="text-sm text-gray-500">Observaciones</span>
                    <p class="font-medium">{{ $inscripcion->observaciones }}</p>
                </div>
                @endif
            </div>

            {{-- Trayectoria Académica --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Trayectoria Académica</h2>
                    @if($inscripcion->estado_ingreso)
                    <span class="px-3 py-1 text-sm rounded-full
                        @switch($inscripcion->estado_ingreso)
                            @case('inscripto') bg-blue-100 text-blue-800 @break
                            @case('cursando') bg-indigo-100 text-indigo-800 @break
                            @case('aprobado') bg-green-100 text-green-800 @break
                            @case('desaprobado') bg-red-100 text-red-800 @break
                            @case('libre') bg-orange-100 text-orange-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch">
                        {{ \App\Models\Inscripcion::ESTADOS_INGRESO[$inscripcion->estado_ingreso] ?? $inscripcion->estado_ingreso ?? 'Sin estado' }}
                    </span>
                    @endif
                </div>

                @if(!empty($tieneComision) && $comision)
                {{-- Información de la comisión --}}
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <h3 class="text-md font-semibold text-blue-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Comisión Asignada
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                        <div>
                            <span class="text-blue-600">Nombre</span>
                            <p class="font-semibold text-gray-800">{{ $comision->nombre }}</p>
                        </div>
                        <div>
                            <span class="text-blue-600">Periodo</span>
                            <p class="font-semibold text-gray-800">{{ $comision->periodo ?? 'N/A' }} {{ $comision->anio }}</p>
                        </div>
                        <div>
                            <span class="text-blue-600">Turno</span>
                            <p class="font-semibold text-gray-800">{{ $comision->turno ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <span class="text-blue-600">Modalidad</span>
                            <p class="font-semibold text-gray-800">{{ $comision->modalidad ?? 'N/A' }}</p>
                        </div>
                        @if($comision->municipio)
                        <div>
                            <span class="text-blue-600">Sede</span>
                            <p class="font-semibold text-gray-800">{{ $comision->municipio->nombre }}</p>
                        </div>
                        @endif
                        @if($comision->aula)
                        <div>
                            <span class="text-blue-600">Aula</span>
                            <p class="font-semibold text-gray-800">{{ $comision->aula->nombre }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Resumen de rendimiento --}}
                @if(!empty($resumenNotas))
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <span class="text-3xl font-bold {{ ($resumenNotas['materias_aprobadas'] ?? 0) == ($resumenNotas['total_materias'] ?? 0) && ($resumenNotas['total_materias'] ?? 0) > 0 ? 'text-green-600' : 'text-gray-800' }}">
                            {{ $resumenNotas['materias_aprobadas'] ?? 0 }}/{{ $resumenNotas['total_materias'] ?? 0 }}
                        </span>
                        <p class="text-sm text-gray-500">Materias Aprobadas</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <span class="text-3xl font-bold {{ ($resumenNotas['porcentaje_asistencia'] ?? 0) >= 75 ? 'text-green-600' : 'text-orange-600' }}">
                            {{ $resumenNotas['porcentaje_asistencia'] ?? 100 }}%
                        </span>
                        <p class="text-sm text-gray-500">Asistencia</p>
                    </div>
                </div>
                @endif

                {{-- Tabla de materias y notas --}}
                @if(!empty($materias) && $materias->count() > 0)
                <h3 class="text-md font-semibold text-gray-700 mb-3">Materias y Calificaciones</h3>
                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Evaluaciones</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nota</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($materias as $materia)
                            @php
                                $infoMateria = $notasPorMateria[$materia->id] ?? null;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $materia->nombre }}</div>
                                    @if($materia->codigo)
                                    <div class="text-xs text-gray-500">{{ $materia->codigo }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($infoMateria && !empty($infoMateria['notas']))
                                    <div class="flex flex-wrap justify-center gap-1">
                                        @foreach($infoMateria['notas'] as $notaInfo)
                                            @if($notaInfo['nota'] && $notaInfo['nota']->nota !== null)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $notaInfo['nota']->nota >= 6 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $notaInfo['evaluacion']->tipo_nombre ?? 'Eval' }}: {{ number_format($notaInfo['nota']->nota, 1) }}
                                            </span>
                                            @else
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-500">
                                                {{ $notaInfo['evaluacion']->tipo_nombre ?? 'Eval' }}: -
                                            </span>
                                            @endif
                                        @endforeach
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-400">Sin evaluaciones</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($infoMateria && $infoMateria['nota_final'] !== null)
                                    <span class="text-lg font-bold {{ $infoMateria['nota_final'] >= 6 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($infoMateria['nota_final'], 1) }}
                                    </span>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($infoMateria && !empty($infoMateria['aprobada']))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Aprobada
                                    </span>
                                    @elseif($infoMateria && !empty($infoMateria['notas']))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">En curso</span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Botón aprobar cursada --}}
                @if(!empty($puedeAprobarCursada) && auth()->user()->hasPermission('inscripciones.editar'))
                <div class="mt-4 pt-4 border-t">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                            <div>
                                <h4 class="text-green-800 font-semibold flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Todas las materias aprobadas
                                </h4>
                                <p class="text-green-600 text-sm">El alumno completó todas las materias del curso de ingreso.</p>
                            </div>
                            <form action="{{ route('inscripciones.aprobar-cursada', $inscripcion) }}" method="POST" onsubmit="return confirm('¿Confirma aprobar la cursada de este alumno?')">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium whitespace-nowrap">
                                    Aprobar Cursada
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @else
                <p class="text-gray-500 text-sm text-center py-4">No hay materias registradas para esta comisión.</p>
                @endif

                @else
                {{-- No tiene comisión asignada --}}
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Sin comisión asignada</h3>
                    <p class="mt-2 text-sm text-gray-500">El alumno aún no ha sido asignado a ninguna comisión del curso de ingreso.</p>
                    @if(auth()->user()->hasPermission('comisiones.editar'))
                    <div class="mt-6">
                        <a href="{{ route('asignacion-alumnos.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-utn-blue hover:bg-blue-800 transition-colors">
                            <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Asignar a comisión
                        </a>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Columna lateral --}}
        <div class="space-y-6">
            {{-- Validación de documentación --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Validación de Documentación</h2>

                @if($inscripcion->estado_documentacion === 'confirmada')
                {{-- Documentación ya confirmada - mostrar estado final --}}
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center text-green-800">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold">Documentación Confirmada</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                        <span class="text-sm text-gray-700">DNI validado</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                        <span class="text-sm text-gray-700">Título secundario validado</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                        <span class="text-sm text-gray-700">Analítico validado</span>
                    </div>
                </div>
                @elseif(auth()->user()->hasPermission('inscripciones.editar'))
                <form action="{{ route('inscripciones.validar-documentacion', $inscripcion) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="doc_dni_validado" value="1" {{ $inscripcion->doc_dni_validado ? 'checked' : '' }}
                                   class="h-4 w-4 text-utn-blue border-gray-300 rounded focus:ring-utn-blue">
                            <span class="ml-2 text-sm text-gray-700">DNI validado</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="doc_titulo_validado" value="1" {{ $inscripcion->doc_titulo_validado ? 'checked' : '' }}
                                   class="h-4 w-4 text-utn-blue border-gray-300 rounded focus:ring-utn-blue">
                            <span class="ml-2 text-sm text-gray-700">Título secundario validado</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="doc_analitico_validado" value="1" {{ $inscripcion->doc_analitico_validado ? 'checked' : '' }}
                                   class="h-4 w-4 text-utn-blue border-gray-300 rounded focus:ring-utn-blue">
                            <span class="ml-2 text-sm text-gray-700">Analítico validado</span>
                        </label>

                        <div>
                            <label for="observaciones_documentacion" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                            <textarea name="observaciones_documentacion" id="observaciones_documentacion" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent text-sm">{{ $inscripcion->observaciones_documentacion }}</textarea>
                        </div>

                        <button type="submit" class="w-full bg-utn-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors text-sm">
                            Guardar Validación
                        </button>
                    </div>
                </form>
                @else
                <div class="space-y-3">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full {{ $inscripcion->doc_dni_validado ? 'bg-green-500' : 'bg-gray-300' }} mr-2"></span>
                        <span class="text-sm text-gray-700">DNI {{ $inscripcion->doc_dni_validado ? 'validado' : 'pendiente' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full {{ $inscripcion->doc_titulo_validado ? 'bg-green-500' : 'bg-gray-300' }} mr-2"></span>
                        <span class="text-sm text-gray-700">Título {{ $inscripcion->doc_titulo_validado ? 'validado' : 'pendiente' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full {{ $inscripcion->doc_analitico_validado ? 'bg-green-500' : 'bg-gray-300' }} mr-2"></span>
                        <span class="text-sm text-gray-700">Analítico {{ $inscripcion->doc_analitico_validado ? 'validado' : 'pendiente' }}</span>
                    </div>
                </div>
                @endif

                @if($inscripcion->usuario_validacion_id)
                <div class="mt-4 pt-4 border-t text-sm text-gray-500">
                    <p>Validado por: {{ $inscripcion->usuarioValidacion?->nombre_completo ?? 'Usuario eliminado' }}</p>
                    <p>Fecha: {{ $inscripcion->fecha_validacion?->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>

            {{-- Condiciones Particulares --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Condiciones Particulares</h2>
                    @if(auth()->user()->hasPermission('inscripciones.editar'))
                    <button onclick="document.getElementById('modalCondicion').classList.remove('hidden')"
                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        + Agregar
                    </button>
                    @endif
                </div>
                @if($inscripcion->condicionesParticulares && $inscripcion->condicionesParticulares->where('activa', true)->count() > 0)
                <div class="space-y-3">
                    @foreach($inscripcion->condicionesParticulares->where('activa', true) as $condicion)
                    <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-purple-900 text-sm">{{ $condicion->titulo }}</p>
                                <p class="text-xs text-purple-600 capitalize">{{ str_replace('_', ' ', $condicion->tipo) }}</p>
                            </div>
                            @if(auth()->user()->hasPermission('inscripciones.editar'))
                            <form action="{{ route('inscripciones.desactivar-condicion', $condicion) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-purple-400 hover:text-purple-600" title="Desactivar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                        @if($condicion->descripcion)
                        <p class="text-xs text-purple-700 mt-2">{{ Str::limit($condicion->descripcion, 100) }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4 text-sm">Sin condiciones registradas.</p>
                @endif
            </div>

            {{-- Solicitudes de Cambio --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Solicitudes de Cambio</h2>
                    @if(auth()->user()->hasPermission('inscripciones.editar') && !empty($tieneComision))
                    <button onclick="document.getElementById('modalSolicitudCambio').classList.remove('hidden')"
                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        + Nueva
                    </button>
                    @endif
                </div>
                @if($inscripcion->solicitudesCambio && $inscripcion->solicitudesCambio->count() > 0)
                <div class="space-y-3">
                    @foreach($inscripcion->solicitudesCambio->take(5) as $solicitud)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $solicitud->tipo) }}</span>
                            @php
                            $estadoColors = [
                                'pendiente' => 'bg-yellow-100 text-yellow-700',
                                'aprobada' => 'bg-green-100 text-green-700',
                                'rechazada' => 'bg-red-100 text-red-700',
                            ];
                            @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $estadoColors[$solicitud->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($solicitud->estado) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4 text-sm">Sin solicitudes.</p>
                @endif
            </div>

            {{-- Historial de Trayectoria --}}
            @if($inscripcion->trayectorias && $inscripcion->trayectorias->count() > 0)
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Historial de Trayectoria</h2>
                <div class="relative">
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    <div class="space-y-4">
                        @foreach($inscripcion->trayectorias as $trayectoria)
                        @php
                        $colors = [
                            'activo' => 'bg-green-500',
                            'pausado' => 'bg-yellow-500',
                            'libre' => 'bg-orange-500',
                            'baja' => 'bg-red-500',
                            'reincorporado' => 'bg-blue-500',
                            'aprobado' => 'bg-emerald-500',
                            'desaprobado' => 'bg-red-500',
                        ];
                        @endphp
                        <div class="relative pl-10">
                            <div class="absolute left-2 w-4 h-4 rounded-full {{ $colors[$trayectoria->estado] ?? 'bg-gray-400' }} border-2 border-white"></div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold text-gray-900 capitalize text-sm">{{ $trayectoria->estado }}</span>
                                    <span class="text-xs text-gray-500">{{ $trayectoria->fecha_inicio->format('d/m/Y') }}</span>
                                </div>
                                @if($trayectoria->motivo)
                                <p class="text-xs text-gray-600">{{ $trayectoria->motivo }}</p>
                                @endif
                                @if($trayectoria->fecha_fin)
                                <p class="text-xs text-gray-400 mt-1">Hasta: {{ $trayectoria->fecha_fin->format('d/m/Y') }}</p>
                                @else
                                <span class="inline-block mt-1 text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded">Vigente</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Información de auditoría --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información de Auditoría</h2>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-500">Registrado por:</span>
                        <p class="font-medium">{{ $inscripcion->usuarioRegistro?->nombre_completo ?? 'Usuario eliminado' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Fecha de creación:</span>
                        <p class="font-medium">{{ $inscripcion->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Última modificación:</span>
                        <p class="font-medium">{{ $inscripcion->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de cancelación --}}
<div id="modal-cancelar" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Cancelar Inscripción</h3>
        <form action="{{ route('inscripciones.cancelar', $inscripcion) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="motivo_cancelacion" class="block text-sm font-medium text-gray-700 mb-2">Motivo de cancelación</label>
                <textarea name="motivo_cancelacion" id="motivo_cancelacion" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent"
                          placeholder="Ingrese el motivo de la cancelación..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-cancelar').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirmar Cancelación
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Agregar Condición Particular --}}
<div id="modalCondicion" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Condición Particular</h3>
            <form action="{{ route('inscripciones.agregar-condicion', $inscripcion) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select name="tipo" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue">
                            <option value="discapacidad">Discapacidad</option>
                            <option value="enfermedad_cronica">Enfermedad Crónica</option>
                            <option value="situacion_laboral">Situación Laboral</option>
                            <option value="situacion_familiar">Situación Familiar</option>
                            <option value="condicionalidad_academica">Condicionalidad Académica</option>
                            <option value="otra">Otra</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                        <input type="text" name="titulo" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue" placeholder="Ej: Hipoacusia leve">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue" placeholder="Describa la condición..."></textarea>
                    </div>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="requiere_adecuacion" value="1" class="rounded border-gray-300">
                            <span class="text-sm text-gray-700">Requiere adecuaciones pedagógicas</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalCondicion').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Solicitud de Cambio --}}
<div id="modalSolicitudCambio" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Solicitar Cambio</h3>
            <form action="{{ route('inscripciones.crear-solicitud', $inscripcion) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Cambio</label>
                        <select name="tipo" id="tipoSolicitud" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue" onchange="toggleCamposCambio()">
                            <option value="comision">Cambio de Comisión</option>
                            <option value="modalidad">Cambio de Modalidad</option>
                            <option value="turno">Cambio de Turno</option>
                        </select>
                    </div>
                    <div id="campoComision">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Comisión Destino</label>
                        <select name="comision_destino_id" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue">
                            <option value="">Seleccionar comisión...</option>
                            @if(isset($comisionesDisponibles) && $comisionesDisponibles->count() > 0)
                                @foreach($comisionesDisponibles as $comisionDisp)
                                    <option value="{{ $comisionDisp->id }}">
                                        {{ $comisionDisp->nombre }} - {{ $comisionDisp->turno ?? '' }} {{ $comisionDisp->modalidad ?? '' }}
                                        @if($comisionDisp->cupos_disponibles !== null) ({{ $comisionDisp->cupos_disponibles }} cupos) @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div id="campoModalidad" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Modalidad Destino</label>
                        <select name="modalidad_destino" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue">
                            <option value="Presencial">Presencial</option>
                            <option value="Virtual">Virtual</option>
                            <option value="Semipresencial">Semipresencial</option>
                        </select>
                    </div>
                    <div id="campoTurno" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Turno Destino</label>
                        <select name="turno_destino" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue">
                            <option value="mañana">Mañana</option>
                            <option value="tardenoche">TardeNoche</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                        <textarea name="motivo" required rows="3" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue" placeholder="Explique el motivo de la solicitud..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalSolicitudCambio').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Enviar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleCamposCambio() {
        const tipo = document.getElementById('tipoSolicitud').value;
        document.getElementById('campoComision').classList.toggle('hidden', tipo !== 'comision');
        document.getElementById('campoModalidad').classList.toggle('hidden', tipo !== 'modalidad');
        document.getElementById('campoTurno').classList.toggle('hidden', tipo !== 'turno');
    }
</script>
@endpush
@endsection
