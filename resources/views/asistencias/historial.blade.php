@extends('layouts.app')
@section('title','Historial de Asistencias')
@section('content')
<div class="container mx-auto p-4">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
        <a href="{{ route('asistencias.index') }}" class="hover:text-blue-600">Asistencias</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('asistencias.comision', $inscripcionComision->comision->id) }}" class="hover:text-blue-600">
            {{ $inscripcionComision->comision->nombre }}
        </a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-800 font-medium">{{ $inscripcionComision->academicoDato->user->name ?? $inscripcionComision->alumno->name ?? 'Alumno' }}</span>
    </div>

    <div class="bg-white border-b border-gray-200 shadow-sm sm:rounded-lg">
        <div class="p-6">
            
            <!-- Encabezado -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-utn-blue mb-1">
                        {{ $inscripcionComision->academicoDato->user->name ?? $inscripcionComision->alumno->name ?? 'Alumno' }}
                    </h1>
                    <p class="text-gray-600">Comisión: {{ $inscripcionComision->comision->nombre }} - {{ $inscripcionComision->comision->turno }}</p>
                </div>
                <a href="{{ route('asistencias.comision', $inscripcionComision->comision->id) }}" 
                   class="flex items-center gap-2 text-gray-600 hover:text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Volver a Comisión
                </a>
            </div>

            <!-- Tarjeta de porcentaje principal -->
            <div class="mb-6 p-6 rounded-lg {{ $estaEnRiesgo ? 'bg-red-50 border-2 border-red-200' : ($porcentaje >= 85 ? 'bg-green-50 border-2 border-green-200' : 'bg-yellow-50 border-2 border-yellow-200') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium mb-2 {{ $estaEnRiesgo ? 'text-red-700' : ($porcentaje >= 85 ? 'text-green-700' : 'text-yellow-700') }}">
                            Porcentaje de Asistencia
                        </p>
                        <p class="text-5xl font-bold {{ $estaEnRiesgo ? 'text-red-900' : ($porcentaje >= 85 ? 'text-green-900' : 'text-yellow-900') }}">
                            {{ round($porcentaje, 1) }}%
                        </p>
                    </div>
                    @if($estaEnRiesgo)
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="bg-red-600 text-white px-4 py-2 rounded-full text-sm font-bold">
                                ⚠️ EN RIESGO
                            </span>
                        </div>
                    @elseif($porcentaje >= 85)
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="bg-green-600 text-white px-4 py-2 rounded-full text-sm font-bold">
                                ✓ EXCELENTE
                            </span>
                        </div>
                    @else
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-yellow-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="bg-yellow-600 text-white px-4 py-2 rounded-full text-sm font-bold">
                                ⚠ PRECAUCIÓN
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total'] }}</p>
                    <p class="text-sm text-gray-600 mt-1">Total</p>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-900">{{ $estadisticas['presentes'] }}</p>
                    <p class="text-sm text-green-700 mt-1">Presentes</p>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-red-900">{{ $estadisticas['ausentes'] }}</p>
                    <p class="text-sm text-red-700 mt-1">Ausentes</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-900">{{ $estadisticas['tardanzas'] }}</p>
                    <p class="text-sm text-yellow-700 mt-1">Tardanzas</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-blue-900">{{ $estadisticas['justificadas'] }}</p>
                    <p class="text-sm text-blue-700 mt-1">Justificadas</p>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <a href="{{ route('asistencias.justificar', $inscripcionComision->comision->id) }}" 
                   class="flex items-center justify-center gap-2 p-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    Justificar Inasistencia
                </a>
                <button onclick="window.print()" 
                        class="flex items-center justify-center gap-2 p-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    Imprimir Historial
                </button>
            </div>

            <!-- Tabla de historial -->
            <div class="border-t pt-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Historial de Asistencias</h3>
                
                @if($inscripcionComision->asistencias->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b-2 border-gray-300">
                                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inscripcionComision->asistencias as $asistencia)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="border border-gray-300 px-4 py-3 text-sm">
                                            {{ $asistencia->fecha->format('d/m/Y') }}
                                            <span class="text-xs text-gray-500 block">{{ $asistencia->fecha->locale('es')->isoFormat('dddd') }}</span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3">
                                            @php
                                                $estadoConfig = [
                                                    'presente' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Presente'],
                                                    'ausente' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Ausente'],
                                                    'tardanza' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Tardanza'],
                                                    'justificado' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Justificado'],
                                                ];
                                                $config = $estadoConfig[$asistencia->estado] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($asistencia->estado)];
                                            @endphp
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                                {{ $config['label'] }}
                                            </span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-sm text-gray-700">
                                            {{ $asistencia->observaciones ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-lg font-semibold mb-2">No hay registros de asistencia</p>
                        <p class="text-sm">Aún no se ha registrado ninguna asistencia para este alumno</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>

<!-- Estilos para impresión -->
<style>
    @media print {
        .no-print, nav, footer, button, a {
            display: none !important;
        }
        body {
            background: white !important;
        }
    }
</style>
@endsection