@extends('layouts.app')
@section('title', 'Resumen por Comision')
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
            <h1 class="text-3xl font-bold text-gray-800">Resumen por Comision</h1>
            <p class="text-gray-600 mt-1">Vista comparativa de todas las comisiones activas</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-teal-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Comisiones activas</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $comisiones->count() }}</p>
                </div>
                <div class="bg-teal-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total alumnos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $comisiones->sum('alumnos') }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Asistencia promedio</p>
                    @php
                        $avgAsist = $comisiones->whereNotNull('pct_asistencia')->avg('pct_asistencia');
                    @endphp
                    <p class="text-2xl font-bold text-gray-800">{{ $avgAsist ? number_format($avgAsist, 1) . '%' : '-' }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Aprobacion promedio</p>
                    @php
                        $avgAprob = $comisiones->whereNotNull('pct_aprobacion')->avg('pct_aprobacion');
                    @endphp
                    <p class="text-2xl font-bold text-gray-800">{{ $avgAprob ? number_format($avgAprob, 1) . '%' : '-' }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('reportes.resumen-comisiones') }}" class="flex flex-wrap gap-4 items-end">
            <div class="w-36">
                <label class="block text-sm font-medium text-gray-700 mb-1">Anio</label>
                <select name="anio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach($anios as $anio)
                        <option value="{{ $anio }}" {{ request('anio') == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                <select name="periodo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach($periodos as $periodo)
                        <option value="{{ $periodo }}" {{ request('periodo') == $periodo ? 'selected' : '' }}>{{ ucfirst($periodo) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">Filtrar</button>
            @if(request()->hasAny(['anio', 'periodo']))
            <a href="{{ route('reportes.resumen-comisiones') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Limpiar</a>
            @endif
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Comision</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Turno</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Modalidad</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Alumnos</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Ocupacion</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Asistencia</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aprobacion</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($comisiones as $comision)
                <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('comisiones.show', $comision->id) }}'">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-teal-600">{{ strtoupper(substr($comision->nombre, 0, 2)) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $comision->nombre }}</p>
                                <span class="text-xs text-gray-400">Cupo: {{ $comision->cupo_maximo ?? '-' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ ucfirst($comision->turno ?? '-') }}</span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="text-sm text-gray-600">{{ ucfirst($comision->modalidad ?? '-') }}</span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="text-sm font-semibold text-gray-800">{{ $comision->alumnos }}</span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($comision->pct_ocupacion !== null)
                            @php
                                $colorOcup = $comision->pct_ocupacion >= 90 ? 'bg-red-100 text-red-700' : ($comision->pct_ocupacion >= 70 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700');
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorOcup }}">{{ $comision->pct_ocupacion }}%</span>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($comision->pct_asistencia !== null)
                            @php
                                $colorAsist = $comision->pct_asistencia >= 75 ? 'bg-green-100 text-green-700' : ($comision->pct_asistencia >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorAsist }}">{{ $comision->pct_asistencia }}%</span>
                        @else
                            <span class="text-xs text-gray-400">Sin datos</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($comision->pct_aprobacion !== null)
                            @php
                                $colorAprob = $comision->pct_aprobacion >= 70 ? 'bg-green-100 text-green-700' : ($comision->pct_aprobacion >= 40 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorAprob }}">{{ $comision->pct_aprobacion }}%</span>
                        @else
                            <span class="text-xs text-gray-400">Sin datos</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="mt-3 text-gray-500">No se encontraron comisiones activas con los criterios seleccionados.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
