@extends('layouts.app')
@section('title', 'Gestión de Materias')
@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Materias</h1>
            <p class="text-gray-600 mt-1">Administra las materias del curso de ingreso</p>
        </div>
        @if(auth()->user()->hasPermission('comisiones.crear'))
        <a href="{{ route('materias.create') }}" class="bg-green-700 hover:bg-green-800 text-white font-semibold px-5 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Materia
        </a>
        @endif
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('materias.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Codigo o nombre de materia..."
                           class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                </div>
            </div>
            <div class="w-36">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach(\App\Models\Materia::getTipos() as $key => $label)
                        <option value="{{ $key }}" {{ request('tipo') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="activa" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todos</option>
                    <option value="1" {{ request('activa') == '1' ? 'selected' : '' }}>Activas</option>
                    <option value="0" {{ request('activa') == '0' ? 'selected' : '' }}>Inactivas</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">
                Filtrar
            </button>
            @if(request()->hasAny(['search', 'tipo', 'activa']))
                <a href="{{ route('materias.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Materia</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Carga Horaria</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Comisiones</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($materias as $materia)
                <tr class="hover:bg-gray-50 transition-colors cursor-pointer group" onclick="window.location='{{ route('materias.show', $materia) }}'">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg {{ $materia->activa ? 'bg-utn-blue/10' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 {{ $materia->activa ? 'text-utn-blue-dark' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">{{ $materia->nombre }}</p>
                                <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-mono bg-gray-100 text-gray-600">{{ $materia->codigo }}</span>
                                    @if($materia->es_nivelacion)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">Nivelacion</span>
                                    @endif
                                    @if($materia->descripcion)
                                    <span class="text-xs text-gray-400 truncate max-w-[200px]">{{ Str::limit($materia->descripcion, 40) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-center">
                        @php
                            $tipoColors = [
                                'obligatoria' => 'bg-utn-blue/10 text-utn-blue-dark',
                                'nivelacion' => 'bg-purple-100 text-purple-700',
                                'optativa' => 'bg-teal-100 text-teal-700',
                            ];
                            $tColor = $tipoColors[$materia->tipo] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tColor }}">
                            {{ \App\Models\Materia::getTipos()[$materia->tipo] ?? ucfirst($materia->tipo) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($materia->carga_horaria)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-utn-blue/10 text-utn-blue-dark">
                            {{ $materia->carga_horaria }} hs
                        </span>
                        @else
                        <span class="text-sm text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $materia->comisiones->count() > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $materia->comisiones->count() }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $materia->activa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $materia->activa ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-right" onclick="event.stopPropagation()">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('materias.show', $materia) }}" class="text-utn-blue-dark hover:text-utn-dark" title="Ver detalles">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @if(auth()->user()->hasPermission('comisiones.editar'))
                            <a href="{{ route('materias.edit', $materia) }}" class="text-yellow-600 hover:text-yellow-800" title="Editar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @endif
                            @if(auth()->user()->hasPermission('comisiones.eliminar'))
                            @if($materia->comisiones->count() === 0)
                            <form action="{{ route('materias.destroy', $materia) }}" method="POST" class="inline"
                                  data-confirm="Eliminar materia {{ $materia->nombre }}?" data-confirm-type="danger" data-confirm-title="Eliminar materia" data-confirm-text="Eliminar">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Eliminar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="mt-3 text-gray-500">No se encontraron materias con los filtros seleccionados.</p>
                        @if(auth()->user()->hasPermission('comisiones.crear'))
                        <div class="mt-4">
                            <a href="{{ route('materias.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-green-700 hover:bg-green-800">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Nueva Materia
                            </a>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($materias->hasPages())
    <div class="mt-6">
        {{ $materias->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
