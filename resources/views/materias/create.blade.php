@extends('layouts.app')
@section('title', 'Nueva Materia')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('materias.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Nueva Materia</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('materias.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código <span class="text-red-500">*</span></label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" required
                            class="w-full rounded-lg border-gray-300 @error('codigo') border-red-500 @enderror" placeholder="MAT001">
                        @error('codigo')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                        <select name="tipo" required class="w-full rounded-lg border-gray-300">
                            <option value="obligatoria" {{ old('tipo') == 'obligatoria' ? 'selected' : '' }}>Obligatoria</option>
                            <option value="optativa" {{ old('tipo') == 'optativa' ? 'selected' : '' }}>Optativa</option>
                            <option value="nivelacion" {{ old('tipo', 'nivelacion') == 'nivelacion' ? 'selected' : '' }}>Nivelación</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                        class="w-full rounded-lg border-gray-300 @error('nombre') border-red-500 @enderror" placeholder="Matemática Inicial">
                    @error('nombre')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3" class="w-full rounded-lg border-gray-300" placeholder="Descripción de la materia...">{{ old('descripcion') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carga Horaria</label>
                        <input type="number" name="carga_horaria" value="{{ old('carga_horaria') }}" min="1"
                            class="w-full rounded-lg border-gray-300" placeholder="40">
                    </div>
                    <div class="flex items-end gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="es_nivelacion" value="1" {{ old('es_nivelacion', true) ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="text-sm text-gray-700">Es de nivelación</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="activa" value="1" {{ old('activa', true) ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="text-sm text-gray-700">Activa</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                <a href="{{ route('materias.index') }}" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Guardar Materia</button>
            </div>
        </form>
    </div>
</div>
@endsection