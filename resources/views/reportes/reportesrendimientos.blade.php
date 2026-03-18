@extends('layouts.app')
@section('title', 'Reporte de inscripciones')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-utn-blue-dark">
            Módulo de Reportes de Rendimientos
        </h1>
    </div>

     <div class="mb-6 flex justify-end">
        <a href="{{ route('reportes.index') }}"
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
    </div>
    {{-- Filtros --}}

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                    
                <form method="GET" action="{{ route('reportes.reporterendimiento') }}" class="p-6">
                    <input type="hidden" name="vista" value="general">
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                            Filtros del Reporte 
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            {{-- Especialidad --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Especialidad
                                    </label>
                                    <select name="especialidad_id"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg
                                                focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                                        <option value="">Todas</option>
                                        @foreach($especialidades as $esp)
                                            <option value="{{ $esp->id_sysacad }}"
                                                {{ request('especialidad_id') == $esp->id_sysacad ? 'selected' : '' }}>
                                                {{ $esp->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

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

                            {{-- Comisión --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Comisión
                                </label>
                                <select name="comision_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg
                                            focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                                    <option value="">Todas</option>
                                    @foreach($comisiones as $comision)
                                        <option value="{{ $comision->id }}"
                                            {{ request('comision_id') == $comision->id ? 'selected' : '' }}>
                                            {{ $comision->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>  
                                                
                            {{-- Desde --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Desde
                                </label>
                                <input type="date" name="fecha_desde"
                                    value="{{ request('fecha_desde') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg
                                            focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                            </div>

                            {{-- Hasta --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Hasta
                                </label>
                                <input type="date" name="fecha_hasta"
                                    value="{{ request('fecha_hasta') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg
                                            focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                            </div>

                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="mt-8 flex justify-end gap-4">
                        <a href="{{ route('reportes.reporterendimiento') }}"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700
                                hover:bg-gray-50 transition-colors duration-200">
                            Limpiar
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-utn-blue text-white rounded-lg
                                    hover:bg-utn-dark transition-colors duration-200">
                            Filtrar
                        </button>
                    </div>
                </form>
        </div>

    {{-- Fin Filtros --}}


    <div class="p-6 bg-white border-b border-gray-200">
        
        {{-- GRAFICOs --}}

            <div class="relative h-96">
                <canvas id="chartMateriaStack"></canvas>
            </div>
       
        {{-- FIN GRAFICOS --}}

        {{-- Tabla --}}
        <div class="mt-8 bg-white rounded-lg shadow p-6 overflow-x-auto">
            <h2 class="text-lg font-semibold mb-4 text-center">
                Rendimiento por Comisión y Parcial
            </h2>

            <table class="min-w-full text-sm border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2">Comisión</th>
                        <th class="border px-3 py-2">Materia</th>
                        <th class="border px-3 py-2">Parcial</th>
                        <th class="border px-3 py-2">Tipo</th>
                        <th class="border px-3 py-2">Fecha</th>
                        <th class="border px-3 py-2 text-center text-green-600">Aprobados</th>
                        <th class="border px-3 py-2 text-center text-red-600">Desaprobados</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($tablaRendimiento as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2">{{ $row->comision }}</td>
                            <td class="border px-3 py-2">{{ $row->materia }}</td>
                            <td class="border px-3 py-2">{{ $row->parcial }}</td>
                            <td class="border px-3 py-2 text-center">{{ $row->tipo }}</td>
                            <td class="border px-3 py-2 text-center">
                                {{ \Carbon\Carbon::parse($row->fecha)->format('d/m/Y') }}
                            </td>
                            <td class="border px-3 py-2 text-center font-semibold text-green-600">
                                {{ $row->aprobados }}
                            </td>
                            <td class="border px-3 py-2 text-center font-semibold text-red-600">
                                {{ $row->desaprobados }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">
                                No hay datos para los filtros seleccionados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

       
        {{-- FIN GRAFICOS --}}


    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('chartMateriaStack').getContext('2d');

    const dataAprobados = @json($dataAprobados).map(v => Number(v));
const dataDesaprobados = @json($dataDesaprobados).map(v => Number(v));


    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labelsMaterias),
            datasets: [
                {
                    label: 'Aprobados',
                    data: dataAprobados,
                    backgroundColor: '#22c55e'
                },
                {
                    label: 'Desaprobados',
                    data: dataDesaprobados,
                    backgroundColor: '#ef4444'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: true },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad de alumnos'
                    }
                }
            },
            plugins: {
                legend: { position: 'top' },


                datalabels: {
                    color: '#111',
                    anchor: 'end',
                    align: 'top',
                    font: {
                        weight: 'bold'
                    },
                    formatter: (value, context) => {
                        const i = context.dataIndex;
                        const total =
                            dataAprobados[i] + dataDesaprobados[i];

                        // Mostrar solo una vez (en la última pila)
                        return context.datasetIndex === 1 ? total : '';
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
});
</script>



@endsection
