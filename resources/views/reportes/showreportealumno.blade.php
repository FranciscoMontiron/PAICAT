@extends('layouts.app')
@section('title', 'Ficha del Alumno')
@section('content')

<div class="container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('reportes.reportealumnos') }}" class="text-utn-blue-dark hover:text-utn-blue-dark flex items-center gap-1 text-sm mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver al listado de alumnos
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Ficha del Alumno</h1>
            <p class="text-gray-600 mt-1">{{ $persona->apellido }}, {{ $persona->nombre }} - DNI: {{ $persona->documento }}</p>
        </div>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div id="alert-success" class="relative bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-green-600 hover:text-green-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div id="alert-error" class="relative bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-600 hover:text-red-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    <!-- Datos del alumno -->
    <div class="bg-white shadow-md rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Datos del Alumno</h3>
        @if($persona)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Nombre completo</span>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $persona->apellido }}, {{ $persona->nombre }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">DNI</span>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $persona->documento }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Comision</span>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $persona->comision_nombre }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Anio ingreso</span>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $persona->anio_ingreso }}</p>
            </div>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <p>No se encontraron datos del alumno en el sistema.</p>
        </div>
        @endif
    </div>

    <!-- Tabs -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <div class="border-b border-gray-200 px-6">
            <nav class="flex gap-6">
                <button id="tab-asistencias" class="tab-btn border-b-2 border-utn-blue text-utn-blue-dark font-semibold px-1 py-3 text-sm">
                    Asistencias
                </button>
                <button id="tab-evaluaciones" class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 px-1 py-3 text-sm">
                    Evaluaciones
                </button>
            </nav>
        </div>

        <!-- Tab: Asistencias -->
        <div id="content-asistencias" class="p-6">

            <!-- Filtro por materia -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <form action="{{ route('reportes.reportealumnosdetalle', $persona->inscripcion_id) }}" method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Materia</label>
                        <select name="materia_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                            <option value="">Todas</option>
                            @foreach($materias as $materia)
                                <option value="{{ $materia->id }}" {{ request('materia_id') == $materia->id ? 'selected' : '' }}>{{ $materia->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">Filtrar</button>
                    @if(request()->filled('materia_id'))
                    <a href="{{ route('reportes.reportealumnosdetalle', $persona->inscripcion_id) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Limpiar</a>
                    @endif
                </form>
            </div>

            <!-- Tabla de asistencias -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Materia</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($asistencias as $asistencia)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $asistencia->materia ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($asistencia->estado === 'presente') bg-green-100 text-green-700
                                    @elseif($asistencia->estado === 'ausente') bg-red-100 text-red-700
                                    @elseif($asistencia->estado === 'justificado') bg-yellow-100 text-yellow-700
                                    @elseif($asistencia->estado === 'tardanza') bg-orange-100 text-orange-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($asistencia->estado) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center text-gray-500">No hay registros de asistencia</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($asistencias->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                    {{ $asistencias->links() }}
                </div>
                @endif
            </div>

            <!-- Resumen por materia -->
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700">Resumen de asistencia por materia</h4>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Materia</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">Asist.</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-red-600 uppercase tracking-wider">Aus.</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-orange-600 uppercase tracking-wider">Tard.</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Just.</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">% Asist.</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($resumenPorMateria as $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row->materia }}</td>
                            <td class="px-4 py-3 text-sm text-center font-semibold text-green-600">{{ $row->asistencias }}</td>
                            <td class="px-4 py-3 text-sm text-center font-semibold text-red-600">{{ $row->ausencias }}</td>
                            <td class="px-4 py-3 text-sm text-center font-semibold text-orange-600">{{ $row->tardanzas }}</td>
                            <td class="px-4 py-3 text-sm text-center font-semibold text-blue-600">{{ $row->justificados }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $row->porcentaje_asistencia }}%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $row->estado === 'En riesgo' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $row->estado }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-500">No hay datos de asistencia</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div><!-- Fin Tab Asistencias -->

        <!-- Tab: Evaluaciones -->
        <div id="content-evaluaciones" class="hidden p-6">

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Materia</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Evaluacion</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($evaluaciones as $evaluacion)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::parse($evaluacion->fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $evaluacion->materia_nombre }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ $evaluacion->evaluacion_nombre }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 mt-1">{{ ucfirst($evaluacion->tipo) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($evaluacion->nota !== null)
                                    @php $colorNota = $evaluacion->nota >= 4 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $colorNota }}">{{ $evaluacion->nota }}</span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-gray-500">No hay registros de evaluaciones</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($evaluaciones->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                    {{ $evaluaciones->links() }}
                </div>
                @endif
            </div>

        </div><!-- Fin Tab Evaluaciones -->

    </div><!-- Fin Tabs Container -->

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs = {
        asistencias: {
            btn: document.getElementById('tab-asistencias'),
            content: document.getElementById('content-asistencias')
        },
        evaluaciones: {
            btn: document.getElementById('tab-evaluaciones'),
            content: document.getElementById('content-evaluaciones')
        }
    };

    function activar(tab) {
        Object.values(tabs).forEach(t => {
            t.content.classList.add('hidden');
            t.btn.classList.remove('border-utn-blue', 'text-utn-blue-dark', 'font-semibold');
            t.btn.classList.add('border-transparent', 'text-gray-500');
        });
        tabs[tab].content.classList.remove('hidden');
        tabs[tab].btn.classList.remove('border-transparent', 'text-gray-500');
        tabs[tab].btn.classList.add('border-utn-blue', 'text-utn-blue-dark', 'font-semibold');
    }

    tabs.asistencias.btn.addEventListener('click', () => activar('asistencias'));
    tabs.evaluaciones.btn.addEventListener('click', () => activar('evaluaciones'));
});
</script>

@endsection
