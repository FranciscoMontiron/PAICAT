@extends('layouts.app')
@section('title', 'Reportes')
@section('content')

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-utn-blue">Módulo de Reportes de asistencias por comision</h1>
            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">Módulo 5 - Reportes de asistencias</span>
        </div>
     

       <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <form method="GET" action="{{ route('reportes.reporteasistenciascomision') }}" class="p-6">

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
                                            focus:ring-2 focus:ring-utn-blue focus:border-transparent">
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
                                        focus:ring-2 focus:ring-utn-blue focus:border-transparent">
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
                                        focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                                <option value="">Todas</option>
                                @foreach($comisiones as $comision)
                                    <option value="{{ $comision->id }}"
                                        {{ request('comision_id') == $comision->id ? 'selected' : '' }}>
                                        {{ $comision->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Estado
                            </label>
                            <select name="estado"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg
                                        focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                                <option value="">Todos</option>
                                <option value="presente" {{ request('estado') == 'presente' ? 'selected' : '' }}>
                                    Presente
                                </option>
                                <option value="ausente" {{ request('estado') == 'ausente' ? 'selected' : '' }}>
                                    Ausente
                                </option>
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
                                        focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                        </div>

                        {{-- Hasta --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Hasta
                            </label>
                            <input type="date" name="fecha_hasta"
                                value="{{ request('fecha_hasta') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg
                                        focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                        </div>

                    </div>
                </div>

                {{-- Acciones --}}
                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('reportes.reporteasistenciascomision') }}"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700
                            hover:bg-gray-50 transition-colors duration-200">
                        Limpiar
                    </a>

                    <button type="submit"
                            class="px-6 py-2 bg-utn-blue text-white rounded-lg
                                hover:bg-blue-800 transition-colors duration-200">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
        <p id="totalRegistros" class="text-sm text-gray-600 mb-2">
            Total registros: {{ array_sum($totales->toArray()) }}
        </p>

                   {{-- Gráfico de Asistencias --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Resumen de Asistencias
                </h3>
                <div class="flex justify-center">
                    <div class="w-80 h-80"> {{-- 256x256 --}}
                        <canvas id="asistenciasChart" height="120"></canvas>
                    </div>
                </div>


                
            </div>
        </div>


        <!--Tabla donde mostramos las cantidades por carrera-->

        @php
            $estados = ['presente', 'ausente', 'justificado', 'tardanza'];

            $totalesColumnas = array_fill_keys($estados, 0);
        @endphp
            <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Asistencias por Especialidad
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2 text-left">Especialidad</th>
                                    <th class="border px-3 py-2 text-center">Presente</th>
                                    <th class="border px-3 py-2 text-center">Ausente</th>
                                    <th class="border px-3 py-2 text-center">Justificado</th>
                                    <th class="border px-3 py-2 text-center">Tardanza</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($especialidadesEstados as $especialidad => $registros)
                                    @php
                                        $porEstado = $registros->keyBy('estado');
                                    @endphp

                                    <tr>
                                        <td class="border px-3 py-2">{{ $especialidad }}</td>

                                        @foreach ($estados as $estado)
                                            @php
                                                $valor = $porEstado[$estado]->total ?? 0;
                                                $totalesColumnas[$estado] += $valor;
                                            @endphp

                                            <td class="border px-3 py-2 text-center">
                                                {{ $valor }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>

                            {{-- FILA TOTAL --}}
                            <tfoot class="bg-gray-200 font-semibold">
                                <tr>
                                    <td class="border px-3 py-2 text-right">TOTAL</td>

                                    @foreach ($estados as $estado)
                                        <td class="border px-3 py-2 text-center">
                                            {{ $totalesColumnas[$estado] }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>




        <!--Finaliza tabla donde se muestran las cantidades por carrera-->

    </div>
</div>

<!--Script de grafico-->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const dataEstados = [
        { label: 'Presentes', value: {{ $totales['presente'] ?? 0 }}, color: '#22c55e' },
        { label: 'Ausentes', value: {{ $totales['ausente'] ?? 0 }}, color: '#ef4444' },
        { label: 'Justificados', value: {{ $totales['justificado'] ?? 0 }}, color: '#3b82f6' },
        { label: 'Tardanza', value: {{ $totales['tardanza'] ?? 0 }}, color: '#f97316' }
    ];

    const canvas = document.getElementById('asistenciasChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    const totalElement = document.getElementById('totalRegistros');

    const chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: dataEstados.map(e => e.label),
            datasets: [{
                data: dataEstados.map(e => e.value),
                backgroundColor: dataEstados.map(e => e.color)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    onClick: function (e, legendItem, legend) {
                        const index = legendItem.index;
                        const chart = legend.chart;

                        chart.toggleDataVisibility(index);
                        chart.update();

                        let totalVisible = 0;
                        chart.data.datasets[0].data.forEach((val, i) => {
                            if (chart.getDataVisibility(i)) {
                                totalVisible += val;
                            }
                        });

                        if (totalElement) {
                            totalElement.innerText = `Total registros: ${totalVisible}`;
                        }
                    },
                    labels: {
                        color: '#000',
                        generateLabels: function (chart) {

                            const data = chart.data.datasets[0].data;

                            let total = 0;
                            data.forEach((val, i) => {
                                if (chart.getDataVisibility(i)) {
                                    total += val;
                                }
                            });

                            return chart.data.labels.map((label, i) => {
                                const value = data[i];
                                const visible = chart.getDataVisibility(i);
                                const porcentaje = total > 0
                                    ? ((value / total) * 100).toFixed(1)
                                    : 0;

                                return {
                                    text: value > 0
                                        ? `${label}: ${value} (${porcentaje}%)`
                                        : `${label}: 0`,
                                    fillStyle: chart.data.datasets[0].backgroundColor[i],
                                    strokeStyle: chart.data.datasets[0].backgroundColor[i],
                                    hidden: !visible,
                                    index: i
                                };
                            });
                        }
                    }
                }
            }
        }
    });

});
</script>


@endsection
