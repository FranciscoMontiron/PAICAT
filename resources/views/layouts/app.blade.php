<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PAICAT') }} - @yield('title', 'Sistema de Gestión del Curso de Ingreso')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Asegurar que Tailwind reconozca estas clases */
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="font-inter antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-gradient-to-r from-utn-blue to-blue-900 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                                <div class="bg-white rounded-lg p-2 group-hover:scale-105 transition-transform duration-200">
                                    <svg class="w-6 h-6 text-utn-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <span class="text-white font-bold text-xl tracking-tight">PAICAT</span>
                            </a>
                        </div>

                        <!-- Navigation Links - Desktop -->
                        <div class="hidden md:ml-8 md:flex md:space-x-1">
                            {{-- Dashboard - Todos los usuarios autenticados --}}
                            <a href="{{ route('home') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('home') ? 'text-white bg-white/10 rounded-lg' : 'text-gray-200 hover:text-white hover:bg-white/5 rounded-lg' }} transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Dashboard
                            </a>

                            {{-- Inscripciones - Todos los roles --}}
                            @if(auth()->user()->hasPermission('inscripciones.ver'))
                            <a href="{{ route('inscripciones.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('inscripciones.*') ? 'text-white bg-white/10 rounded-lg' : 'text-gray-200 hover:text-white hover:bg-white/5 rounded-lg' }} transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Inscripciones
                            </a>
                            @endif

                            {{-- Comisiones - Admin, Coordinador, Docente --}}
                            @if(auth()->user()->hasPermission('comisiones.ver'))
                            <a href="{{ route('comisiones.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('comisiones.*') ? 'text-white bg-white/10 rounded-lg' : 'text-gray-200 hover:text-white hover:bg-white/5 rounded-lg' }} transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Comisiones
                            </a>
                            @endif

                            {{-- Asistencias - Todos los roles --}}
                            @if(auth()->user()->hasPermission('asistencias.ver'))
                            <a href="{{ route('asistencias.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('asistencias.*') ? 'text-white bg-white/10 rounded-lg' : 'text-gray-200 hover:text-white hover:bg-white/5 rounded-lg' }} transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Asistencias
                            </a>
                            @endif

                            {{-- Evaluaciones - Todos los roles --}}
                            @if(auth()->user()->hasPermission('evaluaciones.ver'))
                            <a href="{{ route('evaluaciones.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('evaluaciones.*') ? 'text-white bg-white/10 rounded-lg' : 'text-gray-200 hover:text-white hover:bg-white/5 rounded-lg' }} transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Evaluaciones
                            </a>
                            @endif

                            {{-- Reportes - Admin, Coordinador, Docente --}}
                            @if(auth()->user()->hasPermission('reportes.ver'))
                            <a href="{{ route('reportes.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('reportes.*') ? 'text-white bg-white/10 rounded-lg' : 'text-gray-200 hover:text-white hover:bg-white/5 rounded-lg' }} transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Reportes
                            </a>
                            @endif

                            {{-- Usuarios - Solo Admin y Coordinador --}}
                            @if(auth()->user()->hasPermission('usuarios.ver'))
                            <a href="{{ route('usuarios.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('usuarios.*') ? 'text-white bg-white/10 rounded-lg' : 'text-gray-200 hover:text-white hover:bg-white/5 rounded-lg' }} transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Usuarios
                            </a>
                            @endif

                            @if(config('app.debug'))
                            <a href="{{ route('developer') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('developer') ? 'text-white bg-white/10 rounded-lg' : 'text-gray-200 hover:text-white hover:bg-white/5 rounded-lg' }} transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                </svg>
                                Desarrollador
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- User dropdown -->
                    <div class="hidden md:flex md:items-center" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-200 hover:text-white hover:bg-white/10 transition-all duration-200">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ substr(auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->apellido, 0, 1) }}</span>
                                </div>
                                <div class="text-left">
                                    <div class="text-sm font-medium text-white">{{ auth()->user()->nombre_completo }}</div>
                                    <div class="text-xs text-gray-300">{{ auth()->user()->roles()->first()->nombre ?? 'Usuario' }}</div>
                                </div>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 top-16 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5" style="display: none;">
                            <div class="py-1">
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->nombre_completo }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center md:hidden">
                        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-200 hover:text-white hover:bg-white/10 focus:outline-none transition duration-200">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden border-t border-white/10" x-data="{ open: false }">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('home') ? 'text-white bg-white/10' : 'text-gray-200 hover:text-white hover:bg-white/5' }} transition duration-200">Dashboard</a>

                    @if(auth()->user()->hasPermission('inscripciones.ver'))
                    <a href="{{ route('inscripciones.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('inscripciones.*') ? 'text-white bg-white/10' : 'text-gray-200 hover:text-white hover:bg-white/5' }} transition duration-200">Inscripciones</a>
                    @endif

                    @if(auth()->user()->hasPermission('comisiones.ver'))
                    <a href="{{ route('comisiones.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('comisiones.*') ? 'text-white bg-white/10' : 'text-gray-200 hover:text-white hover:bg-white/5' }} transition duration-200">Comisiones</a>
                    @endif

                    @if(auth()->user()->hasPermission('asistencias.ver'))
                    <a href="{{ route('asistencias.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('asistencias.*') ? 'text-white bg-white/10' : 'text-gray-200 hover:text-white hover:bg-white/5' }} transition duration-200">Asistencias</a>
                    @endif

                    @if(auth()->user()->hasPermission('evaluaciones.ver'))
                    <a href="{{ route('evaluaciones.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('evaluaciones.*') ? 'text-white bg-white/10' : 'text-gray-200 hover:text-white hover:bg-white/5' }} transition duration-200">Evaluaciones</a>
                    @endif

                    @if(auth()->user()->hasPermission('reportes.ver'))
                    <a href="{{ route('reportes.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('reportes.*') ? 'text-white bg-white/10' : 'text-gray-200 hover:text-white hover:bg-white/5' }} transition duration-200">Reportes</a>
                    @endif

                    @if(auth()->user()->hasPermission('usuarios.ver'))
                    <a href="{{ route('usuarios.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('usuarios.*') ? 'text-white bg-white/10' : 'text-gray-200 hover:text-white hover:bg-white/5' }} transition duration-200">Usuarios</a>
                    @endif

                    @if(config('app.debug'))
                    <a href="{{ route('developer') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('developer') ? 'text-white bg-white/10' : 'text-gray-200 hover:text-white hover:bg-white/5' }} transition duration-200">Desarrollador</a>
                    @endif
                </div>
            </div>
        </nav>

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

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-sm text-gray-600 mb-2 md:mb-0">
                        &copy; {{ date('Y') }} <span class="font-semibold text-utn-blue">PAICAT</span> - UTN Facultad Regional La Plata
                    </p>
                    <p class="text-xs text-gray-500">
                        Sistema de Gestión del Curso de Ingreso
                    </p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
