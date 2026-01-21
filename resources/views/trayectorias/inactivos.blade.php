@extends('layouts.app')
@section('title', 'Estudiantes Inactivos')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('trayectorias.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Estudiantes Inactivos</h1>
                <p class="text-gray-600">Sin asistencia registrada en los últimos {{ $diasInactividad }} días</p>
            </div>
        </div>
    </div>

    {{-- Alerta --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <div>
            <p class="text-amber-800 font-medium">Atención requerida</p>
            <p class="text-amber-700 text-sm">Estos estudiantes no han tenido asistencia registrada como "presente" en el período configurado. Considere contactarlos o revisar su situación.</p>
        </div>
    </div>

    {{-- Config días --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Días de inactividad</label>
                <select name="dias" class="rounded-lg border-gray-300 text-sm">
                    <option value="7" {{ $diasInactividad == 7 ? 'selected' : '' }}>7 días</option>
                    <option value="14" {{ $diasInactividad == 14 ? 'selected' : '' }}>14 días</option>
                    <option value="21" {{ $diasInactividad == 21 ? 'selected' : '' }}>21 días</option>
                    <option value="30" {{ $diasInactividad == 30 ? 'selected' : '' }}>30 días</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                Actualizar
            </button>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Trayectoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Asistencia</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($inactivos as $inscripcion)
                    @php
                    $person = $inscripcion->getPerson();
                    $trayectoria = $inscripcion->trayectoriaActual;
                    $ultimaAsistencia = $inscripcion->asistencias()->where('estado', 'presente')->latest('fecha')->first();
                    @endphp
                    <tr class="hover:bg-amber-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 flex-shrink-0 bg-amber-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-amber-600">{{ substr($person->nombre ?? 'N', 0, 1) }}{{ substr($person->apellido ?? 'A', 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $person->nombre ?? 'N/A' }} {{ $person->apellido ?? '' }}</div>
                                    <div class="text-sm text-gray-500">DNI: {{ $person->documento ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $inscripcion->especialidad_nombre ?? 'Sin asignar' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($trayectoria)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $trayectoria->estado == 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($trayectoria->estado) }}
                            </span>
                            @else
                            <span class="text-gray-400 text-sm">Sin registro</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($ultimaAsistencia)
                            <span class="text-gray-600">{{ $ultimaAsistencia->fecha->format('d/m/Y') }}</span>
                            <span class="text-gray-400">({{ $ultimaAsistencia->fecha->diffForHumans() }})</span>
                            @else
                            <span class="text-red-500">Nunca registrada</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('trayectorias.show', $inscripcion) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Ver
                            </a>
                            <a href="{{ route('trayectorias.show', $inscripcion) }}#modalBaja" class="text-red-600 hover:text-red-900">
                                Registrar Baja
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-green-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-500 font-medium">¡Excelente!</p>
                                <p class="text-gray-400 text-sm">No hay estudiantes inactivos en el período seleccionado.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inactivos->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $inactivos->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection