@extends('layouts.app')
@section('title', 'Auto-crear Comisiones')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('comisiones.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Auto-crear Comisiones</h1>
                <p class="text-sm text-gray-500 mt-0.5">Genera comisiones automaticamente segun la demanda de inscripciones y las aulas disponibles</p>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('auto-crear-comisiones.preview') }}" id="formAutoCrear">
        @csrf

        <!-- Año -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-utn-blue-darker rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Configuracion</h2>
                        <p class="text-sm text-gray-600">Selecciona el año y las materias para las nuevas comisiones</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="anio" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Año <span class="text-red-500">*</span>
                        </label>
                        <select name="anio" id="anio" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @foreach($anios as $anio)
                            <option value="{{ $anio }}" {{ $anio == date('Y') ? 'selected' : '' }}>{{ $anio }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Materias -->
                <div class="mt-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Materias a asignar <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        @foreach($materias as $materia)
                        <label class="flex items-center gap-3 p-2 bg-white rounded-lg border border-gray-200 hover:border-indigo-300 cursor-pointer transition-colors">
                            <input type="checkbox" name="materias[]" value="{{ $materia->id }}" checked
                                class="rounded border-gray-300 text-utn-blue-dark focus:ring-indigo-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">{{ $materia->nombre }}</span>
                                <span class="text-xs text-gray-500 ml-1">[{{ $materia->tipo }}]</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('materias')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Municipios/Sedes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-emerald-100 border-b border-emerald-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Municipios y Aulas</h2>
                        <p class="text-sm text-gray-600">Selecciona las sedes cuyas aulas se usaran para definir cupos</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if($municipios->isEmpty())
                <div class="p-4 bg-amber-50 rounded-lg border border-amber-200 text-center">
                    <p class="text-sm text-amber-800">No hay municipios con aulas activas.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($municipios as $municipio)
                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-emerald-300 cursor-pointer transition-colors">
                        <input type="checkbox" name="municipios[]" value="{{ $municipio->id }}" checked
                            class="mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-5 h-5">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $municipio->nombre }}</p>
                            @if($municipio->direccion)
                            <p class="text-xs text-gray-500">{{ $municipio->direccion }}</p>
                            @endif
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                @foreach($municipio->aulas as $aula)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-700">
                                    {{ $aula->nombre }} ({{ $aula->capacidad ?? '?' }} cupos)
                                </span>
                                @endforeach
                                @if($municipio->aulas->isEmpty())
                                <span class="text-xs text-gray-400 italic">Sin aulas activas</span>
                                @endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @endif
                @error('municipios')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Botones -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('comisiones.index') }}"
                class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" id="btnGenerar"
                class="px-6 py-2.5 bg-utn-blue-darker text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all flex items-center gap-2">
                <svg id="btnGenerarIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <svg id="btnGenerarSpinner" class="hidden animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="btnGenerarText">Generar Vista Previa</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('formAutoCrear').addEventListener('submit', function() {
    document.getElementById('btnGenerar').disabled = true;
    document.getElementById('btnGenerarIcon').classList.add('hidden');
    document.getElementById('btnGenerarSpinner').classList.remove('hidden');
    document.getElementById('btnGenerarText').textContent = 'Analizando demanda...';
});
</script>
@endpush
@endsection
