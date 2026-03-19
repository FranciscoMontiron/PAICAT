@extends('layouts.app')
@section('title', 'Reporte de Rendimiento')
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
            <h1 class="text-3xl font-bold text-gray-800">Rendimiento Academico</h1>
            <p class="text-gray-600 mt-1">Aprobados y desaprobados por materia y comision</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('reportes.reporterendimiento') }}" class="flex flex-wrap gap-4 items-end">
            <input type="hidden" name="vista" value="general">
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">Materia</label>
                <select name="materia_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todas</option>
                    @foreach($materias as $materia)
                        <option value="{{ $materia->id }}" {{ request('materia_id') == $materia->id ? 'selected' : '' }}>{{ $materia->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">Comision</label>
                <select name="comision_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todas</option>
                    @foreach($comisiones as $comision)
                        <option value="{{ $comision->id }}" {{ request('comision_id') == $comision->id ? 'selected' : '' }}>{{ $comision->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
            </div>
            <div class="w-36">
                <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">Filtrar</button>
            <a href="{{ route('reportes.reporterendimiento') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Limpiar</a>
        </form>
    </div>

    <!-- Grafico -->
    <div class="bg-white shadow-md rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aprobados vs Desaprobados por Materia</h3>
        <div class="relative h-96">
            <canvas id="chartMateriaStack"></canvas>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Rendimiento por Comision y Parcial</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Comision</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Materia</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Parcial</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">Aprobados</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-red-600 uppercase tracking-wider">Desaprobados</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tablaRendimiento as $row)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $row->comision }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $row->materia }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row->parcial }}</td>
                    <td class="px-4 py-3 text-sm text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ ucfirst($row->tipo) }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-center text-gray-600">{{ \Carbon\Carbon::parse($row->fecha)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $row->aprobados }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">{{ $row->desaprobados }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="mt-3 text-gray-500">No hay datos para los filtros seleccionados</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
                { label: 'Aprobados', data: dataAprobados, backgroundColor: '#22c55e' },
                { label: 'Desaprobados', data: dataDesaprobados, backgroundColor: '#ef4444' }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Cantidad de alumnos' } }
            },
            plugins: {
                legend: { position: 'top' },
                datalabels: {
                    color: '#111',
                    anchor: 'end',
                    align: 'top',
                    font: { weight: 'bold' },
                    formatter: (value, context) => {
                        const i = context.dataIndex;
                        return context.datasetIndex === 1 ? dataAprobados[i] + dataDesaprobados[i] : '';
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
});
</script>

@endsection
