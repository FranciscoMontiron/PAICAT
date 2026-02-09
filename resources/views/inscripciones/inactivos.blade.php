@extends('layouts.app')
@section('title', 'Estudiantes Inactivos')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Estudiantes Inactivos</h1>
            <p class="text-gray-600 mt-1">Alumnos cursando sin actividad en los últimos {{ $diasLimite }} días</p>
        </div>
        <a href="{{ route('inscripciones.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver
        </a>
    </div>

    <!-- Alertas -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
        <p class="font-bold">Listo</p>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <!-- Filtro de días -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('inscripciones.inactivos') }}" class="flex items-center gap-4">
            <label for="dias" class="text-sm font-medium text-gray-700">Días de inactividad:</label>
            <input type="number" name="dias" id="dias" value="{{ $diasLimite }}" min="1" max="365"
                class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium transition">
                Filtrar
            </button>
            <span class="text-sm text-gray-500 ml-2">
                (Configurado por defecto: {{ config('paicat.dias_inactividad', 30) }} días)
            </span>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-600">Total Inactivos</p>
            <p class="text-2xl font-bold text-gray-800">{{ $inactivos->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-sm text-gray-600">Promedio días sin actividad</p>
            <p class="text-2xl font-bold text-gray-800">
                {{ $inactivos->count() > 0 ? round($inactivos->avg('dias_sin_actividad')) : 0 }}
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600">Mayor inactividad</p>
            <p class="text-2xl font-bold text-gray-800">
                {{ $inactivos->count() > 0 ? $inactivos->max('dias_sin_actividad') : 0 }} días
            </p>
        </div>
    </div>

    @if($inactivos->count() > 0)
    <!-- Formulario de baja masiva -->
    <form method="POST" action="{{ route('inscripciones.baja-inactivos') }}" id="formBaja"
        onsubmit="return confirm('¿Confirma marcar como LIBRE a los estudiantes seleccionados?')">
        @csrf

        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-semibold text-gray-800">Listado de Inactivos</h2>
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        Seleccionar todos
                    </label>
                </div>
                <span id="selectionCount" class="text-sm text-gray-500">0 seleccionados</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12"></th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alumno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comisión</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Última actividad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días inactivo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($inactivos as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="inscripcion_ids[]" value="{{ $item['inscripcion']->id }}"
                                    class="alumno-check rounded border-gray-300 text-red-600 focus:ring-red-500">
                            </td>
                            <td class="px-6 py-4">
                                @if($item['persona'])
                                <div class="text-sm font-medium text-gray-900">{{ $item['persona']->apellido }}, {{ $item['persona']->nombre }}</div>
                                <div class="text-xs text-gray-500">DNI: {{ $item['persona']->nro_documento ?? '-' }}</div>
                                @else
                                <span class="text-gray-400">Sin datos</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($item['comision'])
                                {{ $item['comision']->codigo }} - {{ $item['comision']->nombre }}
                                <div class="text-xs text-gray-500">{{ $item['comision']->modalidad }} | {{ $item['comision']->turno ?? 'Virtual' }}</div>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $item['ultima_actividad']->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @php $dias = $item['dias_sin_actividad']; @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $dias > 60 ? 'bg-red-100 text-red-800' : ($dias > 30 ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $dias }} días
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('inscripciones.show', $item['inscripcion']) }}"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Acción masiva -->
        <div id="accionMasiva" class="bg-white rounded-lg shadow p-4 flex items-center gap-4 hidden">
            <div class="flex-1">
                <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">Motivo de baja por inactividad:</label>
                <input type="text" name="motivo" id="motivo"
                    value="Baja por inactividad: sin actividad registrada en los últimos {{ $diasLimite }} días"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
            </div>
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 text-sm font-medium transition whitespace-nowrap">
                Marcar como Libre
            </button>
        </div>
    </form>
    @else
    <div class="bg-white rounded-lg shadow text-center py-12">
        <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="mt-2 text-lg font-medium text-gray-900">Sin inactivos</h3>
        <p class="mt-1 text-sm text-gray-500">No hay estudiantes con más de {{ $diasLimite }} días sin actividad.</p>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.alumno-check');
    const selectionCount = document.getElementById('selectionCount');
    const accionMasiva = document.getElementById('accionMasiva');

    function updateUI() {
        const checked = document.querySelectorAll('.alumno-check:checked').length;
        if (selectionCount) selectionCount.textContent = checked + ' seleccionados';
        if (accionMasiva) accionMasiva.classList.toggle('hidden', checked === 0);
        if (checkAll) {
            checkAll.checked = checked === checkboxes.length && checkboxes.length > 0;
            checkAll.indeterminate = checked > 0 && checked < checkboxes.length;
        }
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateUI));
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateUI();
        });
    }
});
</script>
@endsection
