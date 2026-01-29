@extends('layouts.app')

@section('title', 'Cursadas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Cursadas</h1>
        <p class="text-gray-600 mt-1">Gestión global de cursadas de todos los alumnos</p>
    </div>

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500">
            <p class="text-sm text-gray-600">Total</p>
            <p class="text-2xl font-bold text-gray-800">{{ $estadisticas['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600">Cursando</p>
            <p class="text-2xl font-bold text-blue-600">{{ $estadisticas['cursando'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600">Aprobados</p>
            <p class="text-2xl font-bold text-green-600">{{ $estadisticas['aprobados'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-600">Desaprobados</p>
            <p class="text-2xl font-bold text-red-600">{{ $estadisticas['desaprobados'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600">Libres</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $estadisticas['libres'] }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form action="{{ route('cursadas.global') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar alumno</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Nombre, apellido o documento..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Comisión</label>
                <select name="comision_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                    <option value="">Todas</option>
                    @foreach($comisiones as $comision)
                        <option value="{{ $comision->id }}" {{ request('comision_id') == $comision->id ? 'selected' : '' }}>
                            {{ $comision->codigo }} - {{ $comision->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach(\App\Models\Cursada::ESTADOS as $key => $label)
                        <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                <select name="anio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                    <option value="">Todos</option>
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('anio') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                Filtrar
            </button>
            @if(request()->hasAny(['buscar', 'comision_id', 'estado', 'anio']))
                <a href="{{ route('cursadas.global') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    {{-- Tabla de cursadas --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        @if($cursadas->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <p class="text-lg font-medium">No hay cursadas registradas</p>
                <p class="text-sm mt-1">Las cursadas se crean automáticamente al inscribir alumnos a comisiones.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comisión</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nota Final</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recursante</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cursadas as $cursada)
                            @php
                                $estudiante = $cursada->getEstudiante();
                                $estadoColors = [
                                    'cursando' => 'bg-blue-100 text-blue-800',
                                    'aprobado' => 'bg-green-100 text-green-800',
                                    'desaprobado' => 'bg-red-100 text-red-800',
                                    'libre' => 'bg-yellow-100 text-yellow-800',
                                    'abandono' => 'bg-gray-100 text-gray-800',
                                    'baja' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">
                                        {{ $estudiante ? $estudiante->apellido . ', ' . $estudiante->nombre : 'Sin datos' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $estudiante?->documento ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('comisiones.show', $cursada->comision) }}" class="text-utn-blue hover:underline">
                                        {{ $cursada->comision->codigo ?? 'N/A' }}
                                    </a>
                                    <div class="text-sm text-gray-500">{{ $cursada->comision->nombre ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $estadoColors[$cursada->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ \App\Models\Cursada::ESTADOS[$cursada->estado] ?? $cursada->estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $cursada->anio }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($cursada->nota_final !== null)
                                        <span class="font-medium {{ $cursada->nota_final >= 6 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($cursada->nota_final, 2) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($cursada->es_recursante)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                            Sí
                                        </span>
                                    @else
                                        <span class="text-gray-400">No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('cursadas.show', [$cursada->comision, $cursada]) }}"
                                           class="text-utn-blue hover:text-blue-800" title="Ver detalle">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('cursadas.edit', [$cursada->comision, $cursada]) }}"
                                           class="text-gray-600 hover:text-gray-800" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($cursadas->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $cursadas->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
