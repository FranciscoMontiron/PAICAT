@extends('layouts.app')

@section('title', 'Editar Municipio')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('municipios.index') }}" class="text-utn-blue-dark hover:text-utn-blue-dark flex items-center gap-1 text-sm mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver a municipios
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Editar Municipio</h1>
        <p class="text-gray-600 mt-1">{{ $municipio->nombre }}</p>
    </div>

    {{-- Formulario --}}
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('municipios.update', $municipio) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Nombre --}}
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $municipio->nombre) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('nombre') border-red-500 @enderror"
                           placeholder="Ej: La Plata">
                    @error('nombre')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Código --}}
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                    <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $municipio->codigo) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('codigo') border-red-500 @enderror"
                           placeholder="Ej: LP">
                    @error('codigo')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Código corto para identificar el municipio</p>
                </div>

                {{-- Dirección --}}
                <div>
                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="direccion" id="direccion" value="{{ old('direccion', $municipio->direccion) }}"
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
                           {{ old('activo', $municipio->activo) ? 'checked' : '' }}>
                    <label for="activo" class="text-sm font-medium text-gray-700">Municipio activo</label>
                </div>

                {{-- Observaciones --}}
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('observaciones') border-red-500 @enderror"
                              placeholder="Notas adicionales...">{{ old('observaciones', $municipio->observaciones) }}</textarea>
                    @error('observaciones')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('municipios.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
