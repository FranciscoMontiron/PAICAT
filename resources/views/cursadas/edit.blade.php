@extends('layouts.app')

@section('title', 'Editar Cursada')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-4 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('comisiones.show', $comision) }}" class="text-utn-blue-dark hover:underline">{{ $comision->codigo }}</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="{{ route('cursadas.index', $comision) }}" class="text-utn-blue-dark hover:underline">Cursadas</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li class="text-gray-500">Editar</li>
        </ol>
    </nav>

    @php
        $estudiante = $cursada->getEstudiante();
    @endphp

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Editar Cursada</h1>
        <p class="text-gray-600 mt-1">
            {{ $estudiante ? $estudiante->apellido . ', ' . $estudiante->nombre : 'Sin datos' }} - {{ $comision->nombre }}
        </p>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('cursadas.update', [$comision, $cursada]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Estado --}}
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="estado" id="estado"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('estado') border-red-500 @enderror">
                        @foreach(\App\Models\Cursada::getEstados() as $key => $label)
                            <option value="{{ $key }}" {{ old('estado', $cursada->estado) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('estado')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Motivo del cambio (solo si cambia estado) --}}
                <div>
                    <label for="motivo_cambio" class="block text-sm font-medium text-gray-700 mb-1">
                        Motivo del cambio de estado
                    </label>
                    <input type="text" name="motivo_cambio" id="motivo_cambio" value="{{ old('motivo_cambio') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                           placeholder="Si cambia el estado, indique el motivo...">
                </div>

                {{-- Modalidad --}}
                <div>
                    <label for="modalidad" class="block text-sm font-medium text-gray-700 mb-1">Modalidad</label>
                    <select name="modalidad" id="modalidad"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('modalidad') border-red-500 @enderror">
                        <option value="">Misma que la comisión</option>
                        @foreach(\App\Models\Comision::getModalidades() as $key => $label)
                            <option value="{{ $key }}" {{ old('modalidad', $cursada->modalidad) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('modalidad')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nota Final --}}
                <div>
                    <label for="nota_final" class="block text-sm font-medium text-gray-700 mb-1">Nota Final</label>
                    <input type="number" name="nota_final" id="nota_final"
                           value="{{ old('nota_final', $cursada->nota_final) }}"
                           step="0.01" min="0" max="10"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('nota_final') border-red-500 @enderror"
                           placeholder="0.00 - 10.00">
                    @error('nota_final')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Puede dejarlo vacío para que se calcule automáticamente desde las notas.</p>
                </div>

                {{-- Resultado --}}
                <div>
                    <label for="resultado" class="block text-sm font-medium text-gray-700 mb-1">Resultado</label>
                    <input type="text" name="resultado" id="resultado" value="{{ old('resultado', $cursada->resultado) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('resultado') border-red-500 @enderror"
                           placeholder="Ej: Aprobado con 7.50">
                    @error('resultado')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Es recursante --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="es_recursante" id="es_recursante" value="1"
                           class="h-4 w-4 text-utn-blue-dark border-gray-300 rounded focus:ring-utn-blue-dark"
                           {{ old('es_recursante', $cursada->es_recursante) ? 'checked' : '' }}>
                    <label for="es_recursante" class="text-sm font-medium text-gray-700">
                        Es recursante (está repitiendo)
                    </label>
                </div>

                {{-- Observaciones --}}
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('observaciones') border-red-500 @enderror"
                              placeholder="Notas adicionales...">{{ old('observaciones', $cursada->observaciones) }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('cursadas.show', [$comision, $cursada]) }}"
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

    {{-- Info adicional --}}
    <div class="mt-6 bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
        <p><strong>Última modificación:</strong> {{ $cursada->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
        @if($cursada->usuarioCambioEstado)
            <p><strong>Último cambio de estado por:</strong> {{ $cursada->usuarioCambioEstado->name }} el {{ $cursada->fecha_cambio_estado?->format('d/m/Y H:i') }}</p>
        @endif
    </div>
</div>
@endsection
