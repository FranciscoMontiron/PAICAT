@extends('layouts.app')
@section('title', 'Detalle de Comisión')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-3xl font-bold text-gray-800">{{ $comision->nombre }}</h1>
                    @php
                        $estadoClasses = [
                            'activa' => 'bg-green-100 text-green-800',
                            'cerrada' => 'bg-yellow-100 text-yellow-800',
                            'finalizada' => 'bg-blue-100 text-blue-800',
                            'cancelada' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $estadoClasses[$comision->estado] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($comision->estado) }}
                    </span>
                </div>
                <p class="text-gray-600 mt-1">Código: {{ $comision->codigo }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('comisiones.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg transition duration-200">
                    Volver
                </a>
                @if(auth()->user()->hasPermission('comisiones.editar'))
                <a href="{{ route('comisiones.edit', $comision) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200">
                    Editar
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Inscriptos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['inscriptos'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Cupos Disponibles</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['cupos_disponibles'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Ocupación</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['porcentaje_ocupacion'], 1) }}%</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Evaluaciones</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['evaluaciones'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información General -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Información General</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Año</label>
                            <p class="mt-1 text-gray-900">{{ $comision->anio }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Periodo</label>
                            <p class="mt-1 text-gray-900">{{ $comision->periodo }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Turno</label>
                            <p class="mt-1 text-gray-900">{{ $comision->turno }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Modalidad</label>
                            <p class="mt-1 text-gray-900">{{ $comision->modalidad }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Cupo Máximo</label>
                            <p class="mt-1 text-gray-900">{{ $comision->cupo_maximo }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Cupo Actual</label>
                            <p class="mt-1 text-gray-900">{{ $comision->cupo_actual }}</p>
                        </div>
                        @if($comision->fecha_inicio)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Fecha de Inicio</label>
                            <p class="mt-1 text-gray-900">{{ $comision->fecha_inicio->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        @if($comision->fecha_fin)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Fecha de Fin</label>
                            <p class="mt-1 text-gray-900">{{ $comision->fecha_fin->format('d/m/Y') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($comision->descripcion)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Descripción</label>
                        <p class="mt-1 text-gray-900">{{ $comision->descripcion }}</p>
                    </div>
                    @endif

                    @if($comision->observaciones)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Observaciones</label>
                        <p class="mt-1 text-gray-900">{{ $comision->observaciones }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Alumnos Inscritos -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-xl font-bold text-gray-800">Alumnos Inscritos</h2>
                        <span class="text-sm text-gray-600">{{ $comision->inscripciones->count() }} alumnos</span>
                    </div>
                    @if(auth()->user()->hasPermission('comisiones.editar'))
                    <button onclick="alert('Funcionalidad en desarrollo')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Agregar Alumno
                    </button>
                    @endif
                </div>
                <div class="p-6">
                    @if($comision->inscripciones->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Alumno</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inscripción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($comision->inscripciones as $inscripcion)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $inscripcion->academicoDato->user->nombre_completo }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $inscripcion->academicoDato->user->email }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ ucfirst($inscripcion->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $inscripcion->fecha_inscripcion->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-center text-gray-500 py-8">No hay alumnos inscritos en esta comisión aún.</p>
                    @endif
                </div>
            </div>

            <!-- Evaluaciones -->
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Evaluaciones</h2>
                </div>
                <div class="p-6">
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Gestionar Evaluaciones</h3>
                        <p class="mt-1 text-sm text-gray-500">Ver, crear y administrar las evaluaciones de esta comisión</p>
                        @if(auth()->user()->hasPermission('evaluaciones.ver'))
                        <div class="mt-6">
                            {{-- TODO: Implementar en rama feature/evaluaciones --}}
                            {{-- <a href="{{ route('comisiones.evaluaciones.index', $comision) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700"> --}}
                            <button onclick="alert('Funcionalidad a implementar en rama feature/evaluaciones')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Ver Evaluaciones ({{ $stats['evaluaciones'] }})
                            </button>
                            {{-- </a> --}}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Docente -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Docente</h2>
                </div>
                <div class="p-6">
                    @if($comision->docente)
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $comision->docente->nombre_completo }}</p>
                            <p class="text-sm text-gray-600">{{ $comision->docente->email }}</p>
                        </div>
                    </div>
                    @else
                    <p class="text-gray-500 italic">Sin docente asignado</p>
                    @if(auth()->user()->hasPermission('comisiones.editar'))
                    <button class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Asignar Docente
                    </button>
                    @endif
                    @endif
                </div>
            </div>

            <!-- Acciones Rápidas -->
            @if(auth()->user()->hasPermission('comisiones.editar'))
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Cambiar Estado</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('comisiones.cambiarEstado', $comision) }}" method="POST">
                        @csrf
                        <select name="estado" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 mb-3">
                            <option value="activa" {{ $comision->estado == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="cerrada" {{ $comision->estado == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                            <option value="finalizada" {{ $comision->estado == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                            <option value="cancelada" {{ $comision->estado == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            Actualizar Estado
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Información Adicional -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Información</h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Creada:</span>
                        <span class="text-gray-900">{{ $comision->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Última actualización:</span>
                        <span class="text-gray-900">{{ $comision->updated_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

