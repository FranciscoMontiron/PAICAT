@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Bienvenida --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-utn-blue to-utn-blue-dark p-6">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Bienvenido, {{ auth()->user()->nombre ?? 'Usuario' }}
                </h1>
                <p class="text-white/80 mt-1">
                    Sistema de Gestión del Curso de Ingreso - UTN FRLP
                </p>
            </div>
        </div>
        <div class="h-1 bg-utn-orange"></div>
    </div>

    {{-- Estadísticas rápidas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Inscripciones --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Inscripciones</p>
                    <p class="text-3xl font-bold text-utn-blue mt-1">{{ $stats['inscripciones'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-utn-blue/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-utn-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-green-600 font-medium">Activas este período</span>
            </div>
        </div>

        {{-- Comisiones --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Comisiones</p>
                    <p class="text-3xl font-bold text-utn-orange mt-1">{{ $stats['comisiones'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-utn-orange/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-utn-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-gray-500">Disponibles</span>
            </div>
        </div>

        {{-- Usuarios --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Usuarios</p>
                    <p class="text-3xl font-bold text-gray-700 mt-1">{{ $stats['users'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-gray-500">En el sistema</span>
            </div>
        </div>

        {{-- Pendientes --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pendientes</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pendientes'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-yellow-600 font-medium">Por validar</span>
            </div>
        </div>
    </div>

    {{-- Accesos rápidos a módulos --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-utn-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            Módulos del Sistema
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Inscripciones --}}
            <a href="{{ route('inscripciones.index') }}" class="group bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:border-utn-blue hover:shadow-md transition-all duration-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-utn-blue rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 group-hover:text-utn-blue transition-colors">Inscripciones</h3>
                        <p class="text-sm text-gray-500 mt-1">Gestionar inscripciones al curso de ingreso</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-blue group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            {{-- Comisiones --}}
            <a href="{{ route('comisiones.index') }}" class="group bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:border-utn-orange hover:shadow-md transition-all duration-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-utn-orange rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 group-hover:text-utn-orange transition-colors">Comisiones</h3>
                        <p class="text-sm text-gray-500 mt-1">Administrar comisiones y asignaciones</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-utn-orange group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            {{-- Asistencias --}}
            <a href="{{ route('asistencias.index') }}" class="group bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:border-green-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 group-hover:text-green-600 transition-colors">Asistencias</h3>
                        <p class="text-sm text-gray-500 mt-1">Control de asistencias por comisión</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-green-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            {{-- Evaluaciones --}}
            <a href="{{ route('evaluaciones.index') }}" class="group bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:border-purple-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">Evaluaciones</h3>
                        <p class="text-sm text-gray-500 mt-1">Gestionar evaluaciones y notas</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-purple-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            {{-- Reportes --}}
            <a href="{{ route('reportes.index') }}" class="group bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:border-indigo-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-indigo-500 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Reportes</h3>
                        <p class="text-sm text-gray-500 mt-1">Generar reportes y estadísticas</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-indigo-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            {{-- Usuarios --}}
            <a href="{{ route('usuarios.index') }}" class="group bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:border-gray-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-gray-600 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 group-hover:text-gray-700 transition-colors">Usuarios</h3>
                        <p class="text-sm text-gray-500 mt-1">Administrar usuarios y permisos</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-gray-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>
        </div>
    </div>

    {{-- Información del usuario --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Tu sesión</h3>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-utn-blue flex items-center justify-center">
                <span class="text-lg font-bold text-white">{{ substr(auth()->user()->nombre ?? 'U', 0, 1) }}{{ substr(auth()->user()->apellido ?? '', 0, 1) }}</span>
            </div>
            <div>
                <p class="font-medium text-gray-900">{{ auth()->user()->nombre_completo ?? 'Usuario' }}</p>
                <p class="text-sm text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                <p class="text-xs text-utn-orange font-medium mt-1">{{ auth()->user()->roles()->first()->nombre ?? 'Usuario' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
