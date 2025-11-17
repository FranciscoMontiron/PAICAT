@extends('layouts.app')
@section('title', 'Asistencias')
@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-utn-blue">Módulo de Asistencias</h1>
            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">Módulo 3</span>
        </div>
        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Funcionalidades a Desarrollar</h3>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-center"><span class="text-yellow-500 mr-2">•</span>Registrar asistencia diaria</li>
                <li class="flex items-center"><span class="text-yellow-500 mr-2">•</span>Modificar asistencia</li>
                <li class="flex items-center"><span class="text-yellow-500 mr-2">•</span>Justificar inasistencia</li>
                <li class="flex items-center"><span class="text-yellow-500 mr-2">•</span>Ver historial de asistencias por alumno</li>
                <li class="flex items-center"><span class="text-yellow-500 mr-2">•</span>Calcular porcentaje de asistencia</li>
                <li class="flex items-center"><span class="text-yellow-500 mr-2">•</span>Alertar aspirantes con riesgo de perder por faltas</li>
            </ul>
        </div>
    </div>
</div>
@endsection
