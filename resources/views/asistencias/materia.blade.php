@extends('layouts.app')

@section('title', 'Historial - ' . $materia->nombre)

@section('content')
@php $asistenciaMinima = \App\Services\ConfiguracionService::get('asistencia_minima', 75); @endphp
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-6">
        <a href="{{ route('asistencias.index') }}" class="hover:text-utn-blue-dark transition">Asistencias</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('asistencias.comision.materias', $comision) }}" class="hover:text-utn-blue-dark transition">{{ $comision->codigo }}</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-800 font-medium">{{ $materia->nombre }}</span>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Historial de Asistencias</h1>
                <p class="text-gray-600 mt-2">{{ $materia->nombre }} - {{ $comision->codigo }}</p>
            </div>
            <div class="flex gap-3">
                @if(auth()->user()->hasPermission('asistencias.crear'))
                <a href="{{ route('asistencias.materia.registrar', [$comision, $materia]) }}" 
                   class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg transition">
                    Pasar Asistencia
                </a>
                @endif
                <a href="{{ route('asistencias.comision.materias', $comision) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600 mb-1">Total Clases</p>
            <p class="text-2xl font-bold text-gray-800">{{ $estadisticas['total_clases'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600 mb-1">Promedio Asistencia</p>
            <p class="text-2xl font-bold text-green-600">{{ round($estadisticas['promedio_asistencia'], 1) }}%</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600 mb-1">Alumnos en Riesgo</p>
            <p class="text-2xl font-bold text-red-600">{{ $estadisticas['alumnos_en_riesgo'] }}</p>
        </div>
    </div>

    <!-- Tabla de asistencias -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Registro de Asistencias</h2>
        </div>
        
        @if($fechasAsistencia->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alumno</th>
                        @foreach($fechasAsistencia as $fecha)
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                            {{ $fecha->format('d/m') }}
                        </th>
                        @endforeach
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">%</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($inscripciones as $inscripcion)
                    @php
                        $nombreAlumno = 'Sin nombre';
                        try {
                            $alumno = $inscripcion->getAlumnoAttribute();
                            $nombreAlumno = $alumno->name ?? $alumno->nombre_completo ?? 'Sin nombre';
                        } catch (\Exception $e) {
                            $nombreAlumno = 'Sin nombre';
                        }
                        
                        $asistenciasAlumno = $inscripcion->asistencias->keyBy(function($a) {
                            return $a->fecha->format('Y-m-d');
                        });
                        
                        $total = $asistenciasAlumno->count();
                        $presentes = $asistenciasAlumno->whereIn('estado', ['presente', 'justificado'])->count();
                        $tardanzas = $asistenciasAlumno->where('estado', 'tardanza')->count();
                        $porcentaje = $total > 0 ? (($presentes + ($tardanzas * 0.5)) / $total) * 100 : 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $nombreAlumno }}
                        </td>
                        @foreach($fechasAsistencia as $fecha)
                        @php
                            $asistencia = $asistenciasAlumno->get($fecha->format('Y-m-d'));
                            $estado = $asistencia->estado ?? null;
                        @endphp
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($estado)
                            <span class="px-2 py-1 text-xs font-semibold rounded
                                @if($estado == 'presente') bg-green-100 text-green-800
                                @elseif($estado == 'ausente') bg-red-100 text-red-800
                                @elseif($estado == 'tardanza') bg-yellow-100 text-yellow-800
                                @else bg-utn-blue/10 text-utn-blue-dark @endif">
                                @if($estado == 'presente') P
                                @elseif($estado == 'ausente') A
                                @elseif($estado == 'tardanza') T
                                @else J @endif
                            </span>
                            @else
                            <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold
                            @if($porcentaje >= $asistenciaMinima) text-green-600
                            @elseif($porcentaje >= 50) text-yellow-600
                            @else text-red-600 @endif">
                            {{ round($porcentaje, 1) }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Sin registros</h3>
            <p class="mt-2 text-sm text-gray-500">Aún no hay asistencias registradas para esta materia.</p>
        </div>
        @endif
    </div>
</div>
@endsection