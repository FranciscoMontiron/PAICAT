<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PAICAT') }} - @yield('title', 'Sistema de Gestión del Curso de Ingreso')</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Tipografía institucional UTN: Arial */
        body {
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header institucional UTN -->
        <header class="bg-utn-blue">
            <!-- Barra superior con logo -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo UTN -->
                    <a href="{{ route('home') }}" class="flex items-center space-x-4">
                        <img src="{{ asset('images/logo-horizontal-blanco.png') }}" alt="UTN - Facultad Regional La Plata" class="h-12">
                        <div class="hidden sm:block border-l border-white/30 pl-4">
                            <span class="text-white font-bold text-lg tracking-wide">PAICAT</span>
                            <p class="text-white/70 text-xs">Sistema de Gestión del Curso de Ingreso</p>
                        </div>
                    </a>

                    <!-- Usuario y logout - Desktop -->
                    <div class="hidden md:flex items-center space-x-4" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-white hover:bg-white/10 transition-all duration-200">
                            <div class="w-9 h-9 rounded-full bg-utn-orange flex items-center justify-center">
                                <span class="text-sm font-bold text-white">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}{{ substr(auth()->user()->apellido ?? '', 0, 1) }}</span>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-medium">{{ auth()->user()->nombre_completo ?? 'Usuario' }}</div>
                                <div class="text-xs text-white/70">{{ auth()->user()->roles()->first()->nombre ?? 'Usuario' }}</div>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown -->
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-4 top-20 mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black/5 z-50" style="display: none;">
                            <div class="py-1">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->nombre_completo ?? 'Usuario' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden" x-data="{ mobileOpen: false }">
                        <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-lg text-white hover:bg-white/10">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navegación principal -->
            <nav class="bg-utn-blue-dark border-t border-white/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="hidden md:flex space-x-1 py-1">
                        {{-- Dashboard --}}
                        <a href="{{ route('home') }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ request()->routeIs('home') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Inicio
                            </span>
                        </a>

                        {{-- MENÚ: Alumnos --}}
                        @if(auth()->user()->hasPermission('inscripciones.ver'))
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('inscripciones.*') || request()->routeIs('trayectorias.*') || request()->routeIs('cursadas.*') || request()->routeIs('solicitudes.*') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Alumnos
                                <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute left-0 mt-0 w-56 bg-white rounded-b-lg shadow-xl z-50" style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('inscripciones.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('inscripciones.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Inscripciones
                                    </a>
                                    <a href="{{ route('trayectorias.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('trayectorias.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Trayectorias
                                    </a>
                                    @if(auth()->user()->hasPermission('comisiones.ver'))
                                    <a href="{{ route('cursadas.global') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('cursadas.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        Cursadas
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('comisiones.editar'))
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ route('solicitudes.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('solicitudes.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                        Solicitudes de Cambio
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- MENÚ: Académico --}}
                        @if(auth()->user()->hasPermission('comisiones.ver'))
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('comisiones.*') || request()->routeIs('asistencias.*') || request()->routeIs('evaluaciones.*') || request()->routeIs('materias.*') || request()->routeIs('asignacion-alumnos.*') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Académico
                                <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute left-0 mt-0 w-56 bg-white rounded-b-lg shadow-xl z-50" style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('comisiones.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('comisiones.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Comisiones
                                    </a>
                                    @if(auth()->user()->hasPermission('asistencias.ver'))
                                    <a href="{{ route('asistencias.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('asistencias.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        Asistencias
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('evaluaciones.ver'))
                                    <a href="{{ route('evaluaciones.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('evaluaciones.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Evaluaciones
                                    </a>
                                    @endif
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ route('materias.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('materias.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        Materias
                                    </a>
                                    @if(auth()->user()->hasPermission('comisiones.editar'))
                                    <a href="{{ route('asignacion-alumnos.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('asignacion-alumnos.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Asignación Aleatoria
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- MENÚ: Infraestructura --}}
                        @if(auth()->user()->hasPermission('comisiones.ver'))
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('municipios.*') || request()->routeIs('aulas.*') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Infraestructura
                                <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute left-0 mt-0 w-56 bg-white rounded-b-lg shadow-xl z-50" style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('municipios.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('municipios.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Municipios / Sedes
                                    </a>
                                    <a href="{{ route('aulas.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('aulas.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Aulas
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Reportes --}}
                        @if(auth()->user()->hasPermission('reportes.ver'))
                        <a href="{{ route('reportes.index') }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ request()->routeIs('reportes.*') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Reportes
                            </span>
                        </a>
                        @endif

                        {{-- MENÚ: Administración --}}
                        @if(auth()->user()->hasPermission('usuarios.ver') || config('app.debug'))
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('usuarios.*') || request()->routeIs('developer') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Administración
                                <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute left-0 mt-0 w-56 bg-white rounded-b-lg shadow-xl z-50" style="display: none;">
                                <div class="py-1">
                                    @if(auth()->user()->hasPermission('usuarios.ver'))
                                    <a href="{{ route('usuarios.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('usuarios.*') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        Usuarios
                                    </a>
                                    @endif
                                    @if(config('app.debug'))
                                    <a href="{{ route('developer') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('developer') ? 'bg-blue-50 text-utn-blue font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                        </svg>
                                        Desarrollo
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </nav>
        </header>

        <!-- Barra de acento naranja UTN -->
        <div class="h-1 bg-utn-orange"></div>

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main class="flex-1 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        <!-- Footer institucional UTN -->
        <footer class="bg-utn-blue mt-auto">
            <div class="h-1 bg-utn-orange"></div>
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('images/logo-horizontal-blanco.png') }}" alt="UTN" class="h-8 opacity-80">
                        <div class="text-white/70 text-sm">
                            <p class="font-semibold text-white">PAICAT</p>
                            <p class="text-xs">Sistema de Gestión del Curso de Ingreso</p>
                        </div>
                    </div>
                    <div class="text-center md:text-right">
                        <p class="text-white/60 text-xs">
                            &copy; {{ date('Y') }} UTN - Facultad Regional La Plata
                        </p>
                        <p class="text-white/40 text-xs mt-1">
                            Secretaría Académica
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {{-- Stack para scripts adicionales de cada vista --}}
    @stack('scripts')
</body>

</html>