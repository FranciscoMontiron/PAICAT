@extends('layouts.app')
@section('title', 'Editar Comisión')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('comisiones.show', $comision) }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Comisión</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $comision->nombre }} ({{ $comision->codigo }})</p>
            </div>
        </div>
    </div>

    <form action="{{ route('comisiones.update', $comision) }}" method="POST">
        @csrf
        @method('PUT')

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
                    {{-- Materias (selección múltiple) --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Materias <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 font-normal ml-1">(Seleccione una o más materias)</span>
                        </label>
                        @php
                        $materiasSeleccionadas = old('materias', $comision->materias->pluck('id')->toArray());
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 p-4 bg-gray-50 rounded-lg border border-gray-200 max-h-48 overflow-y-auto">
                            @foreach($materias as $materia)
                            <label class="flex items-center gap-3 p-2 bg-white rounded-lg border border-gray-200 hover:border-indigo-300 cursor-pointer transition-colors">
                                <input type="checkbox" name="materias[]" value="{{ $materia->id }}"
                                    {{ in_array($materia->id, $materiasSeleccionadas) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $materia->nombre }}</span>
                                    <span class="text-xs text-gray-500 ml-1">[{{ $materia->codigo }}]</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('materias')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre -->
                    <div class="md:col-span-2">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre de la Comisión <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $comision->nombre) }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('nombre') border-red-500 @enderror">
                        @error('nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Código -->
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $comision->codigo) }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('codigo') border-red-500 @enderror">
                        @error('codigo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Año -->
                    <div>
                        <label for="anio" class="block text-sm font-medium text-gray-700 mb-1">
                            Año <span class="text-red-500">*</span>
                        </label>
                        <select name="anio" id="anio" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('anio') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            @for($year = date('Y') - 2; $year <= date('Y') + 2; $year++)
                                <option value="{{ $year }}" {{ old('anio', $comision->anio) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                        </select>
                        @error('anio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Periodo -->
                    <div>
                        <label for="periodo" class="block text-sm font-medium text-gray-700 mb-1">
                            Periodo <span class="text-red-500">*</span>
                        </label>
                        <select name="periodo" id="periodo" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('periodo') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            <option value="Verano" {{ old('periodo', $comision->periodo) == 'Verano' ? 'selected' : '' }}>Verano</option>
                            <option value="Invierno" {{ old('periodo', $comision->periodo) == 'Invierno' ? 'selected' : '' }}>Invierno</option>
                            <option value="Anual" {{ old('periodo', $comision->periodo) == 'Anual' ? 'selected' : '' }}>Anual</option>
                        </select>
                        @error('periodo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Turno -->
                    <div>
                        <label for="turno" class="block text-sm font-medium text-gray-700 mb-1">
                            Turno <span class="text-red-500">*</span>
                        </label>
                        <select name="turno" id="turno" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('turno') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            <option value="Mañana" {{ old('turno', $comision->turno) == 'Mañana' ? 'selected' : '' }}>Mañana</option>
                            <option value="Tarde" {{ old('turno', $comision->turno) == 'Tarde' ? 'selected' : '' }}>Tarde</option>
                            <option value="Noche" {{ old('turno', $comision->turno) == 'Noche' ? 'selected' : '' }}>Noche</option>
                        </select>
                        @error('turno')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Modalidad -->
                    <div>
                        <label for="modalidad" class="block text-sm font-medium text-gray-700 mb-1">
                            Modalidad <span class="text-red-500">*</span>
                        </label>
                        <select name="modalidad" id="modalidad" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('modalidad') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            <option value="Presencial" {{ old('modalidad', $comision->modalidad) == 'Presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="Virtual" {{ old('modalidad', $comision->modalidad) == 'Virtual' ? 'selected' : '' }}>Virtual</option>
                            <option value="Semipresencial" {{ old('modalidad', $comision->modalidad) == 'Semipresencial' ? 'selected' : '' }}>Semipresencial</option>
                        </select>
                        @error('modalidad')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select name="estado" id="estado" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('estado') border-red-500 @enderror">
                            <option value="activa" {{ old('estado', $comision->estado) == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="cerrada" {{ old('estado', $comision->estado) == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                            <option value="finalizada" {{ old('estado', $comision->estado) == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                            <option value="cancelada" {{ old('estado', $comision->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                        @error('estado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cupo Máximo -->
                    <div>
                        <label for="cupo_maximo" class="block text-sm font-medium text-gray-700 mb-1">
                            Cupo Máximo <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="cupo_maximo" id="cupo_maximo" value="{{ old('cupo_maximo', $comision->cupo_maximo) }}" required min="{{ $comision->cupo_actual }}" max="200"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('cupo_maximo') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Cupo actual: {{ $comision->cupo_actual }} inscriptos (mínimo: {{ $comision->cupo_actual }}, máximo: 200)</p>
                        @error('cupo_maximo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Docente -->
                    <div>
                        <label for="docente_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Docente Asignado
                        </label>
                        <select name="docente_id" id="docente_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('docente_id') border-red-500 @enderror">
                            <option value="">Sin asignar</option>
                            @foreach($docentes as $docente)
                            <option value="{{ $docente->id }}" {{ old('docente_id', $comision->docente_id) == $docente->id ? 'selected' : '' }}>
                                {{ $docente->nombre_completo }}
                            </option>
                            @endforeach
                        </select>
                        @error('docente_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha Inicio -->
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Inicio
                        </label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio', $comision->fecha_inicio?->format('Y-m-d')) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('fecha_inicio') border-red-500 @enderror">
                        @error('fecha_inicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha Fin -->
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Fin
                        </label>
                        <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin', $comision->fecha_fin?->format('Y-m-d')) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('fecha_fin') border-red-500 @enderror">
                        @error('fecha_fin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                            Descripción
                        </label>
                        <textarea name="descripcion" id="descripcion" rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $comision->descripcion) }}</textarea>
                        @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Observaciones -->
                    <div class="md:col-span-2">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                            Observaciones
                        </label>
                        <textarea name="observaciones" id="observaciones" rows="2"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('observaciones') border-red-500 @enderror">{{ old('observaciones', $comision->observaciones) }}</textarea>
                        @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('comisiones.show', $comision) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition duration-200">
                        Guardar Cambios
                    </button>
                </div>
    </form>
</div>
</div>
@endsection