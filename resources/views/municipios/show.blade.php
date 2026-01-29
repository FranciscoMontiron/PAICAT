@extends('layouts.app')

@section('title', $municipio->nombre)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <a href="{{ route('municipios.index') }}" class="text-utn-blue hover:text-blue-800 flex items-center gap-1 text-sm mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver a municipios
            </a>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                {{ $municipio->nombre }}
                <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full {{ $municipio->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $municipio->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </h1>
            @if($municipio->codigo)
            <p class="text-gray-600 mt-1">Código: {{ $municipio->codigo }}</p>
            @endif
        </div>
        @if(auth()->user()->hasPermission('municipios.editar'))
        <a href="{{ route('municipios.edit', $municipio) }}"
           class="bg-utn-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Editar
        </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Información del municipio --}}
        <div class="lg:col-span-1">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Información</h2>
                <dl class="space-y-4">
                    @if($municipio->direccion)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $municipio->direccion }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Comisiones</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $municipio->comisiones->count() }} total</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Comisiones Activas</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $municipio->comisiones->where('estado', 'activa')->count() }}</dd>
                    </div>
                    @if($municipio->observaciones)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Observaciones</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $municipio->observaciones }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Comisiones del municipio --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Comisiones en este municipio</h2>
                </div>
                @if($municipio->comisiones->count() > 0)
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comisión</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Año</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Turno</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($municipio->comisiones as $comision)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('comisiones.show', $comision) }}" class="text-utn-blue hover:text-blue-800">
                                    <div class="text-sm font-medium">{{ $comision->nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ $comision->codigo }}</div>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $comision->anio }}</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $comision->turno ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $comision->estado === 'activa' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($comision->estado) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="px-6 py-10 text-center text-gray-500">
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <p class="mt-2">No hay comisiones en este municipio</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
