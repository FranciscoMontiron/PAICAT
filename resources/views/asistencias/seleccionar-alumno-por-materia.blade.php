@extends('layouts.app')

@section('title', 'Seleccionar Alumno para Justificar Inasistencias')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-user-check mr-2"></i>Seleccionar Alumno - {{ $materia->nombre }}
        </h1>
        <p class="text-gray-600 mt-2">
            Comisión: <span class="font-semibold">{{ $comision->nombre }}</span> | 
            Materia: <span class="font-semibold">{{ $materia->nombre }}</span>
        </p>
    </div>

    @if($inscripciones->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="text-yellow-500 text-5xl mb-4">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">¡No hay alumnos con inasistencias!</h3>
            <p class="text-gray-600 mb-4">Todos los alumnos tienen sus asistencias al día en esta materia.</p>
            <a href="{{ route('asistencias.materia.historial', [$comision, $materia]) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Historial
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-700">
                    Alumnos con inasistencias sin justificar
                    <span class="ml-2 px-3 py-1 bg-red-100 text-red-800 text-sm rounded-full">
                        {{ $inscripciones->count() }} alumnos
                    </span>
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Selecciona un alumno para justificar sus inasistencias en {{ $materia->nombre }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Alumno
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Documento
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Inasistencias sin justificar
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Última inasistencia
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($inscripciones as $inscripcion)
                            @php
                                $alumno = $inscripcion->academicoDato->user ?? null;
                                $ausencias = $inscripcion->asistencias->where('materia_id', $materia->id)->where('estado', 'ausente');
                                $ultimaAusencia = $ausencias->first();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $alumno ? $alumno->name : 'Alumno no encontrado' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $alumno ? $alumno->email : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $inscripcion->academicoDato->documento ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $ausencias->count() > 3 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $ausencias->count() }} inasistencias
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($ultimaAusencia)
                                        {{ \Carbon\Carbon::parse($ultimaAusencia->fecha)->format('d/m/Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('asistencias.alumno.justificar', [$comision, $inscripcion]) }}?materia_id={{ $materia->id }}" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit mr-1"></i> Justificar
                                    </a>
                                    <a href="{{ route('asistencias.alumno.historial', [$comision, $inscripcion]) }}?materia_id={{ $materia->id }}" 
                                       class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-history mr-1"></i> Historial
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t">
                <div class="flex justify-between items-center">
                    <a href="{{ route('asistencias.materia.historial', [$comision, $materia]) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al Historial
                    </a>
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ $inscripciones->count() }}</span> alumnos con inasistencias por justificar
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection