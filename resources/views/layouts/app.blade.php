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
        body { font-family: Arial, Helvetica, sans-serif; }
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

                        {{-- Inscripciones --}}
                        @if(auth()->user()->hasPermission('inscripciones.ver'))
                        <a href="{{ route('inscripciones.index') }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ request()->routeIs('inscripciones.*') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Inscripciones
                            </span>
                        </a>
                        @endif

                        {{-- Comisiones --}}
                        @if(auth()->user()->hasPermission('comisiones.ver'))
                        <a href="{{ route('comisiones.index') }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ request()->routeIs('comisiones.*') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Comisiones
                            </span>
                        </a>
                        @endif

                        {{-- Asistencias --}}
                        @if(auth()->user()->hasPermission('asistencias.ver'))
                        <a href="{{ route('asistencias.index') }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ request()->routeIs('asistencias.*') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Asistencias
                            </span>
                        </a>
                        @endif

                        {{-- Evaluaciones --}}
                        @if(auth()->user()->hasPermission('evaluaciones.ver'))
                        <a href="{{ route('evaluaciones.index') }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ request()->routeIs('evaluaciones.*') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Evaluaciones
                            </span>
                        </a>
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

                        {{-- Usuarios --}}
                        @if(auth()->user()->hasPermission('usuarios.ver'))
                        <a href="{{ route('usuarios.index') }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ request()->routeIs('usuarios.*') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Usuarios
                            </span>
                        </a>
                        @endif

                        @if(config('app.debug'))
                        <a href="{{ route('developer') }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ request()->routeIs('developer') ? 'bg-white text-utn-blue' : 'text-white hover:bg-white/10' }}">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                </svg>
                                Dev
                            </span>
                        </a>
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
