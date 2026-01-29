@extends('layouts.app')

@section('title', 'Aulas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Aulas</h1>
            <p class="text-gray-600 mt-1">Gestiona los espacios físicos disponibles por sede</p>
        </div>
        @if(auth()->user()->hasPermission('comisiones.crear'))
        <a href="{{ route('aulas.create') }}"
           class="bg-utn-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nueva Aula
        </a>
        @endif
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form action="{{ route('aulas.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="buscar" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}"
                       placeholder="Nombre, código o ubicación..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
            </div>
            <div class="w-48">
                <label for="municipio_id" class="block text-sm font-medium text-gray-700 mb-1">Municipio</label>
                <select name="municipio_id" id="municipio_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach($municipios as $municipio)
                        <option value="{{ $municipio->id }}" {{ request('municipio_id') == $municipio->id ? 'selected' : '' }}>
                            {{ $municipio->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label for="activa" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="activa" id="activa" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                    <option value="">Todos</option>
                    <option value="1" {{ request('activa') === '1' ? 'selected' : '' }}>Activas</option>
                    <option value="0" {{ request('activa') === '0' ? 'selected' : '' }}>Inactivas</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-utn-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                <a href="{{ route('aulas.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aula</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Municipio</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Capacidad</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-28">Comisiones</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Estado</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-32">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($aulas as $aula)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $aula->nombre }}</div>
                                @if($aula->codigo)
                                <div class="text-xs text-gray-500">Código: {{ $aula->codigo }}</div>
                                @endif
                                @if($aula->ubicacion)
                                <div class="text-xs text-gray-400">{{ $aula->ubicacion }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $aula->municipio->nombre ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($aula->capacidad)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $aula->capacidad }}
                        </span>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $aula->comisiones_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $aula->activa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $aula->activa ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-1">
                            <a href="{{ route('aulas.show', $aula) }}"
                               class="p-1.5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors"
                               title="Ver detalle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @if(auth()->user()->hasPermission('comisiones.editar'))
                            <a href="{{ route('aulas.edit', $aula) }}"
                               class="p-1.5 rounded bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors"
                               title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('aulas.toggle-activa', $aula) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="p-1.5 rounded {{ $aula->activa ? 'bg-yellow-100 text-yellow-600 hover:bg-yellow-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }} transition-colors"
                                        title="{{ $aula->activa ? 'Desactivar' : 'Activar' }}">
                                    @if($aula->activa)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @endif
                                </button>
                            </form>
                            @endif
                            @if(auth()->user()->hasPermission('comisiones.eliminar') && $aula->comisiones_count === 0)
                            <form action="{{ route('aulas.destroy', $aula) }}" method="POST" class="inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar esta aula?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                        title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <p class="mt-2">No hay aulas registradas</p>
                        @if(auth()->user()->hasPermission('comisiones.crear'))
                        <a href="{{ route('aulas.create') }}" class="mt-2 inline-block text-utn-blue hover:text-blue-800 text-sm">
                            Crear primera aula
                        </a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        @if($aulas->hasPages())
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
            {{ $aulas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
