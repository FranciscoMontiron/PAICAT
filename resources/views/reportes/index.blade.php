@extends('layouts.app')
@section('title', 'Reportes')
@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-utn-blue">Módulo de Reportes y Estadísticas</h1>
            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">Módulo 5</span>
        </div>
        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Funcionalidades a Desarrollar</h3>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Generar reporte de inscripciones por período</li>
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Generar estadísticas de asistencia por comisión</li>
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Generar estadísticas de rendimiento académico</li>
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Generar reporte de aspirantes por procedencia</li>
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Dashboard con indicadores clave</li>
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Exportar reportes a PDF/Excel</li>
            </ul>
        </div>
    </div>
</div>
@endsection
