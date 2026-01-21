@extends('layouts.app')
@section('title', 'Crear Comisión')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('comisiones.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nueva Comisión</h1>
                <p class="text-sm text-gray-500 mt-0.5">Completa los datos para crear una nueva comisión</p>
            </div>
        </div>
    </div>

    <form action="{{ route('comisiones.store') }}" method="POST">
        @csrf

        {{-- Sección: Información General --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Información General</h2>
                        <p class="text-sm text-gray-600">Datos básicos de identificación</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Materia --}}
                    <div class="md:col-span-2">
                        <label for="materia_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Materia <span class="text-red-500">*</span>
                        </label>
                        <select name="materia_id" id="materia_id" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('materia_id') border-red-500 @enderror">
                            <option value="">Seleccionar materia...</option>
                            @foreach($materias as $materia)
                            <option value="{{ $materia->id }}" {{ old('materia_id') == $materia->id ? 'selected' : '' }}>
                                [{{ $materia->codigo }}] {{ $materia->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @error('materia_id')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Nombre --}}
                    <div class="md:col-span-2">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nombre de la Comisión <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                            placeholder="Ej: Comisión Verano 2027 - Turno Mañana"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('nombre') border-red-500 ring-2 ring-red-200 @enderror">
                        @error('nombre')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Código --}}
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="codigo" id="codigo" value="{{ old('codigo') }}" required
                            placeholder="Ej: COM-2027-V-M-01"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow font-mono @error('codigo') border-red-500 @enderror">
                        @error('codigo')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Año --}}
                    <div>
                        <label for="anio" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Año <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="anio" id="anio" value="{{ old('anio', date('Y')) }}" required
                            min="2020" max="2100" placeholder="Ej: 2027"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('anio') border-red-500 @enderror">
                        @error('anio')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección: Configuración Académica --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-emerald-100 border-b border-emerald-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Configuración Académica</h2>
                        <p class="text-sm text-gray-600">Período, turno y modalidad de cursada</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    {{-- Periodo --}}
                    <div>
                        <label for="periodo" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Periodo <span class="text-red-500">*</span>
                        </label>
                        <select name="periodo" id="periodo" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-shadow">
                            <option value="">Seleccionar...</option>
                            <option value="Verano" {{ old('periodo') == 'Verano' ? 'selected' : '' }}>Verano</option>
                            <option value="Invierno" {{ old('periodo') == 'Invierno' ? 'selected' : '' }}>Invierno</option>
                            <option value="Anual" {{ old('periodo') == 'Anual' ? 'selected' : '' }}>Anual</option>
                        </select>
                        @error('periodo')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Turno --}}
                    <div>
                        <label for="turno" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Turno <span class="text-red-500">*</span>
                        </label>
                        <select name="turno" id="turno" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-shadow">
                            <option value="">Seleccionar...</option>
                            <option value="Mañana" {{ old('turno') == 'Mañana' ? 'selected' : '' }}>Mañana</option>
                            <option value="Tarde" {{ old('turno') == 'Tarde' ? 'selected' : '' }}>Tarde</option>
                            <option value="Noche" {{ old('turno') == 'Noche' ? 'selected' : '' }}>Noche</option>
                        </select>
                        @error('turno')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Modalidad --}}
                    <div>
                        <label for="modalidad" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Modalidad <span class="text-red-500">*</span>
                        </label>
                        <select name="modalidad" id="modalidad" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-shadow">
                            <option value="">Seleccionar...</option>
                            <option value="Presencial" {{ old('modalidad') == 'Presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="Virtual" {{ old('modalidad') == 'Virtual' ? 'selected' : '' }}>Virtual</option>
                            <option value="Semipresencial" {{ old('modalidad') == 'Semipresencial' ? 'selected' : '' }}>Semipresencial</option>
                        </select>
                        @error('modalidad')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cupo Máximo --}}
                    <div>
                        <label for="cupo_maximo" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Cupo Máximo <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="cupo_maximo" id="cupo_maximo" value="{{ old('cupo_maximo', 80) }}" required min="1" max="200"
                                class="w-full px-4 py-2.5 pr-16 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-shadow">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">alumnos</span>
                        </div>
                        @error('cupo_maximo')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección: Docente y Fechas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-amber-100 border-b border-amber-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Docente y Fechas</h2>
                        <p class="text-sm text-gray-600">Asignación de docente y período de cursada</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Docente --}}
                    <div>
                        <label for="docente_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Docente Asignado
                        </label>
                        <select name="docente_id" id="docente_id"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-shadow">
                            <option value="">Sin asignar</option>
                            @foreach($docentes as $docente)
                            <option value="{{ $docente->id }}" {{ old('docente_id') == $docente->id ? 'selected' : '' }}>
                                {{ $docente->nombre_completo }}
                            </option>
                            @endforeach
                        </select>
                        @error('docente_id')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha Inicio --}}
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Fecha de Inicio
                        </label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-shadow">
                        @error('fecha_inicio')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha Fin --}}
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Fecha de Fin
                        </label>
                        <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-shadow">
                        @error('fecha_fin')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección: Información Adicional (colapsable) --}}
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
                            <h2 class="text-lg font-semibold text-gray-900">Información Adicional</h2>
                            <p class="text-sm text-gray-600">Descripción y observaciones (opcional)</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </summary>
            <div class="p-6">
                <div class="space-y-5">
                    {{-- Descripción --}}
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Descripción
                        </label>
                        <textarea name="descripcion" id="descripcion" rows="2" placeholder="Descripción general de la comisión..."
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-shadow resize-none">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Observaciones --}}
                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Observaciones
                        </label>
                        <textarea name="observaciones" id="observaciones" rows="2" placeholder="Notas adicionales..."
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-shadow resize-none">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </details>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('comisiones.index') }}"
                class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Crear Comisión
            </button>
        </div>
    </form>
</div>
@endsection