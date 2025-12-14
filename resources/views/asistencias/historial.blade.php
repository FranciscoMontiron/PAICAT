@extends('layouts.app')
@section('title', 'Historial de Asistencias')
@section('content')
<div class="container mx-auto p-4">

    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-utn-blue">
                Historial de Asistencias: {{ $inscripcionComision->alumno?->name ?? 'Alumno Desconocido' }}
            </h1>
            <span class="px-3 py-1 rounded-full 
                {{ $estaEnRiesgo ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} 
                text-sm font-semibold">
                {{ $estaEnRiesgo ? 'En Riesgo' : 'Asistencia OK' }}
            </span>
        </div>

        <div class="mb-4">
            <p class="text-gray-700">
                Comisión: <strong>{{ $inscripcionComision->comision->nombre ?? 'Sin comisión' }}</strong><br>
                Porcentaje de asistencia: <strong>{{ $porcentaje }}%</strong>
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Fecha</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Estado</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Observaciones</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inscripcionComision->asistencias as $asistencia)
                        <tr class="border-t">
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $asistencia->fecha->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700 capitalize">{{ $asistencia->estado }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $asistencia->observaciones ?? '-' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $asistencia->registradoPor?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center text-gray-500">No hay registros de asistencia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-end space-x-4">
            <div class="text-gray-700">
                <p><strong>Total asistencias:</strong> {{ $estadisticas['total'] }}</p>
                <p><strong>Presentes:</strong> {{ $estadisticas['presentes'] }}</p>
                <p><strong>Ausentes:</strong> {{ $estadisticas['ausentes'] }}</p>
                <p><strong>Tardanzas:</strong> {{ $estadisticas['tardanzas'] }}</p>
                <p><strong>Justificadas:</strong> {{ $estadisticas['justificadas'] }}</p>
            </div>
        </div>
    </div>

    <a href="{{ route('asistencias.index') }}" class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Volver al Módulo de Asistencias</a>

</div>
@endsection
