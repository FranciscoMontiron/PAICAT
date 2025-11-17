@extends('layouts.app')

@section('title', 'Comisiones')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-utn-blue">Módulo de Comisiones</h1>
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Módulo 2</span>
        </div>

        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Funcionalidades a Desarrollar</h3>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-center"><span class="text-green-500 mr-2">•</span>Crear comisión</li>
                <li class="flex items-center"><span class="text-green-500 mr-2">•</span>Asignar docente a comisión</li>
                <li class="flex items-center"><span class="text-green-500 mr-2">•</span>Asignar aspirantes a comisión (automático)</li>
                <li class="flex items-center"><span class="text-green-500 mr-2">•</span>Reasignar aspirante a otra comisión</li>
                <li class="flex items-center"><span class="text-green-500 mr-2">•</span>Cerrar inscripción a comisión</li>
                <li class="flex items-center"><span class="text-green-500 mr-2">•</span>Modificar datos de comisión</li>
                <li class="flex items-center"><span class="text-green-500 mr-2">•</span>Ver distribución de comisiones</li>
            </ul>
        </div>
    </div>
</div>
@endsection
