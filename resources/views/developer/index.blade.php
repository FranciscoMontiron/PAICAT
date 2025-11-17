@extends('layouts.app')
@section('title', 'Desarrollador')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Herramientas de Desarrollador</h1>
        <p class="text-gray-600 mt-1">Referencia de colores, componentes y convenciones del proyecto</p>
    </div>

    <!-- Paleta de Colores -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 mb-8 border border-gray-300">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-utn-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
            </svg>
            Paleta de Colores del Proyecto
        </h2>

        <!-- Colores Institucionales -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Colores Institucionales UTN</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-utn-blue w-16 h-16 rounded-lg shadow-md border-2 border-gray-300"></div>
                        <div class="ml-4">
                            <p class="font-mono text-sm font-bold text-gray-800">#003366</p>
                            <p class="text-sm text-gray-600">UTN Blue</p>
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded mt-1 inline-block">bg-utn-blue / text-utn-blue</code>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-utn-orange w-16 h-16 rounded-lg shadow-md border-2 border-gray-300"></div>
                        <div class="ml-4">
                            <p class="font-mono text-sm font-bold text-gray-800">#FF6600</p>
                            <p class="text-sm text-gray-600">UTN Orange</p>
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded mt-1 inline-block">bg-utn-orange / text-utn-orange</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colores por Módulo -->
        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Colores por Módulo</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <!-- Inscripciones - Blue -->
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                    <div class="flex items-center mb-2">
                        <div class="bg-blue-500 w-10 h-10 rounded shadow"></div>
                        <p class="ml-2 font-semibold text-sm text-gray-700">Inscripciones</p>
                    </div>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block">bg-blue-500 / text-blue-600</code>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block mt-1">bg-blue-50 (fondos)</code>
                </div>

                <!-- Comisiones - Green -->
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                    <div class="flex items-center mb-2">
                        <div class="bg-green-500 w-10 h-10 rounded shadow"></div>
                        <p class="ml-2 font-semibold text-sm text-gray-700">Comisiones</p>
                    </div>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block">bg-green-500 / text-green-600</code>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block mt-1">bg-green-50 (fondos)</code>
                </div>

                <!-- Asistencias - Yellow -->
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                    <div class="flex items-center mb-2">
                        <div class="bg-yellow-500 w-10 h-10 rounded shadow"></div>
                        <p class="ml-2 font-semibold text-sm text-gray-700">Asistencias</p>
                    </div>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block">bg-yellow-500 / text-yellow-600</code>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block mt-1">bg-yellow-50 (fondos)</code>
                </div>

                <!-- Evaluaciones - Red -->
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                    <div class="flex items-center mb-2">
                        <div class="bg-red-500 w-10 h-10 rounded shadow"></div>
                        <p class="ml-2 font-semibold text-sm text-gray-700">Evaluaciones</p>
                    </div>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block">bg-red-500 / text-red-600</code>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block mt-1">bg-red-50 (fondos)</code>
                </div>

                <!-- Reportes - Purple -->
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                    <div class="flex items-center mb-2">
                        <div class="bg-purple-500 w-10 h-10 rounded shadow"></div>
                        <p class="ml-2 font-semibold text-sm text-gray-700">Reportes</p>
                    </div>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block">bg-purple-500 / text-purple-600</code>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block mt-1">bg-purple-50 (fondos)</code>
                </div>

                <!-- Usuarios - Indigo -->
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                    <div class="flex items-center mb-2">
                        <div class="bg-indigo-500 w-10 h-10 rounded shadow"></div>
                        <p class="ml-2 font-semibold text-sm text-gray-700">Usuarios</p>
                    </div>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block">bg-indigo-500 / text-indigo-600</code>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block mt-1">bg-indigo-50 (fondos)</code>
                </div>
            </div>
        </div>

        <!-- Colores Neutrales -->
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Colores Neutrales</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-gray-200 w-16 h-16 rounded-lg shadow-md border-2 border-gray-300"></div>
                        <div class="ml-4">
                            <p class="font-semibold text-sm text-gray-700">Fondos claros</p>
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded mt-1 inline-block">gray-100 / gray-200</code>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-gray-600 w-16 h-16 rounded-lg shadow-md border-2 border-gray-300"></div>
                        <div class="ml-4">
                            <p class="font-semibold text-sm text-gray-700">Textos</p>
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded mt-1 inline-block">gray-600 / gray-700</code>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-gray-900 w-16 h-16 rounded-lg shadow-md border-2 border-gray-300"></div>
                        <div class="ml-4">
                            <p class="font-semibold text-sm text-gray-700">Títulos</p>
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded mt-1 inline-block">gray-800 / gray-900</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Nota:</strong> Esta página solo está disponible en modo desarrollo (APP_DEBUG=true).
                    Consulta <a href="#" class="underline font-semibold">ESTRUCTURA_MODULOS.md</a> para más información sobre convenciones y arquitectura.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
