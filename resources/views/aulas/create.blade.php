@extends('layouts.app')

@section('title', 'Nueva Aula')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('aulas.index') }}" class="text-utn-blue hover:text-blue-800 flex items-center gap-1 text-sm mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver a aulas
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Nueva Aula</h1>
        <p class="text-gray-600 mt-1">Registra un nuevo espacio físico para clases</p>
    </div>

    {{-- Formulario --}}
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('aulas.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                {{-- Municipio --}}
                <div>
                    <label for="municipio_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Municipio/Sede <span class="text-red-500">*</span>
                    </label>
                    <select name="municipio_id" id="municipio_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('municipio_id') border-red-500 @enderror">
                        <option value="">Seleccionar municipio...</option>
                        @foreach($municipios as $municipio)
                        <option value="{{ $municipio->id }}" {{ old('municipio_id') == $municipio->id ? 'selected' : '' }}>
                            {{ $municipio->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('municipio_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">El aula pertenece a este municipio/sede</p>
                </div>

                {{-- Nombre --}}
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('nombre') border-red-500 @enderror"
                           placeholder="Ej: Aula 101, Laboratorio de Informática">
                    @error('nombre')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Código --}}
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                        <input type="text" name="codigo" id="codigo" value="{{ old('codigo') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('codigo') border-red-500 @enderror"
                               placeholder="Ej: A101">
                        @error('codigo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Código corto para identificar el aula</p>
                    </div>

                    {{-- Capacidad --}}
                    <div>
                        <label for="capacidad" class="block text-sm font-medium text-gray-700 mb-1">Capacidad</label>
                        <div class="relative">
                            <input type="number" name="capacidad" id="capacidad" value="{{ old('capacidad') }}" min="1" max="500"
                                   class="w-full px-3 py-2 pr-16 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('capacidad') border-red-500 @enderror"
                                   placeholder="Ej: 40">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">personas</span>
                        </div>
                        @error('capacidad')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Ubicación --}}
                <div>
                    <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                    <input type="text" name="ubicacion" id="ubicacion" value="{{ old('ubicacion') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('ubicacion') border-red-500 @enderror"
                           placeholder="Ej: Planta Baja, Edificio Central">
                    @error('ubicacion')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Descripción de la ubicación dentro del edificio</p>
                </div>

                {{-- Activa --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="activa" id="activa" value="1"
                           class="h-4 w-4 text-utn-blue border-gray-300 rounded focus:ring-utn-blue"
                           {{ old('activa', true) ? 'checked' : '' }}>
                    <label for="activa" class="text-sm font-medium text-gray-700">Aula activa</label>
                </div>

                {{-- Observaciones --}}
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('observaciones') border-red-500 @enderror"
                              placeholder="Notas adicionales...">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('aulas.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                    Crear Aula
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
