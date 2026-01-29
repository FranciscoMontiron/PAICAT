@extends('layouts.app')

@section('title', 'Detalle de Aula')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <a href="{{ route('aulas.index') }}" class="text-utn-blue hover:text-blue-800 flex items-center gap-1 text-sm mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver a aulas
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $aula->nombre }}</h1>
            <p class="text-gray-600 mt-1">
                @if($aula->codigo)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-2">
                    {{ $aula->codigo }}
                </span>
                @endif
                {{ $aula->municipio->nombre ?? 'Sin municipio' }}
            </p>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->hasPermission('comisiones.editar'))
            <a href="{{ route('aulas.edit', $aula) }}"
               class="bg-utn-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-200 flex items-center gap-2">
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
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Información del Aula</h2>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-gray-500">Estado</dt>
                        <dd class="mt-1">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $aula->activa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $aula->activa ? 'Activa' : 'Inactiva' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Municipio/Sede</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            {{ $aula->municipio->nombre ?? 'Sin asignar' }}
                        </dd>
                    </div>

                    @if($aula->codigo)
                    <div>
                        <dt class="text-sm text-gray-500">Código</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $aula->codigo }}</dd>
                    </div>
                    @endif

                    @if($aula->capacidad)
                    <div>
                        <dt class="text-sm text-gray-500">Capacidad</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $aula->capacidad }} personas</dd>
                    </div>
                    @endif

                    @if($aula->ubicacion)
                    <div>
                        <dt class="text-sm text-gray-500">Ubicación</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $aula->ubicacion }}</dd>
                    </div>
                    @endif

                    @if($aula->observaciones)
                    <div>
                        <dt class="text-sm text-gray-500">Observaciones</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $aula->observaciones }}</dd>
                    </div>
                    @endif

                    <div class="pt-4 border-t border-gray-200">
                        <dt class="text-sm text-gray-500">Creado</dt>
                        <dd class="mt-1 text-xs text-gray-600">{{ $aula->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Última modificación</dt>
                        <dd class="mt-1 text-xs text-gray-600">{{ $aula->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>

                @if(auth()->user()->hasPermission('comisiones.editar'))
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <form action="{{ route('aulas.toggle-activa', $aula) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $aula->activa ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                            {{ $aula->activa ? 'Desactivar Aula' : 'Activar Aula' }}
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        {{-- Comisiones en esta Aula --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Comisiones en esta Aula</h2>
                    <p class="text-sm text-gray-500">Listado de comisiones que utilizan este espacio</p>
                </div>

                @if($aula->comisiones->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($aula->comisiones as $comision)
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
                                        {{ ucfirst($comision->turno ?? 'Sin turno') }}
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
                                @if($comision->docentes->count() > 0)
                                <div class="text-xs text-gray-500">
                                    {{ $comision->docentes->count() }} docente(s)
                                </div>
                                @endif
                                <a href="{{ route('comisiones.show', $comision) }}"
                                   class="p-1.5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors"
                                   title="Ver comisión">
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
                    <p class="mt-2">No hay comisiones asignadas a esta aula</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
