@extends('layouts.app')
@section('title', 'Vista Previa - Auto-crear Comisiones')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('auto-crear-comisiones.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Vista Previa de Comisiones</h1>
                <p class="text-sm text-gray-500 mt-0.5">Revisa y selecciona las comisiones que deseas crear</p>
            </div>
        </div>
    </div>

    <!-- Resumen -->
    @php
        $totalPropuestas = count($propuestas);
        $totalSeleccionadas = collect($propuestas)->where('seleccionada', true)->count();
        $totalDemanda = collect($propuestas)->sum('demanda');
        $totalSinAsignar = $sinAsignar->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Inscripciones sin comisión</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalSinAsignar }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Comisiones propuestas</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $totalPropuestas }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Cupos cubiertos</p>
            <p class="text-2xl font-bold text-green-600">{{ $totalDemanda }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Materias a vincular</p>
            <p class="text-2xl font-bold text-gray-900">{{ $materias->count() }}</p>
        </div>
    </div>

    <!-- Resumen por grupo -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-amber-100 border-b border-amber-200">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-500 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Demanda por grupo</h2>
                    <p class="text-sm text-gray-600">Distribución de inscripciones sin comisión agrupadas por modalidad, periodo y turno</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-2">
                @foreach($resumenGrupos as $clave => $cantidad)
                @php
                    $partes = explode('|', $clave);
                    $mod = ucfirst($partes[0] ?? '');
                    $per = ucfirst($partes[1] ?? '');
                    $tur = ($partes[2] ?? 'sin_turno') === 'sin_turno' ? '' : ucfirst($partes[2]);
                @endphp
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-800 border border-gray-200">
                    {{ $mod }} / {{ $per }}
                    @if($tur) / {{ $tur }} @endif
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-bold bg-amber-200 text-amber-800">{{ $cantidad }}</span>
                </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <form method="POST" action="{{ route('auto-crear-comisiones.ejecutar') }}" id="formEjecutar">
        @csrf
        <input type="hidden" name="anio" value="{{ $anio }}">
        @foreach($materiasIds as $materiaId)
        <input type="hidden" name="materias[]" value="{{ $materiaId }}">
        @endforeach

        <!-- Materias que se vincularán -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">Materias que se vincularán a cada comisión:</h3>
            </div>
            <div class="px-6 py-3 flex flex-wrap gap-2">
                @foreach($materias as $materia)
                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-700">
                    {{ $materia->nombre }} <span class="ml-1 text-indigo-400">[{{ $materia->tipo }}]</span>
                </span>
                @endforeach
            </div>
        </div>

        <!-- Tabla de propuestas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-utn-blue-darker rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Comisiones a crear ({{ $anio }})</h2>
                            <p class="text-sm text-gray-600">Desmarca las que no desees crear</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnSeleccionarTodas" class="text-xs px-3 py-1.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium transition-colors">
                            Seleccionar todas
                        </button>
                        <button type="button" id="btnDeseleccionarTodas" class="text-xs px-3 py-1.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium transition-colors">
                            Deseleccionar todas
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">Crear</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modalidad</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Turno</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sede / Aula</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cupo</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Demanda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($propuestas as $index => $prop)
                        <tr class="propuesta-row hover:bg-gray-50 transition-colors {{ $prop['seleccionada'] ? '' : 'opacity-50' }}">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="comisiones[{{ $index }}][seleccionada]" value="1"
                                    class="checkbox-propuesta rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5"
                                    {{ $prop['seleccionada'] ? 'checked' : '' }}>
                            </td>
                            <td class="px-4 py-3">
                                <input type="hidden" name="comisiones[{{ $index }}][codigo]" value="{{ $prop['codigo'] }}">
                                <span class="font-mono text-sm text-gray-900">{{ $prop['codigo'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <input type="hidden" name="comisiones[{{ $index }}][nombre]" value="{{ $prop['nombre'] }}">
                                <span class="text-sm text-gray-900">{{ $prop['nombre'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <input type="hidden" name="comisiones[{{ $index }}][modalidad]" value="{{ $prop['modalidad'] }}">
                                @php
                                    $modColor = match(strtolower($prop['modalidad'])) {
                                        'presencial' => 'bg-blue-100 text-blue-700',
                                        'virtual' => 'bg-purple-100 text-purple-700',
                                        'semipresencial' => 'bg-teal-100 text-teal-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $modColor }}">{{ $prop['modalidad'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <input type="hidden" name="comisiones[{{ $index }}][periodo]" value="{{ $prop['periodo'] }}">
                                <span class="text-sm text-gray-700">{{ $prop['periodo'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <input type="hidden" name="comisiones[{{ $index }}][turno]" value="{{ $prop['turno'] ?? '' }}">
                                <span class="text-sm text-gray-700">{{ $prop['turno'] ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <input type="hidden" name="comisiones[{{ $index }}][municipio_id]" value="{{ $prop['municipio_id'] ?? '' }}">
                                <input type="hidden" name="comisiones[{{ $index }}][aula_id]" value="{{ $prop['aula_id'] ?? '' }}">
                                <div class="text-sm">
                                    @if($prop['municipio_nombre'])
                                    <span class="text-gray-900">{{ $prop['municipio_nombre'] }}</span>
                                    @else
                                    <span class="text-gray-400 italic">Sin sede</span>
                                    @endif
                                    @if($prop['aula_nombre'])
                                    <br><span class="text-xs text-gray-500">{{ $prop['aula_nombre'] }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="hidden" name="comisiones[{{ $index }}][cupo_maximo]" value="{{ $prop['cupo_maximo'] ?? '' }}">
                                @if($prop['cupo_maximo'])
                                <span class="text-sm font-semibold text-gray-900">{{ $prop['cupo_maximo'] }}</span>
                                @else
                                <span class="text-xs text-purple-600 font-medium">Sin límite</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    {{ $prop['demanda'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-600">
                <span id="contadorSeleccionadas">{{ $totalSeleccionadas }}</span> de {{ $totalPropuestas }} comisiones seleccionadas
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('auto-crear-comisiones.index') }}"
                    class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Volver
                </a>
                <button type="submit" id="btnCrear"
                    class="px-6 py-2.5 bg-green-700 text-white font-medium rounded-lg hover:bg-green-800 focus:ring-4 focus:ring-green-200 transition-all flex items-center gap-2 shadow-md">
                    <svg id="btnCrearIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg id="btnCrearSpinner" class="hidden animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="btnCrearText">Crear Comisiones Seleccionadas</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Toggle row opacity on checkbox change
document.querySelectorAll('.checkbox-propuesta').forEach(cb => {
    cb.addEventListener('change', function() {
        const row = this.closest('.propuesta-row');
        row.classList.toggle('opacity-50', !this.checked);
        actualizarContador();
    });
});

// Select/deselect all
document.getElementById('btnSeleccionarTodas').addEventListener('click', function() {
    document.querySelectorAll('.checkbox-propuesta').forEach(cb => {
        cb.checked = true;
        cb.closest('.propuesta-row').classList.remove('opacity-50');
    });
    actualizarContador();
});

document.getElementById('btnDeseleccionarTodas').addEventListener('click', function() {
    document.querySelectorAll('.checkbox-propuesta').forEach(cb => {
        cb.checked = false;
        cb.closest('.propuesta-row').classList.add('opacity-50');
    });
    actualizarContador();
});

function actualizarContador() {
    const total = document.querySelectorAll('.checkbox-propuesta:checked').length;
    document.getElementById('contadorSeleccionadas').textContent = total;
}

// Submit spinner
document.getElementById('formEjecutar').addEventListener('submit', function() {
    const btn = document.getElementById('btnCrear');
    btn.disabled = true;
    document.getElementById('btnCrearIcon').classList.add('hidden');
    document.getElementById('btnCrearSpinner').classList.remove('hidden');
    document.getElementById('btnCrearText').textContent = 'Creando comisiones...';
});
</script>
@endpush
@endsection
