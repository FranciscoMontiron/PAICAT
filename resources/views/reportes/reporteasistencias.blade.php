@extends('layouts.app')
@section('title', 'Reporte de asistencia')
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
            <h1 class="text-3xl font-bold text-gray-800">Asistencias por Comision</h1>
            <p class="text-gray-600 mt-1">Analisis de asistencia por comision, especialidad y materia</p>
        </div>
    </div>

    <!-- Toggle General/Detalle -->
    <div class="mb-6">
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" id="toggleDetalle" class="sr-only peer">
            <div class="w-28 h-7 rounded-full bg-utn-blue/50 peer-checked:bg-orange-400 transition-colors duration-300 flex items-center justify-between px-2 text-[10px] font-semibold text-white">
                <span>General</span>
                <span>Detalle</span>
            </div>
            <div class="absolute left-0.5 top-0.5 w-[3.3rem] h-6 bg-white rounded-full transition-transform duration-300 peer-checked:translate-x-[3.6rem]"></div>
        </label>
    </div>

    <!-- ==================== VISTA GENERAL ==================== -->
    <div id="vistaGeneral">

        <!-- Filtros General -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('reportes.reporteasistenciascomision') }}" class="flex flex-wrap gap-4 items-end">
                <input type="hidden" name="vista" value="general">
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                    <select name="especialidad_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                        <option value="">Todas</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp->id_sysacad }}" {{ request('especialidad_id') == $esp->id_sysacad ? 'selected' : '' }}>{{ $esp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="presente" {{ request('estado') == 'presente' ? 'selected' : '' }}>Presente</option>
                        <option value="ausente" {{ request('estado') == 'ausente' ? 'selected' : '' }}>Ausente</option>
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
                <a href="{{ route('reportes.reporteasistenciascomision') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Limpiar</a>
            </form>
        </div>

        <p id="totalRegistros" class="text-sm text-gray-600 mb-4">Total registros: {{ array_sum($totales->toArray()) }}</p>

        <!-- Grafico de torta -->
        <div class="bg-white shadow-md rounded-xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Resumen de Asistencias</h3>
            <div class="flex justify-center">
                <div class="w-80 h-80">
                    <canvas id="asistenciasChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabla Asistencias por Especialidad -->
        @php
            $estados = ['presente', 'ausente', 'justificado', 'tardanza'];
            $totalesColumnas = array_fill_keys($estados, 0);
        @endphp
        <div class="bg-white shadow-md rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Asistencias por Especialidad</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Especialidad</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">Presente</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-red-600 uppercase tracking-wider">Ausente</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Justificado</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-orange-600 uppercase tracking-wider">Tardanza</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($especialidadesEstados as $especialidad => $registros)
                        @php $porEstado = $registros->keyBy('estado'); @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $especialidad }}</td>
                            @foreach ($estados as $estado)
                                @php
                                    $valor = $porEstado[$estado]->total ?? 0;
                                    $totalesColumnas[$estado] += $valor;
                                @endphp
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $valor }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800 text-right">TOTAL</td>
                        @foreach ($estados as $estado)
                            <td class="px-4 py-3 text-sm text-center font-semibold text-gray-800">{{ $totalesColumnas[$estado] }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    <!-- ==================== VISTA DETALLE ==================== -->
    <div id="vistaDetalle" class="hidden">

        <!-- Filtros Detalle -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('reportes.reporteasistenciascomision') }}" class="flex flex-wrap gap-4 items-end">
                <input type="hidden" name="vista" value="detalle">
                <div class="w-36">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                </div>
                <div class="w-36">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                    <select name="especialidad_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                        <option value="">Todas</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp->id_sysacad }}" {{ request('especialidad_id') == $esp->id_sysacad ? 'selected' : '' }}>{{ $esp->nombre }}</option>
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
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Materia</label>
                    <select name="materia_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                        <option value="">Todas</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}" {{ request('materia_id') == $materia->id ? 'selected' : '' }}>{{ $materia->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">Filtrar</button>
                <a href="{{ route('reportes.reporteasistenciascomision', ['vista' => 'detalle']) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Limpiar</a>
            </form>
        </div>

        <!-- Grafico por fecha -->
        @php
            $labels = $asistenciasPorFecha->pluck('fecha');
            $presentes = $asistenciasPorFecha->pluck('presentes');
            $ausentes = $asistenciasPorFecha->pluck('ausentes');
            $tardanzas = $asistenciasPorFecha->pluck('tardanzas');
            $justificados = $asistenciasPorFecha->pluck('justificados');
        @endphp
        <div class="bg-white shadow-md rounded-xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Asistencias por fecha</h3>
            <div class="overflow-x-auto">
                <div class="h-[400px]">
                    <canvas id="chartDetalle"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabla detalle de asistencias -->
        <div id="reporte-asistencias-alumno"></div>
        <div class="bg-white shadow-md rounded-xl overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Asistencias</h3>
                <div class="w-72">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="buscador-asistencias" placeholder="Buscar..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    </div>
                </div>
            </div>
            <table id="tabla-asistencias" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Comision</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Materia</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Turno</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($asistenciadetalle as $asist)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::parse($asist->fecha)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $asist->comision_nombre }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $asist->materia_nombre }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $asist->turno }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $asist->apellido }}, {{ $asist->nombre }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                @if($asist->estado === 'presente') bg-green-100 text-green-700
                                @elseif($asist->estado === 'ausente') bg-red-100 text-red-700
                                @elseif($asist->estado === 'justificado') bg-blue-100 text-blue-700
                                @elseif($asist->estado === 'tardanza') bg-orange-100 text-orange-700
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ ucfirst($asist->estado) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $asistenciadetalle->appends(array_merge(request()->except('page_detalle'),['page_alumnos' => request('page_alumnos')]))->fragment('reporte-asistencias-alumno')->links() }}
            </div>
        </div>

        <!-- Tabla resumen por alumno -->
        <div id="tabla-alumnos"></div>
        <div class="bg-white shadow-md rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Resumen por Alumno</h3>
                <div class="w-72">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="buscador-alumnos" placeholder="Buscar alumno..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    </div>
                </div>
            </div>
            <table id="tabla-alumnos-table" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Especialidad</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Comision</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Materia</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">Asist.</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-red-600 uppercase tracking-wider">Faltas</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-orange-600 uppercase tracking-wider">Tard.</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Justif.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($asistenciasPorAlumno as $row)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row->apellido }}, {{ $row->nombre }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $row->especialidad }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $row->comision }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $row->materia }}</td>
                        <td class="px-4 py-3 text-sm text-center font-semibold text-green-600">{{ $row->asistencias }}</td>
                        <td class="px-4 py-3 text-sm text-center font-semibold text-red-600">{{ $row->faltas }}</td>
                        <td class="px-4 py-3 text-sm text-center font-semibold text-orange-600">{{ $row->tardanzas }}</td>
                        <td class="px-4 py-3 text-sm text-center font-semibold text-blue-600">{{ $row->justificados }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-3 text-gray-500">No hay datos para los filtros seleccionados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $asistenciasPorAlumno->appends(array_merge(request()->except('page_alumnos'),['page_detalle' => request('page_detalle')]))->fragment('tabla-alumnos')->links() }}
            </div>
        </div>

    </div><!-- Fin vistaDetalle -->

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Grafico de torta - General
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

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: dataEstados.map(e => e.label),
            datasets: [{ data: dataEstados.map(e => e.value), backgroundColor: dataEstados.map(e => e.color) }]
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
                            if (chart.getDataVisibility(i)) totalVisible += val;
                        });
                        if (totalElement) totalElement.innerText = `Total registros: ${totalVisible}`;
                    },
                    labels: {
                        color: '#000',
                        generateLabels: function (chart) {
                            const data = chart.data.datasets[0].data;
                            let total = 0;
                            data.forEach((val, i) => { if (chart.getDataVisibility(i)) total += val; });
                            return chart.data.labels.map((label, i) => {
                                const value = data[i];
                                const visible = chart.getDataVisibility(i);
                                const porcentaje = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return {
                                    text: value > 0 ? `${label}: ${value} (${porcentaje}%)` : `${label}: 0`,
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

<!-- Toggle General/Detalle -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('toggleDetalle');
    const general = document.getElementById('vistaGeneral');
    const detalle = document.getElementById('vistaDetalle');
    if (!toggle || !general || !detalle) return;

    const params = new URLSearchParams(window.location.search);
    const esDetalle = params.get('vista') === 'detalle';

    toggle.checked = esDetalle;
    general.classList.toggle('hidden', esDetalle);
    detalle.classList.toggle('hidden', !esDetalle);

    toggle.addEventListener('change', () => {
        general.classList.toggle('hidden', toggle.checked);
        detalle.classList.toggle('hidden', !toggle.checked);
        const url = new URL(window.location);
        url.searchParams.set('vista', toggle.checked ? 'detalle' : 'general');
        window.history.replaceState({}, '', url);
    });
});
</script>

<!-- Grafico detalle por fecha -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = @json($labels);
    const presentes = @json($presentes);
    const ausentes = @json($ausentes);
    const tardanzas = @json($tardanzas);
    const justificados = @json($justificados);

    const canvas = document.getElementById('chartDetalle');
    if (!canvas) return;

    if (labels.length > 10) canvas.style.width = (labels.length * 50) + 'px';

    const totalPorFechaPlugin = {
        id: 'totalPorFecha',
        afterDatasetsDraw(chart) {
            const { ctx, data } = chart;
            const meta0 = chart.getDatasetMeta(0);
            ctx.save();
            ctx.font = 'bold 12px sans-serif';
            ctx.fillStyle = '#111';
            ctx.textAlign = 'center';
            data.labels.forEach((_, index) => {
                let total = 0;
                chart.data.datasets.forEach((dataset, i) => {
                    if (chart.isDatasetVisible(i)) total += dataset.data[index] ?? 0;
                });
                if (total === 0) return;
                const x = meta0.data[index].x;
                let yTop = Infinity;
                chart.getSortedVisibleDatasetMetas().forEach(meta => {
                    const bar = meta.data[index];
                    if (bar) yTop = Math.min(yTop, bar.y);
                });
                ctx.fillText(total, x, yTop - 6);
            });
            ctx.restore();
        }
    };

    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label: 'Presentes', data: presentes, backgroundColor: '#22c55e' },
                { label: 'Ausentes', data: ausentes, backgroundColor: '#ef4444' },
                { label: 'Tardanzas', data: tardanzas, backgroundColor: '#f59e0b' },
                { label: 'Justificados', data: justificados, backgroundColor: '#3b82f6' }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
        },
        plugins: [totalPorFechaPlugin]
    });
});
</script>

<!-- Buscadores -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    function setupSearch(inputId, tableId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const filas = document.querySelectorAll(`#${tableId} tbody tr`);
        input.addEventListener('keyup', () => {
            const texto = input.value.toLowerCase();
            filas.forEach(fila => {
                fila.style.display = fila.innerText.toLowerCase().includes(texto) ? '' : 'none';
            });
        });
    }
    setupSearch('buscador-alumnos', 'tabla-alumnos-table');
    setupSearch('buscador-asistencias', 'tabla-asistencias');
});
</script>

@endsection
