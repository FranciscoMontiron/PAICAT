@extends('layouts.app')
@section('title', 'Reporte de inscripciones')
@section('content')

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('reportes.index') }}" class="text-utn-blue-dark hover:text-utn-blue-dark flex items-center gap-1 text-sm mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver a reportes
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Inscripciones</h1>
            <p class="text-gray-600 mt-1">Distribucion por modalidad, especialidad, genero y rangos etarios</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('reportes.reporteinscriones') }}" class="flex flex-wrap gap-4 items-end">
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Anio de Ingreso</label>
                <select name="anio_ingreso" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todos los anios</option>
                    @foreach($anio_ingreso as $anio)
                        <option value="{{ $anio->anio_ingreso }}" {{ request('anio_ingreso') == $anio->anio_ingreso ? 'selected' : '' }}>{{ $anio->anio_ingreso }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">Filtrar</button>
            @if(request()->hasAny(['anio_ingreso']))
            <a href="{{ route('reportes.reporteinscriones') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Limpiar</a>
            @endif
        </form>
    </div>

    <!-- Modalidad -->
    <div class="bg-white shadow-md rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Alumnos por Modalidad</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
            <!-- Grafico -->
            <div>
                <div id="totalModalidad" class="text-center text-xl font-bold text-gray-800 mb-3">Total: 0</div>
                <div class="relative h-80">
                    <canvas id="chartModalidad"></canvas>
                </div>
            </div>
            <!-- Tabla Carrera x Modalidad -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Carrera</th>
                            @foreach($modalidades as $mod)
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $mod }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($resumenCarreraModalidad as $carrera => $items)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $carrera }}</td>
                            @foreach($modalidades as $mod)
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $items->firstWhere('modalidad', $mod)->total ?? 0 }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Especialidad + Rango Etario -->
    <div class="bg-white shadow-md rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Alumnos por Especialidad</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
            <!-- Grafico -->
            <div>
                <div id="totalEspecialidad" class="text-center text-xl font-bold text-gray-800 mb-3">Total: 0</div>
                <div class="relative h-72">
                    <canvas id="chartEspecialidad"></canvas>
                </div>
            </div>
            <!-- Tabla Rango Etario -->
            <div class="overflow-x-auto">
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Cantidad por rango etario</h4>
                <p class="text-xs text-gray-400 mb-3">"Otros" son registros debajo del rango minimo o sin edad cargada.</p>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Especialidad</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">17-22</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">23-27</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">28-32</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">+32</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Otros</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($edadPorEspecialidadRango as $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row->especialidad }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $row->r17_22 }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $row->r23_27 }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $row->r28_32 }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $row->r32_mas }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $row->otros }}</td>
                            <td class="px-4 py-3 text-sm text-center font-semibold text-gray-800">{{ $row->r17_22 + $row->r23_27 + $row->r28_32 + $row->r32_mas + $row->otros }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabla Especialidad x Genero -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Alumnos por Especialidad y Genero</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Especialidad</th>
                    @foreach($generos as $gen)
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $gen }}</th>
                    @endforeach
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($resumenEspecialidadGenero as $especialidad => $items)
                    @php $totalFila = 0; @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $especialidad }}</td>
                        @foreach($generos as $gen)
                            @php
                                $valor = $items->firstWhere('genero', $gen)->total ?? 0;
                                $totalFila += $valor;
                            @endphp
                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $valor }}</td>
                        @endforeach
                        <td class="px-4 py-3 text-sm text-center font-semibold text-gray-800">{{ $totalFila }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function crearGraficoTorta(canvasId, labels, values, totalElementId, colores) {
        const canvas = document.getElementById(canvasId);
        const totalEl = document.getElementById(totalElementId);
        if (!canvas) return;

        function actualizarTotal(chart) {
            let total = 0;
            chart.data.datasets[0].data.forEach((v, i) => {
                if (chart.getDataVisibility(i)) total += v;
            });
            totalEl.textContent = 'Total: ' + total;
        }

        const chart = new Chart(canvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{ data: values, backgroundColor: colores }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        align: 'center',
                        labels: { boxWidth: 14, padding: 12 },
                        onClick(e, legendItem, legend) {
                            legend.chart.toggleDataVisibility(legendItem.index);
                            legend.chart.update();
                            actualizarTotal(legend.chart);
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                let totalVisible = 0;
                                context.dataset.data.forEach((v, i) => {
                                    if (context.chart.getDataVisibility(i)) totalVisible += v;
                                });
                                const pct = totalVisible ? ((context.raw / totalVisible) * 100).toFixed(1) : 0;
                                return `${context.label} ${context.raw} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
        actualizarTotal(chart);
    }

    crearGraficoTorta('chartModalidad', @json($labelsModalidad), @json($totalesModalidad), 'totalModalidad',
        ['#3b82f6','#22c55e','#f59e0b','#ef4444','#8b5cf6']);

    crearGraficoTorta('chartEspecialidad', @json($labelsEspecialidad), @json($totalesEspecialidad), 'totalEspecialidad',
        ['#0ea5e9','#10b981','#f97316','#ef4444','#a855f7','#14b8a6']);
});
</script>

@endsection
