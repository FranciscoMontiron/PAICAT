@extends('layouts.app')
@section('title', 'Reportes')
@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Reportes y Estadisticas</h1>
            <p class="text-gray-600 mt-1">Analisis de datos del curso de ingreso</p>
        </div>
    </div>

    <!-- Reportes disponibles -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Asistencias -->
        <a href="{{ route('reportes.reporteasistenciascomision') }}"
           class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-200 overflow-hidden group border border-gray-100 hover:border-utn-blue/30">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">Asistencias</h3>
                        <p class="text-sm text-gray-500 mt-1">Analisis de asistencia por comision, especialidad y materia. Vista general y detallada con graficos.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">Filtros por fecha, comision, materia</span>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <!-- Inscripciones -->
        <a href="{{ route('reportes.reporteinscriones') }}"
           class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-200 overflow-hidden group border border-gray-100 hover:border-utn-blue/30">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">Inscripciones</h3>
                        <p class="text-sm text-gray-500 mt-1">Distribucion por modalidad, especialidad, genero y rangos etarios de los inscriptos.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">Filtros por anio de ingreso</span>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <!-- Rendimiento Academico -->
        <a href="{{ route('reportes.reporterendimiento') }}"
           class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-200 overflow-hidden group border border-gray-100 hover:border-utn-blue/30">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">Rendimiento Academico</h3>
                        <p class="text-sm text-gray-500 mt-1">Aprobados y desaprobados por materia y comision. Graficos de rendimiento por evaluacion.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">Filtros por materia, comision, fecha</span>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <!-- Alumnos -->
        <a href="{{ route('reportes.reportealumnos') }}"
           class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-200 overflow-hidden group border border-gray-100 hover:border-utn-blue/30">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">Ficha de Alumnos</h3>
                        <p class="text-sm text-gray-500 mt-1">Listado de alumnos con acceso a ficha individual: asistencia, evaluaciones y estado academico.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">Filtros por comision</span>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <!-- Desercion / Abandono -->
        <a href="{{ route('reportes.desercion') }}"
           class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-200 overflow-hidden group border border-gray-100 hover:border-utn-blue/30">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">Desercion y Abandono</h3>
                        <p class="text-sm text-gray-500 mt-1">Alumnos que dejaron de asistir. Identifica patrones de abandono por comision y especialidad.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">Filtros por comision, dias sin asistir</span>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <!-- Resumen por Comision -->
        <a href="{{ route('reportes.resumen-comisiones') }}"
           class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-200 overflow-hidden group border border-gray-100 hover:border-utn-blue/30">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">Resumen por Comision</h3>
                        <p class="text-sm text-gray-500 mt-1">Vista comparativa de todas las comisiones: ocupacion, asistencia promedio, rendimiento y docente a cargo.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">Filtros por periodo, anio</span>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

    </div>
</div>
@endsection
