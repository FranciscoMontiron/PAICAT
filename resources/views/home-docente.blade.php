@extends('layouts.app')

@section('title', 'Mi Panel Docente')

@section('content')
<div class="space-y-6">
    {{-- Bienvenida --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-utn-dark to-utn-dark-light p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">
                        Bienvenido, {{ auth()->user()->nombre ?? 'Docente' }}
                    </h1>
                    <p class="text-white/80 mt-1">
                        Panel Docente - Curso de Ingreso UTN {{ \App\Services\ConfiguracionService::get('sigla_institucion', 'FRLP') }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0 text-white/70 text-sm">
                    <p>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
                </div>
            </div>
        </div>
        <div class="h-1 bg-utn-blue"></div>
    </div>

    {{-- Alertas --}}
    @if(count($alertas) > 0)
    <div class="space-y-2">
        @foreach($alertas as $alerta)
        <a href="{{ $alerta['url'] }}" class="block bg-{{ $alerta['tipo'] === 'warning' ? 'yellow' : ($alerta['tipo'] === 'danger' ? 'red' : 'blue') }}-50 border-l-4 border-{{ $alerta['tipo'] === 'warning' ? 'yellow' : ($alerta['tipo'] === 'danger' ? 'red' : 'blue') }}-500 p-4 hover:bg-{{ $alerta['tipo'] === 'warning' ? 'yellow' : ($alerta['tipo'] === 'danger' ? 'red' : 'blue') }}-100 transition-colors rounded-r-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    @if($alerta['icono'] === 'clock')
                    <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    @else
                    <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    @endif
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ $alerta['mensaje'] }}</p>
                </div>
                @if($alerta['url'] !== '#')
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                @endif
            </div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Estadísticas del docente --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Mis Comisiones</p>
                    <p class="text-2xl font-bold text-utn-dark-lighter mt-1">{{ $stats['mis_comisiones'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-utn-dark-lighter" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Mis Alumnos</p>
                    <p class="text-2xl font-bold text-utn-blue-dark mt-1">{{ $stats['mis_alumnos'] }}</p>
                </div>
                <div class="w-10 h-10 bg-utn-blue/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Asistencias Hoy</p>
                    <p class="text-2xl font-bold text-utn-blue-dark mt-1">{{ $stats['asistencias_hoy'] }}</p>
                </div>
                <div class="w-10 h-10 bg-utn-blue/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Mis Comisiones - Acceso rápido --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-utn-dark to-utn-dark-light px-5 py-3">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Mis Comisiones
            </h2>
        </div>
        <div class="p-4">
            @if($misComisiones->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($misComisiones as $comision)
                    @php
                        $esPresencial = strtolower($comision->modalidad ?? '') === 'presencial';
                    @endphp
                    <div class="border rounded-lg p-4 {{ $esPresencial ? 'border-gray-200 hover:border-utn-blue' : 'border-gray-200 bg-gray-50' }} hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-bold text-gray-800">{{ $comision->codigo }}</h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded
                                @if($comision->modalidad === 'Presencial') bg-utn-blue/10 text-utn-blue-dark
                                @elseif($comision->modalidad === 'Semipresencial') bg-utn-blue-dark/10 text-utn-dark
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ $comision->modalidad ?? 'Sin modalidad' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">{{ $comision->nombre }}</p>
                        <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                            <span>{{ $comision->turno }}</span>
                            <span>{{ $comision->alumnos_activos_count }} alumnos</span>
                        </div>

                        <div class="flex flex-col gap-2">
                            @if($esPresencial && auth()->user()->hasPermission('asistencias.ver'))
                                <a href="{{ route('asistencias.comision.materias', $comision) }}"
                                   class="w-full bg-utn-blue-darker hover:bg-utn-dark-light text-white text-center px-3 py-2 rounded-lg transition text-sm font-medium flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    Pasar Asistencia
                                </a>
                            @endif
                            <div class="flex gap-2">
                                <a href="{{ route('comisiones.show', $comision) }}"
                                   class="flex-1 bg-utn-blue-darker hover:bg-utn-dark text-white text-center px-3 py-2 rounded-lg transition text-sm font-medium">
                                    Ver Comisión
                                </a>
                                <a href="{{ route('asistencias.historial', $comision) }}"
                                   class="flex-1 bg-utn-blue/10 hover:bg-utn-blue/20 text-utn-blue-dark text-center px-3 py-2 rounded-lg transition text-sm font-medium">
                                    Historial
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p>No tenés comisiones asignadas actualmente</p>
                    <p class="text-sm mt-1">Contactá a la coordinación para que te asignen comisiones</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Accesos rápidos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Acciones frecuentes --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-utn-dark to-utn-dark-light px-5 py-3">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Acciones Rápidas
                </h2>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('asistencias.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-dark/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-dark-lighter" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-dark-lighter">Asistencias</p>
                        <p class="text-xs text-gray-500">Registrar y consultar asistencias</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-dark-lighter group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('evaluaciones.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-blue/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-blue-dark">Evaluaciones</p>
                        <p class="text-xs text-gray-500">Registrar y gestionar evaluaciones</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('inscripciones.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-utn-blue-dark/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-utn-blue-dark">Inscripciones</p>
                        <p class="text-xs text-gray-500">Consultar alumnos inscriptos</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-blue-dark group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                @if(auth()->user()->hasPermission('reportes.ver'))
                <a href="{{ route('reportes.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-gray-700">Reportes</p>
                        <p class="text-xs text-gray-500">Ver reportes de mis comisiones</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-gray-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
            </div>
        </div>

        {{-- Info del usuario --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-utn-blue-darker flex items-center justify-center">
                        <span class="text-xl font-bold text-white">{{ substr(auth()->user()->nombre ?? 'D', 0, 1) }}{{ substr(auth()->user()->apellido ?? '', 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 text-lg">{{ auth()->user()->nombre_completo ?? 'Docente' }}</p>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                        <p class="text-xs text-utn-dark-lighter font-medium mt-1">Docente</p>
                    </div>
                </div>
            </div>

            {{-- Resumen rápido de comisiones --}}
            @if($misComisiones->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase">Resumen de Comisiones</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($misComisiones as $comision)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">{{ $comision->codigo }}</p>
                            <p class="text-xs text-gray-500">{{ $comision->turno }} - {{ $comision->modalidad }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-utn-blue-dark">{{ $comision->alumnos_activos_count }}</p>
                            <p class="text-xs text-gray-500">alumnos</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection