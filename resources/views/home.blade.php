@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Bienvenida --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-utn-dark to-utn-dark-light p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">
                        Bienvenido, {{ auth()->user()->nombre ?? 'Usuario' }}
                    </h1>
                    <p class="text-white/80 mt-1">
                        Sistema de Gestión del Curso de Ingreso - UTN {{ \App\Services\ConfiguracionService::get('sigla_institucion', 'FRLP') }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0 text-white/70 text-sm">
                    <p>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
                </div>
            </div>
        </div>
        <div class="h-1 bg-utn-blue"></div>
    </div>

    {{-- Alertas Importantes --}}
    @if(count($alertas) > 0)
    <div class="space-y-2">
        @foreach($alertas as $alerta)
        @if(empty($alerta['permiso']) || auth()->user()->hasPermission($alerta['permiso']))
        <a href="{{ $alerta['url'] }}" class="block bg-{{ $alerta['tipo'] === 'warning' ? 'yellow' : ($alerta['tipo'] === 'danger' ? 'red' : 'blue') }}-50 border-l-4 border-{{ $alerta['tipo'] === 'warning' ? 'yellow' : ($alerta['tipo'] === 'danger' ? 'red' : 'blue') }}-500 p-4 hover:bg-{{ $alerta['tipo'] === 'warning' ? 'yellow' : ($alerta['tipo'] === 'danger' ? 'red' : 'blue') }}-100 transition-colors rounded-r-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    @if($alerta['icono'] === 'clock')
                    <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    @elseif($alerta['icono'] === 'refresh')
                    <svg class="h-5 w-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    @else
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    @endif
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ $alerta['mensaje'] }}</p>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>
        @endif
        @endforeach
    </div>
    @endif

    {{-- Estadísticas rápidas --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
        {{-- Inscripciones --}}
        @if(auth()->user()->hasPermission('inscripciones.ver'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Inscriptos</p>
                    <p class="text-2xl font-bold text-utn-blue-dark mt-1">{{ $stats['inscripciones'] }}</p>
                </div>
                <div class="w-10 h-10 bg-utn-blue/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        {{-- Cursando --}}
        @if(auth()->user()->hasPermission('comisiones.ver'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Cursando</p>
                    <p class="text-2xl font-bold text-utn-dark-lighter mt-1">{{ $stats['alumnos_cursando'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-utn-dark-lighter" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        {{-- Comisiones Activas --}}
        @if(auth()->user()->hasPermission('comisiones.ver'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Comisiones</p>
                    <p class="text-2xl font-bold text-utn-blue-dark mt-1">{{ $stats['comisiones_activas'] }}</p>
                </div>
                <div class="w-10 h-10 bg-utn-blue/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        {{-- Inscripciones Pendientes --}}
        @if(auth()->user()->hasPermission('inscripciones.ver'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Insc. Pendientes</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pendientes'] }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        {{-- Solicitudes Pendientes --}}
        @if(auth()->user()->hasPermission('comisiones.editar'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Solicitudes</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['solicitudes_pendientes'] }}</p>
                </div>
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        {{-- Municipios --}}
        @if(auth()->user()->hasPermission('comisiones.crear'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Sedes</p>
                    <p class="text-2xl font-bold text-utn-blue-dark mt-1">{{ $stats['municipios'] }}</p>
                </div>
                <div class="w-10 h-10 bg-utn-blue/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Aulas --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Aulas</p>
                    <p class="text-2xl font-bold text-utn-blue-dark mt-1">{{ $stats['aulas'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Accesos Rápidos por Categoría --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ALUMNOS --}}
        @if(auth()->user()->hasPermission('inscripciones.ver'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-utn-blue-darker to-utn-blue-dark px-5 py-3">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Gestión de Alumnos
                </h2>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('inscripciones.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-blue/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-blue-dark">Inscripciones</p>
                        <p class="text-xs text-gray-500">Ver y gestionar inscripciones al curso</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @if(auth()->user()->hasPermission('inscripciones.editar'))
                <a href="{{ route('solicitudes.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-blue/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-blue-dark">Solicitudes de Cambio</p>
                        <p class="text-xs text-gray-500">Gestionar solicitudes de cambio de comisión</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
                @if(auth()->user()->hasPermission('inscripciones.editar'))
                <a href="{{ route('inscripciones.inactivos') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-blue/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-blue-dark">Alumnos Inactivos</p>
                        <p class="text-xs text-gray-500">Gestionar alumnos dados de baja</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- ACADÉMICO --}}
        @if(auth()->user()->hasPermission('comisiones.ver'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-utn-dark to-utn-dark-light px-5 py-3">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Gestión Académica
                </h2>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('comisiones.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-dark/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-dark-lighter" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-dark-lighter">Comisiones</p>
                        <p class="text-xs text-gray-500">Administrar comisiones y docentes</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-dark-lighter group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @if(auth()->user()->hasPermission('asistencias.ver'))
                <a href="{{ route('asistencias.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-dark/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-dark-lighter" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-dark-lighter">Asistencias</p>
                        <p class="text-xs text-gray-500">Control de asistencia por comisión</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-dark-lighter group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
                @if(auth()->user()->hasPermission('evaluaciones.ver'))
                <a href="{{ route('evaluaciones.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-dark/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-dark-lighter" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-dark-lighter">Evaluaciones</p>
                        <p class="text-xs text-gray-500">Gestionar evaluaciones y calificaciones</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-dark-lighter group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
                @if(auth()->user()->hasPermission('comisiones.editar'))
                <a href="{{ route('asignacion-alumnos.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-dark/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-dark-lighter" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-dark-lighter">Asignación Aleatoria</p>
                        <p class="text-xs text-gray-500">Asignar alumnos a comisiones</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-dark-lighter group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- INFRAESTRUCTURA (solo Admin/Coordinador) --}}
        @if(auth()->user()->hasPermission('comisiones.crear'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-utn-blue-dark to-utn-blue-darker px-5 py-3">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Infraestructura
                </h2>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('municipios.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-blue-dark/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-blue-dark">Municipios / Sedes</p>
                        <p class="text-xs text-gray-500">Gestionar sedes presenciales</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <a href="{{ route('aulas.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-blue-dark/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-blue-dark">Aulas</p>
                        <p class="text-xs text-gray-500">Administrar espacios físicos</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <a href="{{ route('materias.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-blue-dark/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-blue-dark">Materias</p>
                        <p class="text-xs text-gray-500">Configurar materias del curso</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
        @endif

        {{-- ADMINISTRACIÓN / REPORTES --}}
        @if(auth()->user()->hasPermission('reportes.ver') || auth()->user()->hasPermission('usuarios.ver'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-utn-dark-lighter to-utn-dark-light px-5 py-3">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Administración
                </h2>
            </div>
            <div class="p-4 space-y-2">
                @if(auth()->user()->hasPermission('reportes.ver'))
                <a href="{{ route('reportes.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-gray-700">Reportes</p>
                        <p class="text-xs text-gray-500">Generar reportes y estadísticas</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-gray-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
                @if(auth()->user()->hasPermission('usuarios.ver'))
                <a href="{{ route('usuarios.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-gray-700">Usuarios</p>
                        <p class="text-xs text-gray-500">Administrar usuarios y permisos</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-gray-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Información del usuario --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-utn-blue flex items-center justify-center">
                    <span class="text-lg font-bold text-white">{{ substr(auth()->user()->nombre ?? 'U', 0, 1) }}{{ substr(auth()->user()->apellido ?? '', 0, 1) }}</span>
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ auth()->user()->nombre_completo ?? 'Usuario' }}</p>
                    <p class="text-sm text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                    <p class="text-xs text-utn-blue-dark font-medium mt-1">{{ auth()->user()->roles()->first()->nombre ?? 'Usuario' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-red-600 flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
