@extends('layouts.app')
@section('title', 'Seleccionar Materia')
@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Seleccionar Materia</h1>
                <p class="text-gray-600 mt-2">{{ $comision->codigo }} - {{ $comision->nombre }}</p>
            </div>
            <a href="{{ route('asistencias.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </div>

    {{-- Información de la comisión --}}
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Información de la Comisión</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-1">Docente</p>
                    <p class="text-gray-900">{{ $comision->docente->name ?? 'Sin asignar' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-1">Período</p>
                    <p class="text-gray-900">{{ $comision->anio }} - {{ $comision->periodo }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-1">Turno</p>
                    <p class="text-gray-900">{{ $comision->turno }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Listado de Materias --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-indigo-200">
            <h2 class="text-lg font-semibold text-gray-800">Selecciona la materia para pasar asistencia</h2>
            <p class="text-sm text-gray-600 mt-1">Esta comisión tiene {{ $comision->materias->count() }} materia(s) asignada(s)</p>
        </div>
        <div class="p-6">
            @if($comision->materias->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($comision->materias as $materia)
                <a href="{{ route('asistencias.create', ['comision' => $comision, 'materia_id' => $materia->id]) }}"
                    class="block p-5 border-2 border-gray-200 rounded-xl hover:border-indigo-500 hover:shadow-lg transition-all duration-200 group">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-indigo-100 text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600">{{ $materia->nombre }}</h3>
                            <p class="text-sm text-gray-500 font-mono">{{ $materia->codigo }}</p>
                            @if($materia->carga_horaria)
                            <p class="text-xs text-gray-400 mt-1">{{ $materia->carga_horaria }} hs</p>
                            @endif
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Sin materias asignadas</h3>
                <p class="mt-2 text-sm text-gray-500">Esta comisión no tiene materias asignadas. Por favor, edite la comisión para agregar materias.</p>
                <a href="{{ route('comisiones.edit', $comision) }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                    Editar comisión
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection