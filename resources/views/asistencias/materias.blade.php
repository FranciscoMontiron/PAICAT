@extends('layouts.app')

@section('title', 'Materias - ' . $comision->nombre)

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-6">
        <a href="{{ route('asistencias.index') }}" class="hover:text-blue-600 transition">Asistencias</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-800 font-medium">{{ $comision->codigo }} - {{ $comision->nombre }}</span>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $comision->codigo }}</h1>
                <p class="text-gray-600 mt-2">{{ $comision->nombre }} - {{ $comision->turno }}</p>
                @if($comision->docente)
                    <p class="text-sm text-gray-500 mt-1">Docente: {{ $comision->docente->name }}</p>
                @endif
            </div>
            <a href="{{ route('asistencias.index') }}" 
               class="flex items-center gap-2 text-gray-600 hover:text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-700 font-medium mb-1">Total Alumnos</p>
            <p class="text-3xl font-bold text-blue-900">{{ $totalAlumnos }}</p>
        </div>
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
            <p class="text-sm text-indigo-700 font-medium mb-1">Total Materias</p>
            <p class="text-3xl font-bold text-indigo-900">{{ $comision->materias->count() }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-green-700 font-medium mb-1">Promedio Asistencia</p>
            <p class="text-3xl font-bold text-green-900">{{ round($promedioAsistencia, 1) }}%</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm text-red-700 font-medium mb-1">Alumnos en Riesgo</p>
            <p class="text-3xl font-bold text-red-900">{{ $alumnosEnRiesgo }}</p>
            <p class="text-xs text-red-400 mt-1">con &lt;75% en alguna materia</p>
        </div>
    </div>

    <!-- Listado de Materias -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-indigo-200">
            <h2 class="text-lg font-semibold text-gray-800">Materias de la Comisión</h2>
            <p class="text-sm text-gray-600 mt-1">Selecciona una materia para gestionar sus asistencias</p>
        </div>
        <div class="p-6">
            @if($comision->materias->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($comision->materias as $materia)
                @php
                    $estadisticas = $materia->estadisticasAsistencia($comision->id) ?? [
                        'total_clases' => 0,
                        'promedio_asistencia' => 0,
                        'alumnos_en_riesgo' => 0
                    ];
                @endphp
                <div class="border-2 border-gray-200 rounded-xl p-6 hover:shadow-xl hover:border-indigo-500 transition-all duration-200 group">
                    <!-- Header Materia -->
                    <div class="flex items-start gap-4 mb-4">
                        <div class="p-3 bg-indigo-100 text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 text-lg">{{ $materia->nombre }}</h3>
                            <p class="text-sm text-gray-500 font-mono mt-1">{{ $materia->codigo }}</p>
                            @if($materia->carga_horaria)
                            <p class="text-xs text-gray-400 mt-1">{{ $materia->carga_horaria }} horas semanales</p>
                            @endif
                        </div>
                    </div>

                    <!-- Estadísticas de la materia -->
                    <div class="grid grid-cols-3 gap-3 mb-4 pt-4 border-t border-gray-200">
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Clases</p>
                            <p class="text-lg font-bold text-gray-800">{{ $estadisticas['total_clases'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Asistencia</p>
                            <p class="text-lg font-bold 
                                @if($estadisticas['promedio_asistencia'] >= 75) text-green-600
                                @elseif($estadisticas['promedio_asistencia'] >= 50) text-yellow-600
                                @else text-red-600 @endif">
                                {{ round($estadisticas['promedio_asistencia'], 1) }}%
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Riesgo</p>
                            <p class="text-lg font-bold text-red-600">{{ $estadisticas['alumnos_en_riesgo'] }}</p>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col gap-2">
                        @if(auth()->user()->hasPermission('asistencias.crear') && $comision->estado == 'activa')
                        <button type="button"
                            onclick="abrirModal('{{ route('asistencias.materia.registrar', [$comision, $materia]) }}', '{{ addslashes($materia->nombre) }}')"
                            class="flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            Pasar Asistencia
                        </button>
                        @endif
                        
                        <a href="{{ route('asistencias.materia.historial', [$comision, $materia]) }}"
                            class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Ver Historial
                        </a>

                        @if(auth()->user()->hasPermission('asistencias.editar'))
                        <a href="{{ route('asistencias.materia.seleccionar-alumno', [$comision, $materia]) }}"
                            class="flex items-center justify-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Justificar
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Sin materias asignadas</h3>
                <p class="mt-2 text-sm text-gray-500">Esta comisión no tiene materias asignadas actualmente.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Listado de Alumnos (opcional, colapsable) -->
    @if($comision->inscripciones->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 cursor-pointer" onclick="toggleAlumnos()">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Lista de Alumnos ({{ $comision->inscripciones->count() }})</h3>
                <svg id="alumnosChevron" class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>
        <div id="alumnosList" class="hidden">
            <div class="p-6 space-y-2">
                @foreach($comision->inscripciones as $inscripcion)
                    @php
                        $porcentaje = $inscripcion->calcularPorcentajeAsistencia();
                        $estaEnRiesgo = $inscripcion->estaEnRiesgo();
                        $colorIndicador = $porcentaje >= 85 ? 'bg-green-500' : ($porcentaje >= 75 ? 'bg-yellow-500' : 'bg-red-500');
                    @endphp
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full {{ $colorIndicador }}"></div>
                            <div>
                                <p class="font-medium text-gray-800">
                                    @php
                                        // Usar el método getAlumnoAttribute() del modelo
                                        try {
                                            $alumno = $inscripcion->getAlumnoAttribute();
                                            $nombreAlumno = $alumno->name ?? $alumno->nombre_completo ?? 'Sin nombre';
                                        } catch (\Exception $e) {
                                            $nombreAlumno = 'Sin nombre';
                                        }
                                    @endphp
                                    {{ $nombreAlumno }}
                                </p>
                                @if($inscripcion->asistencias->count() > 0)
                                    <p class="text-sm text-gray-600">
                                        Asistencia: <span class="font-semibold {{ $estaEnRiesgo ? 'text-red-600' : 'text-green-600' }}">{{ round($porcentaje, 1) }}%</span>
                                        @if($estaEnRiesgo)
                                            <span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full font-semibold">En riesgo</span>
                                        @endif
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500">Sin registros de asistencia</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('asistencias.alumno.historial', [$comision, $inscripcion]) }}" 
                           class="flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium px-3 py-2 rounded hover:bg-blue-50 transition">
                            Ver Detalle
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

<!-- Modal: Pasar Asistencia -->
<div id="modalAsistencia" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-800">Pasar Asistencia</h3>
            <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Opción: Hoy -->
        <button onclick="irHoy()"
            class="w-full flex items-center gap-4 p-4 border-2 border-green-200 hover:border-green-500 hover:bg-green-50 rounded-xl transition mb-3 text-left group">
            <div class="w-10 h-10 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Hoy</p>
                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::today()->isoFormat('dddd D [de] MMMM') }}</p>
            </div>
        </button>

        <!-- Opción: Fecha pasada -->
        <div class="p-4 border-2 border-gray-200 hover:border-indigo-300 rounded-xl transition">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Fecha pasada</p>
                    <p class="text-xs text-gray-500">Seleccioná la fecha de la clase</p>
                </div>
            </div>
            <div class="flex gap-2">
                <input type="date"
                       id="inputFechaPasada"
                       max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                       class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button onclick="irFechaPasada()"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    Ir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAlumnos() {
    const list = document.getElementById('alumnosList');
    const chevron = document.getElementById('alumnosChevron');
    list.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
}

let urlBaseModal = '';

function abrirModal(urlBase, materiaNombre) {
    urlBaseModal = urlBase;
    document.getElementById('modalAsistencia').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modalAsistencia').classList.add('hidden');
}

function irHoy() {
    window.location.href = urlBaseModal;
}

function irFechaPasada() {
    const fecha = document.getElementById('inputFechaPasada').value;
    if (!fecha) {
        alert('Por favor seleccioná una fecha.');
        return;
    }
    window.location.href = urlBaseModal + '?fecha=' + fecha;
}

document.getElementById('modalAsistencia').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
@endsection