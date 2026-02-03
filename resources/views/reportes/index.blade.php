@extends('layouts.app')
@section('title', 'Reportes')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-utn-blue">Módulo de Reportes y Estadísticas</h1>
            </div>
        </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-utn-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            Módulos de Reportes
        </h2>
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            <!-- Botón Reportes Asistencias -->
            <a href="{{ route('reportes.reporteasistenciascomision') }}"
            class="group bg-white rounded-lg shadow-sm border border-gray-200 p-5
                    hover:border-utn-blue hover:shadow-md transition-all duration-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-utn-blue rounded-lg flex items-center justify-center flex-shrink-0
                                group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 group-hover:text-utn-blue transition-colors">
                            Reportes
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Reportes de asistencias por comisión
                        </p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-blue group-hover:translate-x-1 transition-all"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>

            <!-- Botón Reportes Inscripciones -->
            <a href="{{ route('reportes.reporteinscriones') }}"
            class="group bg-white rounded-lg shadow-sm border border-gray-200 p-5
                    hover:border-utn-blue hover:shadow-md transition-all duration-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-utn-blue rounded-lg flex items-center justify-center flex-shrink-0
                                group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 group-hover:text-utn-blue transition-colors">
                            Reportes de Inscripciones
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Reportes de inscripciones
                        </p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-blue group-hover:translate-x-1 transition-all"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>

        </div>
    </div>


        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Funcionalidades a Desarrollar</h3>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Generar reporte de inscripciones por período - Finalizado. </li>
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Generar estadísticas de asistencia por comisión - Finalizado.</li>
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Generar estadísticas de rendimiento académico</li>
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Generar reporte de aspirantes por procedencia</li>
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Dashboard con indicadores clave</li>
                <li class="flex items-center"><span class="text-purple-500 mr-2">•</span>Exportar reportes a PDF/Excel</li>
            </ul>
        </div>
    </div>
</div>
@endsection
