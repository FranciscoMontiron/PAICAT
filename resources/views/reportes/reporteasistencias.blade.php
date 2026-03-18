@extends('layouts.app')
@section('title', 'Reporte de asistencia')
@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-utn-blue-dark">Módulo de Reportes de asistencias por comision</h1>
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

    <div class="raw mb-6">
        <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="toggleDetalle" class="sr-only peer">

                <!-- Fondo -->
                <div
                    class="w-28 h-7 rounded-full bg-utn-blue/50
                        peer-checked:bg-orange-400
                        transition-colors duration-300
                        flex items-center justify-between px-2 text-[10px] font-semibold text-white"
                >
                    <span>General</span>
                    <span>Detalle</span>
                </div>

                <!-- Botón -->
                <div
                    class="absolute left-0.5 top-0.5 w-[3.3rem] h-6 bg-white rounded-full
                        transition-transform duration-300
                        peer-checked:translate-x-[3.6rem]"
                ></div>
        </label>
    </div>
    
    <div class="p-6 bg-white border-b border-gray-200">
        <div id="vistaGeneral">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-10 ">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-3xl font-bold text-utn-blue-dark">Reportes de asistencias generales</h1>
                    </div>
                </div>
                <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                    
                    <form method="GET" action="{{ route('reportes.reporteasistenciascomision') }}" class="p-6">
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

                                {{-- Estado --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Estado
                                    </label>
                                    <select name="estado"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg
                                                focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
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
                            <a href="{{ route('reportes.reporteasistenciascomision') }}"
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

            <!--Vista para detalle-->
            <div id="vistaDetalle" class="hidden">   
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-10 ">
                        <div class="flex items-center justify-between mb-6">
                            <h1 class="text-3xl font-bold text-utn-blue-dark">Reportes de asistencias Detallados</h1>
                        </div>
                    </div>
                        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                            <form method="GET" action="{{ route('reportes.reporteasistenciascomision') }}" class="p-6">
                                <input type="hidden" name="vista" value="detalle">
                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                                        Filtros del Reporte
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    
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

                                        <!---->
    
                                                                                       

                                    </div>
                     
                                </div>

                                {{-- Acciones --}}
                                    <div class="mt-8 flex justify-end gap-4">
                                      <a href="{{ route('reportes.reporteasistenciascomision', ['vista' => 'detalle']) }}"
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
                </div>
            </div>
            <!--Seccion para mostrar info-->
            <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                <!--Graficos-->
                    @php
                        $labels = $asistenciasPorFecha->pluck('fecha');
                        $presentes = $asistenciasPorFecha->pluck('presentes');
                        $ausentes = $asistenciasPorFecha->pluck('ausentes');
                        $tardanzas = $asistenciasPorFecha->pluck('tardanzas');
                        $justificados = $asistenciasPorFecha->pluck('justificados');
                    @endphp
                    <div class="bg-white rounded-lg shadow p-6 mb-8">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">
                            Asistencias por fecha
                        </h2>
                            <div class="bg-white p-4 rounded shadow">
                                <div class="overflow-x-auto">
                                    <div class="h-[400px]">
                                        <canvas id="chartDetalle"></canvas>
                                    </div>
                                </div>
                            </div>
                        
                    </div>
                <!--Fin Graficos-->


            <!--Comienza la tabla para el detalle de las personas y comisiones-->
                <div id="reporte-asistencias-alumno"></div>
                <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                Asistencias
                            </h3>
                            <div class="bg-white rounded-lg shadow overflow-hidden">
                                <div class="overflow-x-auto">
                                    <div class="mb-4">
                                        <input
                                            type="text"
                                            id="buscador-asistencias"
                                            placeholder="Buscar por alumno, comisión, materia, estado o fecha..."
                                            class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-lg
                                                focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                                        >
                                    </div>

                                    <table id="tabla-asistencias" class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="border px-3 py-2 text-left">Fecha</th>
                                                <th class="border px-3 py-2 text-left">Comision</th>
                                                <th class="border px-3 py-2 text-left">Materia</th>
                                                <th class="border px-3 py-2 text-left">Turno</th>
                                                <th class="border px-3 py-2 text-left">Alumno</th>
                                                <th class="border px-3 py-2 text-left">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($asistenciadetalle as $asist)
                                            <tr class="hover:bg-gray-50">
                                                
                                                 <td class="px-6 py-4">
                                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($asist->fecha)->format('d/m/Y') }}</div>
                                                </td>
                                                <td class="px-6 py-4">
                                                     <div class="text-sm text-gray-900">{{$asist->comision_nombre}}</div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm text-gray-900">{{$asist->materia_nombre}} </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                     <div class="text-sm text-gray-900">{{$asist->turno}}</div>
                                                </td>
                                                <td class="px-6 py-4">
                                                     <div class="text-sm text-gray-900">{{$asist->apellido}}, {{$asist->nombre}} </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                     <div class="text-sm text-gray-900">{{$asist->estado}}</div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Paginación -->
                                <div class="px-6 py-4 border-t border-gray-200">
                                    {{ $asistenciadetalle->appends(array_merge(request()->except('page_detalle'),['page_alumnos' => request('page_alumnos')]))->fragment('reporte-asistencias-alumno')->links() }}

                                </div>
                            </div>

                            
                        </div>
                    </div>

                <!--Finaliza la tabla para el detalle de las personas y comisiones-->
            </div>
        <!--Comienza la tabla de estados del alumno por materia-->     

            <div class="mb-4">
                <input
                    type="text"
                    id="buscador-alumnos"
                    placeholder="Buscar alumno por nombre, apellido, comisión o materia..."
                    class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-lg
                        focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                >
            </div>
            <div id="tabla-alumnos"></div>
            <table id="tabla-alumnos-table" class="min-w-full text-sm border border-gray-300 mt-8">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2">Alumno</th>
                        <th class="border px-3 py-2">Especialidad</th>
                        <th class="border px-3 py-2">Comisión</th>
                        <th class="border px-3 py-2">Materia</th>
                        <th class="border px-3 py-2 text-green-600">Asist.</th>
                        <th class="border px-3 py-2 text-red-600">Faltas</th>
                        <th class="border px-3 py-2 text-yellow-600">Tard.</th>
                        <th class="border px-3 py-2 text-utn-blue-dark">Justif.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asistenciasPorAlumno as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2">
                                {{ $row->apellido }}, {{ $row->nombre }}
                            </td>
                            <td class="border px-3 py-2">{{ $row->especialidad }}</td>
                            <td class="border px-3 py-2">{{ $row->comision }}</td>
                            <td class="border px-3 py-2">{{ $row->materia }}</td>

                            <td class="border px-3 py-2 text-center text-green-600 font-semibold">
                                {{ $row->asistencias }}
                            </td>
                            <td class="border px-3 py-2 text-center text-red-600 font-semibold">
                                {{ $row->faltas }}
                            </td>
                            <td class="border px-3 py-2 text-center text-yellow-600 font-semibold">
                                {{ $row->tardanzas }}
                            </td>
                            <td class="border px-3 py-2 text-center text-utn-blue-dark font-semibold">
                                {{ $row->justificados }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">
                                No hay datos para los filtros seleccionados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                
                {{ $asistenciasPorAlumno->appends(array_merge(request()->except('page_alumnos'),['page_detalle' => request('page_detalle')]))->fragment('tabla-alumnos')->links()}}
            </div>

        <!--Finaliza la tabla de estados del alumno-->


        </div><!--Fin de div para vistaDetalle-->


        



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


<!--Scrip de toggle para mostrar detalle o general-->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('toggleDetalle');
    const general = document.getElementById('vistaGeneral');
    const detalle = document.getElementById('vistaDetalle');

    if (!toggle || !general || !detalle) return;

    const params = new URLSearchParams(window.location.search);
    const vista = params.get('vista');

    const esDetalle = vista === 'detalle';

    toggle.checked = esDetalle;
    general.classList.toggle('hidden', esDetalle);
    detalle.classList.toggle('hidden', !esDetalle);

    toggle.addEventListener('change', () => {
        const nuevaVista = toggle.checked ? 'detalle' : 'general';

        general.classList.toggle('hidden', toggle.checked);
        detalle.classList.toggle('hidden', !toggle.checked);

   
        const url = new URL(window.location);
        url.searchParams.set('vista', nuevaVista);
        window.history.replaceState({}, '', url);
    });
});
</script>


