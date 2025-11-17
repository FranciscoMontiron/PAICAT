@extends('layouts.app')

@section('title', 'Inscripciones')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-utn-blue">Módulo de Inscripciones</h1>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">Módulo 1</span>
        </div>

        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Funcionalidades a Desarrollar</h3>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-center">
                    <span class="text-blue-500 mr-2">•</span>
                    Registrar nueva inscripción
                </li>
                <li class="flex items-center">
                    <span class="text-blue-500 mr-2">•</span>
                    Validar documentación
                </li>
                <li class="flex items-center">
                    <span class="text-blue-500 mr-2">•</span>
                    Verificar duplicados
                </li>
                <li class="flex items-center">
                    <span class="text-blue-500 mr-2">•</span>
                    Modificar datos de inscripción
                </li>
                <li class="flex items-center">
                    <span class="text-blue-500 mr-2">•</span>
                    Cancelar inscripción
                </li>
                <li class="flex items-center">
                    <span class="text-blue-500 mr-2">•</span>
                    Buscar aspirante
                </li>
                <li class="flex items-center">
                    <span class="text-blue-500 mr-2">•</span>
                    Importar inscripciones masivas
                </li>
                <li class="flex items-center">
                    <span class="text-blue-500 mr-2">•</span>
                    Exportar listado de inscriptos
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
