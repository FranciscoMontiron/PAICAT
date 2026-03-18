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
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-utn-blue/20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-utn-blue-darker rounded-lg">
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
                {{-- Materias (selección múltiple) --}}
                <div class="mb-5">
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
                                class="rounded border-gray-300 text-utn-blue-dark focus:ring-indigo-500">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nombre --}}
                    <div class="md:col-span-2">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nombre de la Comisión <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $comision->nombre) }}" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('nombre') border-red-500 @enderror">
                        @error('nombre')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Código --}}
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $comision->codigo) }}" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('codigo') border-red-500 @enderror">
                        @error('codigo')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Año --}}
                    <div>
                        <label for="anio" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Año <span class="text-red-500">*</span>
                        </label>
                        <select name="anio" id="anio" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('anio') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            @for($year = date('Y') - 2; $year <= date('Y') + 2; $year++)
                            <option value="{{ $year }}" {{ old('anio', $comision->anio) == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                        @error('anio')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Periodo --}}
                    <div>
                        <label for="periodo" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Periodo <span class="text-red-500">*</span>
                        </label>
                        <select name="periodo" id="periodo" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('periodo') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($tiposIngreso as $key => $value)
                            <option value="{{ $key }}" {{ old('periodo', $comision->periodo) == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('periodo')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Modalidad --}}
                    <div>
                        <label for="modalidad" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Modalidad <span class="text-red-500">*</span>
                        </label>
                        <select name="modalidad" id="modalidad" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('modalidad') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($modalidades as $key => $value)
                            <option value="{{ $key }}" {{ old('modalidad', $comision->modalidad) == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('modalidad')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p id="modalidad-info" class="mt-1.5 text-xs text-utn-blue-dark hidden"></p>
                    </div>

                    {{-- Turno --}}
                    <div id="turno-container">
                        <label for="turno" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Turno <span class="text-red-500 turno-required">*</span>
                        </label>
                        <select name="turno" id="turno"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('turno') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($turnos as $key => $value)
                            <option value="{{ $key }}" {{ old('turno', $comision->turno) == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('turno')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select name="estado" id="estado" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('estado') border-red-500 @enderror">
                            @foreach(\App\Models\Comision::getEstados() as $key => $label)
                                <option value="{{ $key }}" {{ old('estado', $comision->estado) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('estado')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cupo Máximo --}}
                    <div id="cupo-container">
                        <label for="cupo_maximo" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Cupo Máximo <span class="text-red-500 cupo-required">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="cupo_maximo" id="cupo_maximo" value="{{ old('cupo_maximo', $comision->cupo_maximo) }}" min="{{ $comision->cupo_real }}" max="200"
                                class="w-full px-4 py-2.5 pr-16 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('cupo_maximo') border-red-500 @enderror">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">alumnos</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Cupo actual: {{ $comision->cupo_real }} inscriptos (mínimo: {{ $comision->cupo_real }})</p>
                        @error('cupo_maximo')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Municipio y Aula --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5" id="municipio-container">
                    <div>
                        <label for="municipio_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Municipio/Sede <span class="text-red-500 municipio-required">*</span>
                        </label>
                        <select name="municipio_id" id="municipio_id"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('municipio_id') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($municipios ?? [] as $municipio)
                            <option value="{{ $municipio->id }}" {{ old('municipio_id', $comision->municipio_id) == $municipio->id ? 'selected' : '' }}>
                                {{ $municipio->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @error('municipio_id')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Sede presencial donde se dictarán las clases</p>
                    </div>

                    {{-- Aula --}}
                    <div id="aula-container">
                        <label for="aula_id" class="block text-sm font-medium text-gray-700 mb-1.5">Aula</label>
                        <select name="aula_id" id="aula_id"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow @error('aula_id') border-red-500 @enderror">
                            <option value="">Sin asignar</option>
                            @foreach($aulas ?? [] as $aula)
                            <option value="{{ $aula->id }}" data-municipio="{{ $aula->municipio_id }}" {{ old('aula_id', $comision->aula_id) == $aula->id ? 'selected' : '' }}>
                                {{ $aula->nombre_completo ?? $aula->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @error('aula_id')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección: Docentes Asignados --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-amber-100 border-b border-amber-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Docentes Asignados</h2>
                        <p class="text-sm text-gray-600">Selecciona uno o más docentes para esta comisión</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @php
                $docentesSeleccionados = old('docentes', $comision->docentes->pluck('id')->toArray());
                @endphp
                @if($docentes->isEmpty())
                <div class="p-4 bg-amber-50 rounded-lg border border-amber-200 text-center">
                    <svg class="w-10 h-10 mx-auto text-amber-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9.253a2.5 2.5 0 11-3.536 3.536L12.5 6.5" />
                    </svg>
                    <p class="text-sm text-amber-800">No hay docentes disponibles.</p>
                    <p class="text-xs text-amber-600">Primero debes registrar usuarios con rol docente.</p>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto p-1">
                    @foreach($docentes as $docente)
                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-amber-300 hover:bg-amber-50/50 cursor-pointer transition-colors">
                        <input type="checkbox" name="docentes[]" value="{{ $docente->id }}"
                            {{ in_array($docente->id, $docentesSeleccionados) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 w-5 h-5">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $docente->nombre_completo }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $docente->email }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-gray-500">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Puedes seleccionar múltiples docentes. El primero seleccionado será el docente principal.
                </p>
                @endif
                @error('docentes')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Sección: Información Adicional --}}
        <details class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8 group" open>
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
                            <p class="text-sm text-gray-600">Descripción y observaciones</p>
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
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-shadow resize-none @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $comision->descripcion) }}</textarea>
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
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-shadow resize-none @error('observaciones') border-red-500 @enderror">{{ old('observaciones', $comision->observaciones) }}</textarea>
                        @error('observaciones')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </details>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('comisiones.show', $comision) }}"
                class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-utn-blue-darker text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalidadSelect = document.getElementById('modalidad');
    const turnoContainer = document.getElementById('turno-container');
    const turnoSelect = document.getElementById('turno');
    const cupoContainer = document.getElementById('cupo-container');
    const cupoInput = document.getElementById('cupo_maximo');
    const municipioContainer = document.getElementById('municipio-container');
    const municipioSelect = document.getElementById('municipio_id');
    const aulaSelect = document.getElementById('aula_id');
    const modalidadInfo = document.getElementById('modalidad-info');

    // Elementos de asterisco requerido
    const turnoRequired = document.querySelector('.turno-required');
    const cupoRequired = document.querySelector('.cupo-required');
    const municipioRequired = document.querySelector('.municipio-required');

    function actualizarCamposPorModalidad() {
        const modalidad = modalidadSelect.value.toLowerCase();

        if (modalidad === 'virtual') {
            // Virtual: sin turno, sin cupo, municipio opcional
            turnoContainer.classList.add('opacity-50');
            turnoSelect.removeAttribute('required');
            turnoRequired.classList.add('hidden');

            cupoContainer.classList.add('opacity-50');
            cupoInput.removeAttribute('required');
            cupoRequired.classList.add('hidden');

            municipioRequired.classList.add('hidden');
            municipioSelect.removeAttribute('required');

            modalidadInfo.textContent = 'Virtual: Sin turno fijo, sin límite de cupos. Los alumnos rinden un examen final libre.';
            modalidadInfo.classList.remove('hidden');

        } else if (modalidad === 'semipresencial') {
            // Semipresencial: turno opcional, cupo normal, municipio requerido
            turnoContainer.classList.remove('opacity-50');
            turnoSelect.removeAttribute('required');
            turnoRequired.classList.add('hidden');

            cupoContainer.classList.remove('opacity-50');
            cupoInput.setAttribute('required', 'required');
            cupoRequired.classList.remove('hidden');

            municipioRequired.classList.remove('hidden');
            municipioSelect.setAttribute('required', 'required');

            modalidadInfo.textContent = 'Semipresencial: Rinden parciales pero la asistencia a clases es opcional.';
            modalidadInfo.classList.remove('hidden');

        } else {
            // Presencial: todos los campos requeridos
            turnoContainer.classList.remove('opacity-50');
            turnoSelect.setAttribute('required', 'required');
            turnoRequired.classList.remove('hidden');

            cupoContainer.classList.remove('opacity-50');
            cupoInput.setAttribute('required', 'required');
            cupoRequired.classList.remove('hidden');

            municipioRequired.classList.remove('hidden');
            municipioSelect.setAttribute('required', 'required');

            modalidadInfo.textContent = '';
            modalidadInfo.classList.add('hidden');
        }
    }

    // Filtrar aulas por municipio
    function filtrarAulasPorMunicipio() {
        const municipioId = municipioSelect.value;
        const opciones = aulaSelect.querySelectorAll('option[data-municipio]');

        opciones.forEach(opcion => {
            if (!municipioId || opcion.dataset.municipio === municipioId) {
                opcion.classList.remove('hidden');
                opcion.disabled = false;
            } else {
                opcion.classList.add('hidden');
                opcion.disabled = true;
                if (opcion.selected) {
                    aulaSelect.value = '';
                }
            }
        });
    }

    // Event listeners
    modalidadSelect.addEventListener('change', actualizarCamposPorModalidad);
    municipioSelect.addEventListener('change', filtrarAulasPorMunicipio);

    // Estado inicial
    actualizarCamposPorModalidad();
    filtrarAulasPorMunicipio();
});
</script>
@endpush
@endsection
