@extends('layouts.app')

@section('title', 'Cargar Notas: ' . $evaluacion->nombre)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header con breadcrumb --}}
    <div class="mb-6">
        <nav class="flex mb-3" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm">
                <li>
                    <a href="{{ route('evaluaciones.index') }}" class="text-gray-500 hover:text-utn-blue">Evaluaciones</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <a href="{{ route('evaluaciones.comision', $comision) }}" class="text-gray-500 hover:text-utn-blue">{{ $comision->nombre }}</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-700 font-medium">Cargar Notas</span>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $evaluacion->nombre }}</h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                        @switch($evaluacion->tipo)
                            @case('parcial') bg-blue-100 text-blue-700 @break
                            @case('recuperatorio') bg-orange-100 text-orange-700 @break
                            @case('trabajo_practico') bg-green-100 text-green-700 @break
                            @case('examen_final') bg-red-100 text-red-700 @break
                            @default bg-gray-100 text-gray-700
                        @endswitch">
                        {{ $evaluacion->tipo_nombre }}
                    </span>
                    @if($evaluacion->materia)
                        <span class="text-sm text-gray-600">{{ $evaluacion->materia->nombre }}</span>
                    @endif
                    <span class="text-sm text-gray-500">{{ $evaluacion->fecha->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <p class="text-sm text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Estadísticas rápidas --}}
    @php
        $totalAlumnos = $inscripciones->count();
        $notasCargadas = collect($notas)->count();
        $aprobados = collect($notas)->filter(fn($n) => $n && $n->nota >= 4)->count();
        $desaprobados = collect($notas)->filter(fn($n) => $n && $n->nota < 4)->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $totalAlumnos }}</p>
            <p class="text-xs text-gray-500">Total Alumnos</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $notasCargadas }}</p>
            <p class="text-xs text-gray-500">Notas Cargadas</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $aprobados }}</p>
            <p class="text-xs text-gray-500">Aprobados (>=4)</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $desaprobados }}</p>
            <p class="text-xs text-gray-500">Desaprobados</p>
        </div>
    </div>

    {{-- Formulario de notas --}}
    <form action="{{ route('evaluaciones.store-carga-masiva', $evaluacion) }}" method="POST" id="formNotas">
        @csrf
        <div class="bg-white shadow-md rounded-xl overflow-hidden">
            {{-- Header del formulario --}}
            <div class="px-5 py-4 bg-gray-50 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <input type="text" id="buscarAlumno" placeholder="Buscar alumno..."
                               class="pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent w-64">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-500">
                        <span id="contadorVisibles">{{ $totalAlumnos }}</span> de {{ $totalAlumnos }} alumnos
                    </span>
                </div>
                <button type="submit" class="inline-flex items-center px-5 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Notas
                </button>
            </div>

            {{-- Tabla de notas --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">#</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumno</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">DNI</th>
                            <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Nota Actual</th>
                            <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Nueva Nota</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tablaAlumnos">
                        @forelse($inscripciones as $index => $ic)
                            @php
                                $inscripcion = $ic->inscripcion;
                                $alumno = $inscripcion?->getPerson() ?? $ic->academicoDato;
                                if (!$alumno) continue;
                                $dni = $alumno->documento ?? $alumno->dni ?? '-';
                                $notaActual = $notas[$inscripcion->id] ?? null;
                                $valorNota = $notaActual ? (float)$notaActual->nota : null;
                            @endphp
                            <tr class="hover:bg-gray-50 fila-alumno"
                                data-nombre="{{ strtolower($alumno->apellido . ' ' . $alumno->nombre) }}"
                                data-dni="{{ $dni }}">
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-9 w-9 bg-gray-200 rounded-full flex items-center justify-center">
                                            <span class="text-gray-600 font-medium text-sm">
                                                {{ strtoupper(substr($alumno->nombre, 0, 1) . substr($alumno->apellido, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $alumno->apellido }}, {{ $alumno->nombre }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $dni }}
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    @if($notaActual)
                                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg text-sm font-bold
                                            {{ $valorNota >= 4 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ number_format($valorNota, 1) }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    <input type="number"
                                           name="notas[{{ $inscripcion->id }}]"
                                           value="{{ $valorNota !== null ? $valorNota : '' }}"
                                           min="0"
                                           max="10"
                                           step="0.5"
                                           class="w-24 text-center py-2 text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent nota-input"
                                           placeholder="-"
                                           tabindex="{{ $loop->iteration }}">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="mt-2 text-gray-500">No hay alumnos inscriptos en esta comisión</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer del formulario --}}
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-4">
                    <button type="button" onclick="limpiarNotas()" class="text-sm text-gray-600 hover:text-gray-800">
                        Limpiar todos
                    </button>
                    <span class="text-sm text-gray-500">|</span>
                    <button type="button" onclick="setNotaRapida(0)" class="text-sm text-red-600 hover:text-red-800">
                        Todos 0
                    </button>
                    <button type="button" onclick="setNotaRapida(4)" class="text-sm text-yellow-600 hover:text-yellow-800">
                        Todos 4
                    </button>
                    <button type="button" onclick="setNotaRapida(7)" class="text-sm text-green-600 hover:text-green-800">
                        Todos 7
                    </button>
                </div>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Buscar alumno
document.getElementById('buscarAlumno').addEventListener('input', function() {
    const termino = this.value.toLowerCase();
    const filas = document.querySelectorAll('.fila-alumno');
    let visibles = 0;

    filas.forEach(fila => {
        const nombre = fila.dataset.nombre;
        const dni = fila.dataset.dni;
        const coincide = nombre.includes(termino) || dni.includes(termino);
        fila.style.display = coincide ? '' : 'none';
        if (coincide) visibles++;
    });

    document.getElementById('contadorVisibles').textContent = visibles;
});

// Limpiar notas
function limpiarNotas() {
    if (confirm('¿Limpiar todas las notas?')) {
        document.querySelectorAll('.nota-input').forEach(input => {
            input.value = '';
        });
    }
}

// Nota rápida
function setNotaRapida(valor) {
    const filas = document.querySelectorAll('.fila-alumno');
    filas.forEach(fila => {
        if (fila.style.display !== 'none') {
            const input = fila.querySelector('.nota-input');
            if (input && input.value === '') {
                input.value = valor;
            }
        }
    });
}

// Colorear input según nota
document.querySelectorAll('.nota-input').forEach(input => {
    input.addEventListener('change', function() {
        const valor = parseFloat(this.value);
        this.classList.remove('bg-green-50', 'bg-red-50', 'text-green-700', 'text-red-700');
        if (!isNaN(valor)) {
            if (valor >= 4) {
                this.classList.add('bg-green-50', 'text-green-700');
            } else {
                this.classList.add('bg-red-50', 'text-red-700');
            }
        }
    });
    // Trigger initial color
    if (input.value) {
        input.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
