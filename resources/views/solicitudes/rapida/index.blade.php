@extends('layouts.app')

@section('title', 'Solicitar Cambio de Comisión')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Solicitar Cambio de Comisión</h1>
        <p class="text-gray-600 mt-1">Buscá al alumno por DNI o nombre para gestionar el cambio de comisión.</p>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Banner docente --}}
    @if($esDocente)
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-utn-blue-dark flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-utn-blue-dark">
                <strong>Vista del Docente:</strong> Solo aparecen alumnos de las comisiones en las que estás asignado.
            </p>
        </div>
    </div>
    @endif

    {{-- Búsqueda --}}
    <div class="bg-white shadow-md rounded-lg p-5 mb-6">
        <form method="GET" action="{{ route('solicitud-cambio.index') }}" class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar alumno</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        name="buscar"
                        value="{{ $busqueda }}"
                        placeholder="DNI, apellido o nombre..."
                        autofocus
                        class="w-full pl-9 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                    >
                </div>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-utn-blue text-white rounded-lg hover:bg-utn-blue-dark transition-colors font-medium">
                Buscar
            </button>
            @if($busqueda)
            <a href="{{ route('solicitud-cambio.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Limpiar
            </a>
            @endif
        </form>
    </div>

    {{-- Resultados --}}
    @if($busqueda)
        @if($resultados->isEmpty())
        <div class="bg-white shadow-md rounded-xl p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-gray-500 font-medium">No se encontraron alumnos con comisión activa.</p>
            <p class="text-sm text-gray-400 mt-1">Verificá el DNI o nombre ingresado.</p>
        </div>
        @else
        <div class="bg-white shadow-md rounded-xl overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b flex items-center justify-between">
                <span class="text-sm font-medium text-gray-600">
                    {{ $resultados->count() }} {{ $resultados->count() === 1 ? 'resultado' : 'resultados' }} para
                    <strong>"{{ $busqueda }}"</strong>
                </span>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">DNI</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Comisión Actual</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($resultados as $inscripcion)
                    @php
                        $persona = $personas[$inscripcion->person_id] ?? null;
                        $ic = $inscripcion->inscripcionesComision->first();
                        $comision = $ic?->comision;
                        $tienePendiente = $inscripcion->solicitudesCambio()
                            ->whereIn('estado', ['pendiente','en_revision','trueque_detectado'])
                            ->exists();
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $persona?->apellido ?? '—' }}, {{ $persona?->nombre ?? '—' }}
                            </p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm text-gray-600 font-mono">{{ $persona?->documento ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($comision)
                                <p class="text-sm font-medium text-gray-800">{{ $comision->nombre }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $comision->turno ? ucfirst($comision->turno) . ' · ' : '' }}{{ $comision->modalidad ?? '' }}
                                </p>
                            @else
                                <span class="text-sm text-gray-400">Sin comisión</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($tienePendiente)
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full font-medium bg-yellow-100 text-yellow-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Solicitud en proceso
                                </span>
                            @elseif($comision)
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full font-medium bg-green-100 text-green-700">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Puede solicitar
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Sin comisión</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if($comision && !$tienePendiente)
                                <a href="{{ route('solicitud-cambio.form', $inscripcion) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-utn-blue text-white text-sm font-medium rounded-lg hover:bg-utn-blue-dark transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    Solicitar cambio
                                </a>
                            @elseif($tienePendiente)
                                <a href="{{ route('inscripciones.show', $inscripcion) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                    Ver ficha
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    @else
    {{-- Estado vacío inicial --}}
    <div class="bg-white shadow-md rounded-xl p-12 text-center">
        <div class="w-16 h-16 bg-utn-blue/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </div>
        <p class="text-gray-600 font-medium">Ingresá el DNI o nombre del alumno para comenzar</p>
        <p class="text-sm text-gray-400 mt-1">Solo aparecen alumnos con una comisión activa asignada.</p>
    </div>
    @endif

</div>
@endsection
