@extends('layouts.app')

@section('title', $materia->nombre)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <a href="{{ route('materias.index') }}" class="text-utn-blue-dark hover:text-utn-blue-dark flex items-center gap-1 text-sm mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver a materias
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $materia->nombre }}</h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono bg-gray-100 text-gray-800">
                    {{ $materia->codigo }}
                </span>
                @php
                    $tipoColors = [
                        'obligatoria' => 'bg-utn-blue/10 text-utn-blue-dark',
                        'nivelacion' => 'bg-purple-100 text-purple-700',
                        'optativa' => 'bg-teal-100 text-teal-700',
                    ];
                    $tColor = $tipoColors[$materia->tipo] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $tColor }}">
                    {{ \App\Models\Materia::getTipos()[$materia->tipo] ?? ucfirst($materia->tipo) }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->hasPermission('comisiones.editar'))
            <a href="{{ route('materias.edit', $materia) }}"
               class="bg-utn-blue text-white px-4 py-2 rounded-lg hover:bg-utn-dark transition-colors duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            @endif
        </div>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info Principal --}}
        <div class="lg:col-span-1">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informacion de la Materia</h2>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-gray-500">Estado</dt>
                        <dd class="mt-1">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $materia->activa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $materia->activa ? 'Activa' : 'Inactiva' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Tipo</dt>
                        <dd class="mt-1">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $tColor }}">
                                {{ \App\Models\Materia::getTipos()[$materia->tipo] ?? ucfirst($materia->tipo) }}
                            </span>
                        </dd>
                    </div>

                    @if($materia->carga_horaria)
                    <div>
                        <dt class="text-sm text-gray-500">Carga Horaria</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $materia->carga_horaria }} horas</dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-sm text-gray-500">Nivelacion</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $materia->es_nivelacion ? 'Si' : 'No' }}</dd>
                    </div>

                    @if($materia->descripcion)
                    <div>
                        <dt class="text-sm text-gray-500">Descripcion</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $materia->descripcion }}</dd>
                    </div>
                    @endif

                    <div class="pt-4 border-t border-gray-200">
                        <dt class="text-sm text-gray-500">Creado</dt>
                        <dd class="mt-1 text-xs text-gray-600">{{ $materia->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Ultima modificacion</dt>
                        <dd class="mt-1 text-xs text-gray-600">{{ $materia->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Comisiones con esta Materia --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Comisiones con esta Materia</h2>
                    <p class="text-sm text-gray-500">Listado de comisiones que tienen asignada esta materia</p>
                </div>

                @if($materia->comisiones->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($materia->comisiones as $comision)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full {{ $comision->estado === 'activa' ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                                    <svg class="w-5 h-5 {{ $comision->estado === 'activa' ? 'text-green-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $comision->nombre }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $comision->codigo }}
                                        @if($comision->turno)
                                        - {{ ucfirst($comision->turno) }}
                                        @endif
                                        @if($comision->modalidad)
                                        - {{ ucfirst($comision->modalidad) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $comision->estado === 'activa' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($comision->estado) }}
                                </span>
                                <a href="{{ route('comisiones.show', $comision) }}"
                                   class="p-1.5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors"
                                   title="Ver comision">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="px-6 py-10 text-center text-gray-500">
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="mt-2">No hay comisiones con esta materia asignada</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
