@extends('layouts.app')

@section('title', 'Nuevo Municipio')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('infraestructura.index', ['tab' => 'municipios']) }}" class="text-utn-blue-dark hover:text-utn-blue-dark flex items-center gap-1 text-sm mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver a municipios
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Nuevo Municipio</h1>
        <p class="text-gray-600 mt-1">Registra una nueva sede presencial</p>
    </div>

    {{-- Formulario --}}
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('municipios.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                {{-- Nombre --}}
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('nombre') border-red-500 @enderror"
                           placeholder="Ej: La Plata">
                    @error('nombre')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dirección --}}
                <div>
                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="direccion" id="direccion" value="{{ old('direccion') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('direccion') border-red-500 @enderror"
                           placeholder="Ej: Calle 7 N° 776">
                    @error('direccion')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Activo --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="activo" id="activo" value="1"
                           class="h-4 w-4 text-utn-blue-dark border-gray-300 rounded focus:ring-utn-blue-dark"
                           {{ old('activo', true) ? 'checked' : '' }}>
                    <label for="activo" class="text-sm font-medium text-gray-700">Municipio activo</label>
                </div>

                {{-- Observaciones --}}
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('observaciones') border-red-500 @enderror"
                              placeholder="Notas adicionales...">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('infraestructura.index', ['tab' => 'municipios']) }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">
                    Crear Municipio
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
