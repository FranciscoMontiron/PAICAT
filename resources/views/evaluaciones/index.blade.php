@extends('layouts.app')
@section('title', 'Evaluaciones')
@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-utn-blue">Módulo de Evaluaciones y Notas</h1>
            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">Módulo 4</span>
        </div>
        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Funcionalidades a Desarrollar</h3>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-center"><span class="text-red-500 mr-2">•</span>Crear evaluación</li>
                <li class="flex items-center"><span class="text-red-500 mr-2">•</span>Cargar notas de evaluación</li>
                <li class="flex items-center"><span class="text-red-500 mr-2">•</span>Modificar nota</li>
                <li class="flex items-center"><span class="text-red-500 mr-2">•</span>Calcular promedio ponderado</li>
                <li class="flex items-center"><span class="text-red-500 mr-2">•</span>Determinar condición final (Aprobado/Desaprobado)</li>
                <li class="flex items-center"><span class="text-red-500 mr-2">•</span>Registrar recuperatorio</li>
                <li class="flex items-center"><span class="text-red-500 mr-2">•</span>Ver historial de notas por alumno</li>
                <li class="flex items-center"><span class="text-red-500 mr-2">•</span>Exportar acta de notas</li>
            </ul>
        </div>
    </div>
</div>
@endsection
