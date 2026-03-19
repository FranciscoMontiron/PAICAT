@extends('layouts.app')
@section('title', 'Editar Materia')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('materias.index') }}" class="text-utn-blue-dark hover:text-utn-blue-dark flex items-center gap-1 text-sm mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver a materias
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Editar Materia</h1>
        <p class="text-gray-600 mt-1">Modifica los datos de {{ $materia->nombre }}</p>
    </div>

    {{-- Formulario --}}
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('materias.update', $materia) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Nombre --}}
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $materia->nombre) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('nombre') border-red-500 @enderror"
                           placeholder="Ej: Matematica, Fisica">
                    @error('nombre')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Tipo --}}
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo <span class="text-red-500">*</span>
                        </label>
                        <select name="tipo" id="tipo" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('tipo') border-red-500 @enderror">
                            @foreach(\App\Models\Materia::getTipos() as $key => $label)
                                <option value="{{ $key }}" {{ old('tipo', $materia->tipo) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Carga Horaria --}}
                    <div>
                        <label for="carga_horaria" class="block text-sm font-medium text-gray-700 mb-1">Carga Horaria</label>
                        <div class="relative">
                            <input type="number" name="carga_horaria" id="carga_horaria" value="{{ old('carga_horaria', $materia->carga_horaria) }}" min="1" max="500"
                                   class="w-full px-3 py-2 pr-16 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('carga_horaria') border-red-500 @enderror">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">horas</span>
                        </div>
                        @error('carga_horaria')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Checkboxes --}}
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="es_nivelacion" id="es_nivelacion" value="1"
                               class="h-4 w-4 text-utn-blue-dark border-gray-300 rounded focus:ring-utn-blue-dark"
                               {{ old('es_nivelacion', $materia->es_nivelacion) ? 'checked' : '' }}>
                        <label for="es_nivelacion" class="text-sm font-medium text-gray-700">Es de nivelacion</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="activa" id="activa" value="1"
                               class="h-4 w-4 text-utn-blue-dark border-gray-300 rounded focus:ring-utn-blue-dark"
                               {{ old('activa', $materia->activa) ? 'checked' : '' }}>
                        <label for="activa" class="text-sm font-medium text-gray-700">Materia activa</label>
                    </div>
                </div>

                {{-- Descripcion --}}
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                    <textarea name="descripcion" id="descripcion" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('descripcion') border-red-500 @enderror"
                              placeholder="Contenidos principales, objetivos...">{{ old('descripcion', $materia->descripcion) }}</textarea>
                    @error('descripcion')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('materias.index') }}"
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
