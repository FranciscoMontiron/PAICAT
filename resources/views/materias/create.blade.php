@extends('layouts.app')
@section('title', 'Nueva Materia')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('materias.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nueva Materia</h1>
                <p class="text-sm text-gray-500 mt-0.5">Registra una nueva materia del curso de ingreso</p>
            </div>
        </div>
    </div>

    <form action="{{ route('materias.store') }}" method="POST">
        @csrf

        {{-- Sección: Información General --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-emerald-100 border-b border-emerald-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Información General</h2>
                        <p class="text-sm text-gray-600">Datos de identificación de la materia</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Código --}}
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="codigo" id="codigo" value="{{ old('codigo') }}" required
                            placeholder="Ej: MAT001"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-shadow font-mono uppercase @error('codigo') border-red-500 ring-2 ring-red-200 @enderror">
                        @error('codigo')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Tipo <span class="text-red-500">*</span>
                        </label>
                        <select name="tipo" id="tipo" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-shadow">
                            <option value="">Seleccionar...</option>
                            @foreach(\App\Models\Materia::getTipos() as $key => $label)
                                <option value="{{ $key }}" {{ old('tipo', 'nivelacion') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombre --}}
                    <div class="md:col-span-2">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                            placeholder="Ej: Matemática, Física, Introducción a la Vida Universitaria"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-shadow @error('nombre') border-red-500 ring-2 ring-red-200 @enderror">
                        @error('nombre')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección: Configuración --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-utn-blue/20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-utn-blue-darker rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Configuración</h2>
                        <p class="text-sm text-gray-600">Carga horaria y estado de la materia</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Carga Horaria --}}
                    <div>
                        <label for="carga_horaria" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Carga Horaria
                        </label>
                        <div class="relative">
                            <input type="number" name="carga_horaria" id="carga_horaria" value="{{ old('carga_horaria', 40) }}" min="1" max="500"
                                class="w-full px-4 py-2.5 pr-16 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">horas</span>
                        </div>
                        @error('carga_horaria')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Checkboxes --}}
                    <div class="md:col-span-2 flex items-end">
                        <div class="flex flex-wrap gap-6">
                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300 cursor-pointer transition-colors">
                                <input type="checkbox" name="es_nivelacion" value="1" {{ old('es_nivelacion', true) ? 'checked' : '' }}
                                    class="w-5 h-5 rounded border-gray-300 text-utn-blue-dark focus:ring-indigo-500">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">Es de nivelación</span>
                                    <p class="text-xs text-gray-500">Materia del curso de ingreso</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-emerald-300 cursor-pointer transition-colors">
                                <input type="checkbox" name="activa" value="1" {{ old('activa', true) ? 'checked' : '' }}
                                    class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">Activa</span>
                                    <p class="text-xs text-gray-500">Disponible para asignar</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección: Descripción (colapsable) --}}
        <details class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8 group">
            <summary class="px-6 py-4 bg-gray-50 border-b border-gray-200 cursor-pointer hover:bg-gray-100 transition-colors list-none">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-gray-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Descripción</h2>
                            <p class="text-sm text-gray-600">Información adicional (opcional)</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </summary>
            <div class="p-6">
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Descripción de la materia
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="4"
                        placeholder="Contenidos principales, objetivos, metodología..."
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-shadow resize-none">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </details>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('materias.index') }}"
                class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-200 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Guardar Materia
            </button>
        </div>
    </form>
</div>
@endsection
