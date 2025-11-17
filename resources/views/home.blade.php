@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <h1 class="text-3xl font-bold text-utn-blue mb-6">Dashboard - PAICAT</h1>

        <p class="text-gray-600 mb-8">
            Sistema de Gestión del Curso de Ingreso - UTN Facultad Regional La Plata
        </p>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Usuarios</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['users'] ?? 0 }}</p>
                    </div>
                    <svg class="w-10 h-10 text-blue-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-green-50 p-6 rounded-lg border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Inscripciones</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['inscripciones'] ?? 0 }}</p>
                    </div>
                    <svg class="w-10 h-10 text-green-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-yellow-50 p-6 rounded-lg border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Comisiones</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['comisiones'] ?? 0 }}</p>
                    </div>
                    <svg class="w-10 h-10 text-yellow-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Módulos del Sistema -->
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-utn-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            Módulos del Sistema
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Módulo Inscripciones -->
            <a href="{{ route('inscripciones.index') }}" class="group relative bg-gradient-to-br from-white to-blue-50 p-6 rounded-xl border-2 border-blue-200 hover:border-blue-400 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-blue-600 transition-colors duration-300">Inscripciones</h3>
                </div>
                <p class="text-gray-600 leading-relaxed">Gestión de inscripciones al curso de ingreso</p>
                <div class="mt-4 flex items-center text-blue-600 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                    <span>Acceder</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Módulo Comisiones -->
            <a href="{{ route('comisiones.index') }}" class="group relative bg-gradient-to-br from-white to-green-50 p-6 rounded-xl border-2 border-green-200 hover:border-green-400 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-green-600 transition-colors duration-300">Comisiones</h3>
                </div>
                <p class="text-gray-600 leading-relaxed">Gestión de comisiones y asignación de alumnos</p>
                <div class="mt-4 flex items-center text-green-600 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                    <span>Acceder</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Módulo Asistencias -->
            <a href="{{ route('asistencias.index') }}" class="group relative bg-gradient-to-br from-white to-yellow-50 p-6 rounded-xl border-2 border-yellow-200 hover:border-yellow-400 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-yellow-600 transition-colors duration-300">Asistencias</h3>
                </div>
                <p class="text-gray-600 leading-relaxed">Registro y control de asistencias</p>
                <div class="mt-4 flex items-center text-yellow-600 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                    <span>Acceder</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Módulo Evaluaciones -->
            <a href="{{ route('evaluaciones.index') }}" class="group relative bg-gradient-to-br from-white to-red-50 p-6 rounded-xl border-2 border-red-200 hover:border-red-400 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="bg-gradient-to-br from-red-500 to-red-600 p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-red-600 transition-colors duration-300">Evaluaciones</h3>
                </div>
                <p class="text-gray-600 leading-relaxed">Gestión de evaluaciones y notas</p>
                <div class="mt-4 flex items-center text-red-600 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                    <span>Acceder</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Módulo Reportes -->
            <a href="{{ route('reportes.index') }}" class="group relative bg-gradient-to-br from-white to-purple-50 p-6 rounded-xl border-2 border-purple-200 hover:border-purple-400 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-purple-600 transition-colors duration-300">Reportes</h3>
                </div>
                <p class="text-gray-600 leading-relaxed">Generación de reportes y estadísticas</p>
                <div class="mt-4 flex items-center text-purple-600 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                    <span>Acceder</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Módulo Usuarios -->
            <a href="{{ route('usuarios.index') }}" class="group relative bg-gradient-to-br from-white to-indigo-50 p-6 rounded-xl border-2 border-indigo-200 hover:border-indigo-400 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors duration-300">Usuarios</h3>
                </div>
                <p class="text-gray-600 leading-relaxed">Gestión de usuarios y permisos</p>
                <div class="mt-4 flex items-center text-indigo-600 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                    <span>Acceder</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
