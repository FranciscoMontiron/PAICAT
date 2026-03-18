@extends('layouts.app')
@section('title', 'Reportes')
@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

<div class="mb-6 flex justify-end">
        <a href="{{ route('reportes.reportealumnos') }}"
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
    </div>
    {{-- Mensajes --}}
       @if(session('success'))
        <div id="alert-success" class="relative bg-green-50 border-l-4 border-green-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>

                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>

            <!-- Botón cerrar -->
            <button
                type="button"
                onclick="document.getElementById('alert-success').remove()"
                class="absolute top-2 right-2 text-green-600 hover:text-green-800"
            >
                ✕
            </button>
        </div>
        @endif


        @if(session('error'))
            <div id="alert-error" class="relative bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>

                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>

                <!-- Botón cerrar -->
                <button
                    type="button"
                    onclick="document.getElementById('alert-error').remove()"
                    class="absolute top-2 right-2 text-red-600 hover:text-red-800"
                >
                    ✕
                </button>
            </div>
        @endif


    <div class="p-6 bg-white border-b border-gray-200">

        {{-- Datos del alumno --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Datos del Alumno</h2>

                @if($persona)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">Nombre completo</span>
                        <p class="font-medium">{{ $persona->apellido }}, {{ $persona->nombre }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">DNI</span>
                        <p class="font-medium">{{ $persona->documento }}</p>
                    </div>

                    <div>
                        <span class="text-sm text-gray-500">Comision</span>
                        <p class="font-medium">{{ $persona->comision_nombre }}</p>
                    </div>

                    <div>
                        <span class="text-sm text-gray-500">Año ingreso</span>
                        <p class="font-medium">{{ $persona->anio_ingreso }}</p>
                    </div>
                    
                </div>

                @else
                <div class="text-center py-8 text-gray-500">
                    <p>No se encontraron datos del alumno en el sistema de alumnos.</p>
                </div>
                @endif
            </div>
    </div>


    <div class="p-6 bg-white border-b border-gray-200">
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex gap-6">
                <button
                    id="tab-asistencias"
                    class="tab-btn border-b-2 border-utn-blue text-utn-blue-dark font-semibold px-1 pb-2"
                >
                    Asistencias
                </button>

                <button
                    id="tab-evaluaciones"
                    class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 px-1 pb-2"
                >
                    Evaluaciones
                </button>
            </nav>
        </div>

        {{-- Asistencias --}}
        <div id="content-asistencias">
            <h2 class="text-xl font-semibold mb-4">Asistencias</h2>

                {{-- Filtros --}}
                <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                    <form href="{{ route('reportes.reportealumnosdetalle', $persona->inscripcion_id) }}" method="GET"  class="p-6">
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                                Filtros del Reporte
                            </h3>
                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                {{-- Materia --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Materia
                                        </label>
                                        <select name="materia_id"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg
                                                    focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                                            <option value="">Todas</option>
                                            @foreach($materias as $materia)
                                                <option value="{{ $materia->id }}"
                                                    {{ request('materia_id') == $materia->id ? 'selected' : '' }}>
                                                    {{ $materia->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>  

                        </div>

                        <div class="mt-8 flex justify-end gap-4">
                            <a href="{{ route('reportes.reportealumnosdetalle', $persona->inscripcion_id) }}"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Limpiar
                            </a>

                            <button type="submit"
                                    class="px-6 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark">
                                Filtrar
                            </button>
                        </div>
                    </form>
                </div>


                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">

                            {{-- CABECERA --}}
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Fecha
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Materia
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        Estado
                                    </th>
                                </tr>
                            </thead>

                            {{-- CUERPO --}}
                            <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($asistencias as $asistencia)
                                <tr class="hover:bg-gray-50">

                                    {{-- Fecha --}}
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}
                                    </td>

                                    {{-- Materia --}}
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $asistencia->materia ?? '-' }}
                                    </td>

                                    {{-- Estado --}}
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            @if($asistencia->estado === 'presente') bg-green-100 text-green-800
                                            @elseif($asistencia->estado === 'ausente') bg-red-100 text-red-800
                                            @elseif($asistencia->estado === 'justificado') bg-yellow-100 text-yellow-800
                                            @elseif($asistencia->estado === 'tardanza') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($asistencia->estado) }}
                                        </span>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-10 text-center text-gray-500">
                                        No hay registros
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                        {{-- PAGINADOR --}}
                        @if($asistencias->hasPages())
                        <div class="px-4 py-3 border-t bg-gray-50">
                            {{ $asistencias->links() }}
                        </div>
                        @endif
                    </div>
                </div>{{-- Fin de la primer tabla--}}

        {{-- Tabla para el promedio de asistencias--}}
            <div class="mt-10 bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Resumen de asistencia por materia
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                                    <th class="px-4 py-2 text-center text-green-600">Asist.</th>
                                    <th class="px-4 py-2 text-center text-red-600">Aus.</th>
                                    <th class="px-4 py-2 text-center text-orange-600">Tard.</th>
                                    <th class="px-4 py-2 text-center text-utn-blue-dark">Just.</th>
                                    <th class="px-4 py-2 text-center">Estado</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($resumenPorMateria as $row)

                                <tr>
                                    <td class="px-3 py-2">{{ $row->materia }}</td>
                                    <td class="px-3 py-2 text-center">{{ $row->asistencias }}</td>
                                    <td class="px-3 py-2 text-center">{{ $row->ausencias }}</td>
                                    <td class="px-3 py-2 text-center">{{ $row->tardanzas }}</td>
                                    <td class="px-3 py-2 text-center">{{ $row->justificados }}</td>
                                    <td class="px-3 py-2 text-center">{{ $row->porcentaje_asistencia }}%</td>

                                    <td class="px-3 py-2 font-semibold
                                        {{ $row->estado === 'En riesgo' ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $row->estado }}
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                        No hay datos de asistencia
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>{{-- Fin de Tabla de promedio de asistencias --}}
            
        </div>{{-- Fin Asistencias --}}


        {{-- Evaluaciones --}}
        <div id="content-evaluaciones" class="hidden">
            <h2 class="text-xl font-semibold mb-4">Evaluaciones</h2>

            <div class="p-6 bg-white border-b border-gray-200">
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200">

                        {{-- CABECERA --}}
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Evaluación</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nota</th>
                            </tr>
                        </thead>

                        {{-- CUERPO --}}
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($evaluaciones as $evaluacion)
                            <tr class="hover:bg-gray-50">

                                {{-- Fecha --}}
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ \Carbon\Carbon::parse($evaluacion->fecha)->format('d/m/Y') }}
                                </td>

                                {{-- Materia --}}
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $evaluacion->materia_nombre }}
                                </td>

                                {{-- Evaluación --}}
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $evaluacion->evaluacion_nombre }}
                                    <div class="text-xs text-gray-400">
                                        {{ ucfirst($evaluacion->tipo) }}
                                    </div>
                                </td>

                                {{-- Nota --}}
                                <td class="px-4 py-3 text-center text-sm font-semibold text-gray-700">
                                    {{ $evaluacion->nota ?? '-' }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-gray-500">
                                    No hay registros
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{-- PAGINADOR --}}
                    @if($evaluaciones->hasPages())
                    <div class="px-4 py-3 border-t bg-gray-50">
                        {{ $evaluaciones->links() }}
                    </div>
                    @endif
                </div>
            </div>            
        </div>

    </div>

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