<!--Grafico para detalle-->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const labels = @json($labels);
    const presentes = @json($presentes);
    const ausentes = @json($ausentes);
    const tardanzas = @json($tardanzas);
    const justificados = @json($justificados);

    const canvas = document.getElementById('chartDetalle');
    if (!canvas) return;

    // ancho dinámico (scroll horizontal)
    const barWidth = 50;
    if (labels.length > 10) {
        canvas.style.width = (labels.length * barWidth) + 'px';
    }

    const ctx = canvas.getContext('2d');

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
                    if (chart.isDatasetVisible(i)) {
                        total += dataset.data[index] ?? 0;
                    }
                });

                if (total === 0) return;

                const x = meta0.data[index].x;

                // buscamos el punto más alto de la pila
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

    new Chart(ctx, {
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
        scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true }
        }
    }
,
        plugins: [totalPorFechaPlugin]
    });

});
</script>



<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('buscador-alumnos');
    const filas = document.querySelectorAll('#tabla-alumnos-table tbody tr');

    input.addEventListener('keyup', () => {
        const texto = input.value.toLowerCase();

        filas.forEach(fila => {
            const contenido = fila.innerText.toLowerCase();
            fila.style.display = contenido.includes(texto) ? '' : 'none';
        });
    });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('buscador-asistencias');
    const filas = document.querySelectorAll('#tabla-asistencias tbody tr');

    if (!input) return;

    input.addEventListener('keyup', () => {
        const texto = input.value.toLowerCase();

        filas.forEach(fila => {
            const contenido = fila.innerText.toLowerCase();
            fila.style.display = contenido.includes(texto) ? '' : 'none';
        });
    });
});
</script>


@endsection
