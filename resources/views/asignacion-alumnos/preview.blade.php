@extends('layouts.app')
@section('title', 'Vista Previa - Asignación Aleatoria')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Vista Previa de Asignación</h1>
            <p class="text-gray-600 mt-1">Revisa la asignación propuesta antes de confirmar</p>
        </div>
        <a href="{{ route('asignacion-alumnos.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver
        </a>
    </div>

    <!-- Resumen -->
    @php
    $totalAsignar = collect($simulacion)->sum('cantidadAsignados');
    @endphp
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">{{ $totalAsignar }} alumnos serán asignados</h2>
                <p class="opacity-90">a {{ count($simulacion) }} comisiones</p>
            </div>
            <div class="bg-white/20 p-4 rounded-full">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Formulario de confirmación -->
    <form method="POST" action="{{ route('asignacion-alumnos.ejecutar') }}" id="formConfirmar">
        @csrf
        @foreach($comisionesIds as $id)
        <input type="hidden" name="comisiones[]" value="{{ $id }}">
        @endforeach

        <!-- Detalle por comisión -->
        <div class="space-y-6 mb-6">
            @foreach($simulacion as $item)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ $item['comision']->nombre }}
                                <span class="font-mono text-sm text-gray-500 ml-2">({{ $item['comision']->codigo }})</span>
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ $item['comision']->turno }} - {{ $item['comision']->modalidad }}
                                @if($item['comision']->docente)
                                | Docente: {{ $item['comision']->docente->nombre_completo }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-green-600">{{ $item['cantidadAsignados'] }}</span>
                            <span class="text-gray-500">/ {{ $item['cuposDisponibles'] }} cupos</span>
                        </div>
                    </div>
                </div>

                @if($item['asignados']->count() > 0)
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($item['asignados'] as $inscripcion)
                        @php
                        $person = $inscripcion->getPerson();
                        @endphp
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-700 font-semibold text-sm">
                                    {{ $person ? strtoupper(substr($person->nombre ?? '', 0, 1) . substr($person->apellido ?? '', 0, 1)) : '?' }}
                                </span>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $person ? ($person->apellido . ', ' . $person->nombre) : 'Inscripción #' . $inscripcion->id }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $inscripcion->especialidad_nombre ?? 'Sin especialidad' }}
                                </p>
                            </div>
                            <label class="flex items-center cursor-pointer ml-2" title="Excluir de la asignación">
                                <input type="checkbox" name="excluir[]" value="{{ $inscripcion->id }}"
                                    class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="ml-1 text-xs text-gray-500">Excluir</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="p-6 text-center text-gray-500">
                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>No hay alumnos elegibles para esta comisión</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-between items-center bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">
                <svg class="w-5 h-5 inline-block mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Marca "Excluir" para quitar alumnos específicos de la asignación
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('asignacion-alumnos.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-lg transition duration-200">
                    Cancelar
                </a>
                @if($totalAsignar > 0)
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center shadow-md"
                    onclick="return confirm('¿Estás seguro de ejecutar la asignación de {{ $totalAsignar }} alumnos?')">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Confirmar Asignación
                </button>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection