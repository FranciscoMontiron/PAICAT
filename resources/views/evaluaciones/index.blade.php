@extends('layouts.app')
@section('title', 'Evaluaciones')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Evaluaciones</h1>
        <p class="text-gray-600 mt-1">Seleccion&aacute; una comisi&oacute;n para gestionar evaluaciones y calificaciones</p>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <p class="text-sm text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form action="{{ route('evaluaciones.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           placeholder="Nombre o c&oacute;digo de comisi&oacute;n..."
                           class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                </div>
            </div>
            <div class="w-28">
                <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                <select name="anio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach($aniosDisponibles as $anio)
                        <option value="{{ $anio }}" {{ request('anio', date('Y')) == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                <select name="periodo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach(\App\Models\Comision::getTiposIngreso() as $clave => $label)
                        <option value="{{ $label }}" {{ request('periodo') == $label ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">
                Filtrar
            </button>
            @if(request()->hasAny(['buscar', 'anio', 'periodo']))
                <a href="{{ route('evaluaciones.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    {{-- Tabla de Comisiones --}}
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Comisi&oacute;n</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Periodo</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Alumnos</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Materias</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Evaluaciones</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($comisiones as $comision)
                    @php
                        $evaluacionesCount = $comision->evaluaciones->count();
                        $materiasCount = $comision->materias->count();
                        $alumnosCount = $comision->inscripciones()->whereIn('estado', ['inscripto', 'confirmado'])->count();
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer group" onclick="window.location='{{ route('evaluaciones.comision', $comision) }}'">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-utn-blue/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">{{ $comision->nombre }}</p>
                                    <p class="text-xs text-gray-500">{{ $comision->codigo ?? '' }} {{ $comision->turno ? '&bull; ' . ucfirst($comision->turno) : '' }} {{ $comision->modalidad ? '&bull; ' . $comision->modalidad : '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-utn-blue/10 text-utn-blue-dark">
                                {{ $comision->periodo }} {{ $comision->anio }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-sm font-semibold text-gray-700">{{ $alumnosCount }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-sm font-semibold text-gray-700">{{ $materiasCount }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-sm font-semibold {{ $evaluacionesCount > 0 ? 'text-green-600' : 'text-gray-400' }}">{{ $evaluacionesCount }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-utn-blue-dark opacity-0 group-hover:opacity-100 transition-opacity">
                                Abrir
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <p class="mt-3 text-gray-500">No se encontraron comisiones con los filtros seleccionados.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($comisiones->hasPages())
    <div class="mt-6">
        {{ $comisiones->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
