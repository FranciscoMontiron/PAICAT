<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PAICAT') }} - @yield('title', 'Sistema de Gestión del Curso de Ingreso')</title>

    <!-- Scripts -->
    <script>
        // Aplicar tamaño de fuente inmediatamente para evitar parpadeos
        (function() {
            try {
                var lvl = localStorage.getItem('paicat_font_size');
                if (lvl === null || isNaN(parseInt(lvl)) || parseInt(lvl) < 0 || parseInt(lvl) > 4) {
                    lvl = '2';
                }
                document.documentElement.classList.add('font-size-' + lvl);
            } catch (e) {}
        })();

        // Accesibilidad: control de tamaño de fuente (solo texto)
        function fontSizer() {
            return {
                level: parseInt(localStorage.getItem('paicat_font_size') || '2'),
                apply() {
                    document.documentElement.className = document.documentElement.className.replace(/font-size-\d/g, '').trim();
                    document.documentElement.classList.add('font-size-' + this.level);
                    localStorage.setItem('paicat_font_size', this.level);
                },
                increase() {
                    if (this.level < 4) {
                        this.level++;
                        this.apply();
                    }
                },
                decrease() {
                    if (this.level > 0) {
                        this.level--;
                        this.apply();
                    }
                },
                reset() {
                    this.level = 2;
                    this.apply();
                }
            };
        }

        // Definir confirmModal ANTES de que Alpine inicialice
        function confirmModal() {
            return {
                show: false,
                title: '',
                message: '',
                type: 'info',
                confirmText: 'Aceptar',
                _resolve: null,

                open(detail) {
                    this.title = detail.title || 'Confirmar acción';
                    this.message = detail.message || '¿Está seguro?';
                    this.type = detail.type || 'info';
                    this.confirmText = detail.confirmText || 'Aceptar';
                    this._resolve = detail.resolve || null;
                    this.show = true;
                },
                accept() {
                    this.show = false;
                    if (this._resolve) this._resolve(true);
                },
                cancel() {
                    this.show = false;
                    if (this._resolve) this._resolve(false);
                }
            };
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Tipografía institucional UTN: Arial */
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        /* Accesibilidad: variable de escala de texto */
        html {
            --ts: 1;
        }

        html.font-size-0 {
            --ts: 0.875;
        }

        html.font-size-1 {
            --ts: 0.935;
        }

        /* font-size-2 = default (--ts: 1), sin override */
        html.font-size-3 {
            --ts: 1.125;
        }

        html.font-size-4 {
            --ts: 1.25;
        }

        /* Escalar clases de texto globalmente */
        html:not(.font-size-2) .text-xs {
            font-size: calc(0.75rem * var(--ts)) !important;
        }

        html:not(.font-size-2) .text-sm {
            font-size: calc(0.875rem * var(--ts)) !important;
        }

        html:not(.font-size-2) .text-base {
            font-size: calc(1rem * var(--ts)) !important;
        }

        html:not(.font-size-2) .text-lg {
            font-size: calc(1.125rem * var(--ts)) !important;
        }

        html:not(.font-size-2) .text-xl {
            font-size: calc(1.25rem * var(--ts)) !important;
        }

        html:not(.font-size-2) .text-2xl {
            font-size: calc(1.5rem * var(--ts)) !important;
        }

        html:not(.font-size-2) .text-3xl {
            font-size: calc(1.875rem * var(--ts)) !important;
        }

        html:not(.font-size-2) .text-4xl {
            font-size: calc(2.25rem * var(--ts)) !important;
        }

        /* Elementos por defecto */
        html:not(.font-size-2) body {
            font-size: calc(1rem * var(--ts));
        }

        /* Inputs y selects */
        html:not(.font-size-2) input,
        html:not(.font-size-2) select,
        html:not(.font-size-2) textarea,
        html:not(.font-size-2) button {
            font-size: calc(0.875rem * var(--ts)) !important;
        }

        /* Evitar que se rompan tablas y listados grandes con fuentes aumentadas */
        html:not(.font-size-2) .overflow-hidden {
            overflow-x: auto !important;
            overflow-y: hidden !important;
        }
    </style>
</head>

<body class="antialiased bg-gray-100" x-data>
    <div class="min-h-screen flex flex-col">
        <!-- Header institucional UTN -->
        <header class="bg-utn-dark">
            <!-- Barra superior con logo -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo UTN -->
                    <a href="{{ route('home') }}" class="flex items-center space-x-4">
                        <img src="{{ asset('images/logo-horizontal-blanco.png') }}" alt="UTN - {{ \App\Services\ConfiguracionService::get('nombre_institucion', 'Facultad Regional La Plata') }}" class="h-12">
                        <div class="hidden sm:block border-l border-white/30 pl-4">
                            <span class="text-white font-bold text-lg tracking-wide">PAICAT</span>
                            <p class="text-white/70 text-xs">Sistema de Gestión del Curso de Ingreso</p>
                        </div>
                    </a>

                    <!-- Accesibilidad + Usuario - Desktop -->
                    <div class="hidden md:flex items-center space-x-3">

                        <!-- Control de fuente -->
                        <div class="flex items-center bg-white/10 rounded-lg px-1 py-1 gap-0.5" x-data="fontSizer()" role="group" aria-label="Tamano de fuente">
                            <button @click="decrease()" :disabled="level <= 0" class="w-7 h-7 flex items-center justify-center rounded text-white/80 hover:bg-white/15 transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Reducir fuente">
                                <span class="text-xs font-bold">A-</span>
                            </button>
                            <button @click="reset()" class="w-7 h-7 flex items-center justify-center rounded text-white/80 hover:bg-white/15 transition-colors" title="Fuente normal">
                                <span class="text-sm font-bold">A</span>
                            </button>
                            <button @click="increase()" :disabled="level >= 4" class="w-7 h-7 flex items-center justify-center rounded text-white/80 hover:bg-white/15 transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Agrandar fuente">
                                <span class="text-base font-bold">A+</span>
                            </button>
                        </div>

                        <!-- Usuario y logout -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-white hover:bg-white/10 transition-all duration-200">
                                <div class="w-9 h-9 rounded-full bg-utn-blue flex items-center justify-center">
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
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 top-full mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black/5 z-50" style="display: none;">
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
                    </div><!-- Fin Accesibilidad + Usuario -->

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
            <nav class="bg-utn-dark-light border-t border-white/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="hidden md:flex space-x-1 py-1">
                        {{-- Dashboard --}}
                        <a href="{{ route('home') }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ request()->routeIs('home') ? 'bg-white text-utn-blue-dark' : 'text-white hover:bg-white/10' }}">
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
                            <button class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('inscripciones.*') ? 'bg-white text-utn-blue-dark' : 'text-white hover:bg-white/10' }}">
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
                                    <a href="{{ route('inscripciones.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('inscripciones.index') || request()->routeIs('inscripciones.show') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Inscripciones
                                    </a>
                                    @if(auth()->user()->hasPermission('inscripciones.editar'))
                                    <a href="{{ route('inscripciones.inactivos') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('inscripciones.inactivos') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Inactivos
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- MENÚ: Académico --}}
                        @if(auth()->user()->hasPermission('comisiones.ver'))
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('comisiones.*') || request()->routeIs('asistencias.*') || request()->routeIs('evaluaciones.*') || request()->routeIs('materias.*') || request()->routeIs('asignacion-alumnos.*') || request()->routeIs('solicitudes.*') ? 'bg-white text-utn-blue-dark' : 'text-white hover:bg-white/10' }}">
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
                                    <a href="{{ route('comisiones.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('comisiones.*') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Comisiones
                                    </a>
                                    @if(auth()->user()->hasPermission('asistencias.ver'))
                                    <a href="{{ route('asistencias.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('asistencias.*') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        Asistencias
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('evaluaciones.ver'))
                                    <a href="{{ route('evaluaciones.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('evaluaciones.*') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Evaluaciones
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('comisiones.editar'))
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ route('materias.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('materias.*') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        Materias
                                    </a>
                                    <a href="{{ route('asignacion-alumnos.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('asignacion-alumnos.*') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Asignación Aleatoria
                                    </a>
                                    <a href="{{ route('solicitudes.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('solicitudes.*') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
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

                        {{-- MENÚ: Infraestructura (solo Admin/Coordinador) --}}
                        @if(auth()->user()->hasPermission('comisiones.crear'))
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('municipios.*') || request()->routeIs('aulas.*') ? 'bg-white text-utn-blue-dark' : 'text-white hover:bg-white/10' }}">
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
                                    <a href="{{ route('infraestructura.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('infraestructura.*') || request()->routeIs('municipios.*') || request()->routeIs('aulas.*') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Municipios y Aulas
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Reportes --}}
                        @if(auth()->user()->hasPermission('reportes.ver'))
                        <a href="{{ route('reportes.index') }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ request()->routeIs('reportes.*') ? 'bg-white text-utn-blue-dark' : 'text-white hover:bg-white/10' }}">
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
                            <button class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('usuarios.*') || request()->routeIs('roles.*') || request()->routeIs('developer') || request()->routeIs('configuracion.*') ? 'bg-white text-utn-blue-dark' : 'text-white hover:bg-white/10' }}">
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
                                    <a href="{{ route('configuracion.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('configuracion.*') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                        </svg>
                                        Configuración
                                        @if(\App\Services\ConfiguracionService::hayRequeridasSinConfigurar())
                                        <span class="ml-auto bg-amber-100 text-amber-700 text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">!</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('usuarios.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('usuarios.*') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        Usuarios
                                    </a>
                                    <a href="{{ route('roles.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('roles.*') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Roles y Permisos
                                    </a>
                                    @endif
                                    @if(config('app.debug'))
                                    <a href="{{ route('developer') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('developer') ? 'bg-blue-50 text-utn-blue-dark font-medium' : '' }}">
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

        <!-- Barra de acento azul UTN -->
        <div class="h-1 bg-utn-blue"></div>

        {{-- Banner de configuración pendiente --}}
        @if(\App\Services\ConfiguracionService::hayRequeridasSinConfigurar() && auth()->user()->hasPermission('usuarios.ver'))
        <div class="bg-amber-50 border-b border-amber-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <span class="text-sm text-amber-800 font-medium">Hay variables de configuración requeridas sin completar. El sistema puede no funcionar correctamente.</span>
                </div>
                <a href="{{ route('configuracion.index') }}" class="text-sm font-semibold text-amber-800 hover:text-amber-900 underline whitespace-nowrap">
                    Ir a Configuración
                </a>
            </div>
        </div>
        @endif

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
        <footer class="bg-utn-dark mt-auto">
            <div class="h-1 bg-utn-blue"></div>
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
                            &copy; {{ date('Y') }} UTN - {{ \App\Services\ConfiguracionService::get('nombre_institucion', 'Facultad Regional La Plata') }}
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

    {{-- Modal global de confirmación --}}
    <div x-data="confirmModal()"
        @confirm-modal.window="open($event.detail)"
        @keydown.escape.window="show && cancel()"
        id="confirm-modal-root"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[9999] overflow-y-auto"
        aria-modal="true"
        role="dialog">

        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            {{-- Overlay --}}
            <div x-show="show"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="cancel()"></div>

            {{-- Panel --}}
            <div x-show="show"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto z-10 overflow-hidden">

                {{-- Barra superior de color --}}
                <div class="h-1.5" :class="type === 'danger' ? 'bg-red-500' : type === 'warning' ? 'bg-amber-500' : 'bg-utn-blue'"></div>

                <div class="p-6">
                    {{-- Icono centrado --}}
                    <div class="flex justify-center mb-5">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center ring-8"
                            :class="type === 'danger' ? 'bg-red-100 ring-red-50' : type === 'warning' ? 'bg-amber-100 ring-amber-50' : 'bg-blue-100 ring-blue-50'">
                            <svg class="w-7 h-7" :class="type === 'danger' ? 'text-red-600' : type === 'warning' ? 'text-amber-600' : 'text-utn-blue-dark'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    :d="type === 'danger'
                                          ? 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
                                          : type === 'warning'
                                          ? 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z'
                                          : 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'" />
                            </svg>
                        </div>
                    </div>

                    {{-- Contenido centrado --}}
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-gray-900" x-text="title"></h3>
                        <p class="mt-2 text-sm text-gray-500 leading-relaxed" x-text="message"></p>
                    </div>

                    {{-- Botones --}}
                    <div class="flex gap-3 mt-7">
                        <button @click="cancel()" type="button"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 active:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancelar
                        </button>
                        <button @click="accept()" type="button"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="type === 'danger' ? 'bg-red-600 hover:bg-red-700 active:bg-red-800 focus:ring-red-500' : type === 'warning' ? 'bg-amber-600 hover:bg-amber-700 active:bg-amber-800 focus:ring-amber-500' : 'bg-utn-blue hover:bg-utn-dark active:bg-utn-dark focus:ring-utn-blue'"
                            x-text="confirmText">
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        /**
         * Global confirm function — returns a Promise<boolean>
         */
        window.paiConfirm = function(opts) {
            if (typeof opts === 'string') {
                opts = {
                    message: opts
                };
            }
            return new Promise(resolve => {
                window.dispatchEvent(new CustomEvent('confirm-modal', {
                    detail: {
                        ...opts,
                        resolve
                    }
                }));
            });
        };

        /**
         * Auto-bind: any element with data-confirm="..." will show the modal on click/submit.
         * For forms: <form data-confirm="¿Está seguro?">
         * For buttons/links: <button data-confirm="¿Eliminar?">
         * Supports data-confirm-title, data-confirm-type="danger", data-confirm-text="Eliminar"
         */
        document.addEventListener('submit', function(e) {
            const el = e.target.closest('[data-confirm]');
            if (!el || el.dataset.confirmBypassed) {
                return;
            }
            e.preventDefault();
            paiConfirm({
                message: el.dataset.confirm,
                title: el.dataset.confirmTitle || 'Confirmar acción',
                type: el.dataset.confirmType || 'info',
                confirmText: el.dataset.confirmText || 'Aceptar'
            }).then(ok => {
                if (ok) {
                    el.dataset.confirmBypassed = 'true';
                    el.requestSubmit ? el.requestSubmit() : el.submit();
                    delete el.dataset.confirmBypassed;
                }
            });
        }, true);

        document.addEventListener('click', function(e) {
            const el = e.target.closest('a[data-confirm], button[data-confirm]:not([type="submit"])');
            if (!el || el.closest('form')) return;
            e.preventDefault();
            paiConfirm({
                message: el.dataset.confirm,
                title: el.dataset.confirmTitle || 'Confirmar acción',
                type: el.dataset.confirmType || 'info',
                confirmText: el.dataset.confirmText || 'Aceptar'
            }).then(ok => {
                if (ok && el.tagName === 'A' && el.href) {
                    window.location.href = el.href;
                }
            });
        }, true);
    </script>
</body>

</html>