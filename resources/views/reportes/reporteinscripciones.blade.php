@extends('layouts.app')
@section('title', 'Reporte de inscripciones')
@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-utn-blue-dark">
            Módulo de Reportes de inscripciones
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

    <div class="p-6 bg-white border-b border-gray-200">

        {{-- Filtros --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <form method="GET" action="{{ route('reportes.reporteinscriones') }}" class="p-6">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        Filtros del Reporte
                    </h3>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    {{-- Anio ingreso --}}
                       <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Año de Ingreso
                            </label>
                            <select name="anio_ingreso"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                                <option value="">Todos los años</option>
                                
                                @foreach($anio_ingreso as $anio)
                                    <option value="{{ $anio->anio_ingreso }}"
                                        {{ request('anio_ingreso') == $anio->anio_ingreso ? 'selected' : '' }}>
                                        {{ $anio->anio_ingreso }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                </div>

                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('reportes.reporteinscriones') }}"
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

        {{-- ================= MODALIDAD ================= --}}
        <div class="bg-white rounded-lg shadow p-6 mb-10">

            <h2 class="text-lg font-semibold mb-4 text-center">
                Alumnos por modalidad
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">

                {{-- GRÁFICO --}}
                <div>
                    <div id="totalModalidad"
                        class="text-center text-xl font-bold text-gray-800 mb-3">
                        Total: 0
                    </div>

                    <div class="relative h-80">
                        <canvas id="chartModalidad"></canvas>
                    </div>
                </div>

                {{-- TABLA CARRERA x MODALIDAD --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">
                                    Carrera
                                </th>
                                @foreach($modalidades as $mod)
                                    <th class="border px-3 py-2 text-center">
                                        {{ $mod }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($resumenCarreraModalidad as $carrera => $items)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-3 py-2 font-medium">
                                        {{ $carrera }}
                                    </td>

                                    @foreach($modalidades as $mod)
                                        @php
                                            $valor = $items
                                                ->firstWhere('modalidad', $mod)
                                                ->total ?? 0;
                                        @endphp
                                        <td class="border px-3 py-2 text-center">
                                            {{ $valor }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        {{-- ================= ESPECIALIDAD ================= --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">

            <h2 class="text-lg font-semibold mb-4 text-center">
                Alumnos por especialidad
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">

                {{-- TORTA ESPECIALIDAD --}}
                <div>
                    <div id="totalEspecialidad"
                        class="text-center text-xl font-bold text-gray-800 mb-3">
                        Total: 0
                    </div>

                    <div class="relative h-72">
                        <canvas id="chartEspecialidad"></canvas>
                    </div>
                </div>

                {{--TABLA RANGO ETARIO --}}
                    <div class="overflow-x-auto">
                        <h3 class="text-md font-semibold text-center mb-2">
                            Cantidad de alumnos por rango etario
                        </h3>
                        <h3 class="text-md font-semibold text-center mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                    "Otros" son los registros que estan debajo del rango minimo o que no tienen edad cargada.
                            </label>
                        </h3>

                        <table class="min-w-full text-sm border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2 text-left">
                                        Especialidad
                                    </th>
                                    <th class="border px-3 py-2 text-center">
                                        17–22
                                    </th>
                                    <th class="border px-3 py-2 text-center">
                                        23–27
                                    </th>
                                    <th class="border px-3 py-2 text-center">
                                        28–32
                                    </th>
                                    <th class="border px-3 py-2 text-center">
                                        +32
                                    </th>
                                    <th class="border px-3 py-2 text-center">
                                        Otros
                                    </th>
                                    <th class="border px-3 py-2 text-center font-semibold">
                                        Total
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($edadPorEspecialidadRango as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-3 py-2 font-medium">
                                            {{ $row->especialidad }}
                                        </td>
                                        <td class="border px-3 py-2 text-center">
                                            {{ $row->r17_22 }}
                                        </td>
                                        <td class="border px-3 py-2 text-center">
                                            {{ $row->r23_27 }}
                                        </td>
                                        <td class="border px-3 py-2 text-center">
                                            {{ $row->r28_32 }}
                                        </td>
                                        <td class="border px-3 py-2 text-center">
                                            {{ $row->r32_mas }}
                                        </td>
                                        <td class="border px-3 py-2 text-center">{{ $row->otros }}</td>
                                        <td class="border px-3 py-2 text-center font-semibold">
                                            {{ $row->r17_22 + $row->r23_27 + $row->r28_32 + $row->r32_mas + $row->otros }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>

        {{-- TABLA POR ESPECIALIDAD Y GÉNERO --}}
        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-left">
                            Especialidad
                        </th>

                        @foreach($generos as $gen)
                            <th class="border px-3 py-2 text-center">
                                {{ $gen }}
                            </th>
                        @endforeach

                        <th class="border px-3 py-2 text-center font-semibold">
                            Total
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($resumenEspecialidadGenero as $especialidad => $items)
                        @php $totalFila = 0; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2 font-medium">
                                {{ $especialidad }}
                            </td>

                            @foreach($generos as $gen)
                                @php
                                    $valor = $items
                                        ->firstWhere('genero', $gen)
                                        ->total ?? 0;
                                    $totalFila += $valor;
                                @endphp
                                <td class="border px-3 py-2 text-center">
                                    {{ $valor }}
                                </td>
                            @endforeach

                            <td class="border px-3 py-2 text-center font-semibold">
                                {{ $totalFila }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    function crearGraficoTorta(canvasId, labels, values, totalElementId, colores) {

        const canvas = document.getElementById(canvasId);
        const totalEl = document.getElementById(totalElementId);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        function actualizarTotal(chart) {
            let total = 0;
            chart.data.datasets[0].data.forEach((v, i) => {
                if (chart.getDataVisibility(i)) {
                    total += v;
                }
            });
            totalEl.textContent = 'Total: ' + total;
        }

        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colores
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        align: 'center',
                        labels: {
                            boxWidth: 14,
                            padding: 12
                        },
                        onClick(e, legendItem, legend) {
                            const index = legendItem.index;
                            legend.chart.toggleDataVisibility(index);
                            legend.chart.update();
                            actualizarTotal(legend.chart);
                        }
                        },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                let totalVisible = 0;
                                context.dataset.data.forEach((v, i) => {
                                    if (context.chart.getDataVisibility(i)) {
                                        totalVisible += v;
                                    }
                                });

                                const value = context.raw;
                                const pct = totalVisible
                                    ? ((value / totalVisible) * 100).toFixed(1)
                                    : 0;

                                return `${context.label} ${value} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });

        actualizarTotal(chart);
    }

    /* MODALIDAD */
    crearGraficoTorta(
        'chartModalidad',
        @json($labelsModalidad),
        @json($totalesModalidad),
        'totalModalidad',
        ['#3b82f6','#22c55e','#f59e0b','#ef4444','#8b5cf6']
    );

    /* ESPECIALIDAD */
    crearGraficoTorta(
        'chartEspecialidad',
        @json($labelsEspecialidad),
        @json($totalesEspecialidad),
        'totalEspecialidad',
        ['#0ea5e9','#10b981','#f97316','#ef4444','#a855f7','#14b8a6']
    );

});
</script>




@endsection
