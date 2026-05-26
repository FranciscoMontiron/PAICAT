@extends('layouts.app')
@section('title', 'Infraestructura')
@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Infraestructura</h1>
            <p class="text-gray-600 mt-1">Municipios y aulas del curso de ingreso</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Municipios</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['municipios_activos'] }}<span class="text-sm font-normal text-gray-400">/{{ $stats['municipios_total'] }}</span></p>
                </div>
                <div class="bg-utn-blue/10 p-3 rounded-full">
                    <svg class="w-6 h-6 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Aulas Activas</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['aulas_activas'] }}<span class="text-sm font-normal text-gray-400">/{{ $stats['aulas_total'] }}</span></p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Capacidad Total</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['capacidad_total'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Aulas sin Municipio</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $aulas->where('municipio_id', null)->count() }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex gap-0 -mb-px">
            <button onclick="switchTab('municipios')" id="tab-btn-municipios"
                class="tab-btn px-6 py-3 text-sm font-semibold border-b-2 transition-colors {{ $tab === 'municipios' ? 'border-utn-blue-dark text-utn-blue-dark' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Municipios ({{ $stats['municipios_total'] }})
            </button>
            <button onclick="switchTab('aulas')" id="tab-btn-aulas"
                class="tab-btn px-6 py-3 text-sm font-semibold border-b-2 transition-colors {{ $tab === 'aulas' ? 'border-utn-blue-dark text-utn-blue-dark' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Aulas ({{ $stats['aulas_total'] }})
            </button>
        </nav>
    </div>

    {{-- ============== TAB: MUNICIPIOS ============== --}}
    <div id="tab-municipios" class="{{ $tab !== 'municipios' ? 'hidden' : '' }}">
        <!-- Filtros + Nuevo -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-6">
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="filtro-municipio-buscar" placeholder="Nombre, codigo o direccion..."
                               value="{{ request('buscar_municipio') }}"
                               class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                               onkeyup="filtrarMunicipios()">
                    </div>
                </div>
                <div class="w-36">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="filtro-municipio-estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                            onchange="filtrarMunicipios()">
                        <option value="">Todos</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
                @if(auth()->user()->hasPermission('comisiones.crear'))
                <a href="{{ route('municipios.create') }}" class="bg-green-700 hover:bg-green-800 text-white font-semibold px-5 py-2 rounded-lg transition duration-200 flex items-center gap-2 text-sm shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Municipio
                </a>
                @endif
            </div>
        </div>

        <!-- Tabla Municipios -->
        <div class="bg-white shadow-md rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Municipio</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Direccion</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aulas</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Comisiones</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tbody-municipios">
                    @forelse($municipios as $municipio)
                    <tr class="fila-municipio hover:bg-gray-50 transition-colors cursor-pointer group"
                        data-nombre="{{ strtolower($municipio->nombre . ' ' . ($municipio->codigo ?? '') . ' ' . ($municipio->direccion ?? '')) }}"
                        data-activo="{{ $municipio->activo ? '1' : '0' }}"
                        onclick="window.location='{{ route('municipios.show', $municipio) }}'">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg {{ $municipio->activo ? 'bg-utn-blue/10' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 {{ $municipio->activo ? 'text-utn-blue-dark' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">{{ $municipio->nombre }}</p>
                                    @if($municipio->codigo)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-mono bg-gray-100 text-gray-600">{{ $municipio->codigo }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $municipio->direccion ?? '-' }}</td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $municipio->aulas_count > 0 ? 'bg-utn-blue/10 text-utn-blue-dark' : 'bg-gray-100 text-gray-500' }}">
                                {{ $municipio->aulas_count }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $municipio->comisiones_count > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $municipio->comisiones_count }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $municipio->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $municipio->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end gap-2">
                                @if(auth()->user()->hasPermission('comisiones.crear'))
                                <a href="{{ route('aulas.create', ['municipio_id' => $municipio->id]) }}" class="text-utn-blue-dark hover:text-utn-dark" title="Crear aula en este municipio">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v4m2-2h-4" transform="translate(4,-2) scale(0.6)"/>
                                    </svg>
                                </a>
                                @endif
                                <a href="{{ route('municipios.show', $municipio) }}" class="text-utn-blue-dark hover:text-utn-dark" title="Ver detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if(auth()->user()->hasPermission('comisiones.editar'))
                                <a href="{{ route('municipios.edit', $municipio) }}" class="text-yellow-600 hover:text-yellow-800" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('municipios.toggle-activo', $municipio) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="{{ $municipio->activo ? 'text-gray-500 hover:text-gray-700' : 'text-green-600 hover:text-green-800' }}" title="{{ $municipio->activo ? 'Desactivar' : 'Activar' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($municipio->activo)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            @endif
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @if(auth()->user()->hasPermission('comisiones.eliminar'))
                                @if($municipio->comisiones_count === 0 && $municipio->aulas_count === 0)
                                <form action="{{ route('municipios.destroy', $municipio) }}" method="POST" class="inline"
                                      data-confirm="Eliminar municipio {{ $municipio->nombre }}?" data-confirm-type="danger" data-confirm-title="Eliminar municipio" data-confirm-text="Eliminar">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="mt-3 text-gray-500">No hay municipios registrados.</p>
                            @if(auth()->user()->hasPermission('comisiones.crear'))
                            <div class="mt-4">
                                <a href="{{ route('municipios.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-green-700 hover:bg-green-800">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Nuevo Municipio
                                </a>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============== TAB: AULAS ============== --}}
    <div id="tab-aulas" class="{{ $tab !== 'aulas' ? 'hidden' : '' }}">
        <!-- Filtros + Nueva -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-6">
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="filtro-aula-buscar" placeholder="Nombre, codigo o ubicacion..."
                               value="{{ request('buscar_aula') }}"
                               class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                               onkeyup="filtrarAulas()">
                    </div>
                </div>
                <div class="w-44">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Municipio</label>
                    <select id="filtro-aula-municipio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                            onchange="filtrarAulas()">
                        <option value="">Todos</option>
                        @foreach($municipiosActivos as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="filtro-aula-estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                            onchange="filtrarAulas()">
                        <option value="">Todas</option>
                        <option value="1">Activas</option>
                        <option value="0">Inactivas</option>
                    </select>
                </div>
                @if(auth()->user()->hasPermission('comisiones.crear'))
                <a href="{{ route('aulas.create') }}" class="bg-green-700 hover:bg-green-800 text-white font-semibold px-5 py-2 rounded-lg transition duration-200 flex items-center gap-2 text-sm shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Aula
                </a>
                @endif
            </div>
        </div>

        <!-- Tabla Aulas -->
        <div class="bg-white shadow-md rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aula</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Municipio</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Capacidad</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Comisiones</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tbody-aulas">
                    @forelse($aulas as $aula)
                    <tr class="fila-aula hover:bg-gray-50 transition-colors cursor-pointer group"
                        data-nombre="{{ strtolower($aula->nombre . ' ' . ($aula->codigo ?? '') . ' ' . ($aula->ubicacion ?? '')) }}"
                        data-municipio="{{ $aula->municipio_id }}"
                        data-activa="{{ $aula->activa ? '1' : '0' }}"
                        onclick="window.location='{{ route('aulas.show', $aula) }}'">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg {{ $aula->activa ? 'bg-utn-blue/10' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 {{ $aula->activa ? 'text-utn-blue-dark' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 group-hover:text-utn-blue-dark transition-colors">{{ $aula->nombre }}</p>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                                        @if($aula->codigo)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-mono bg-gray-100 text-gray-600">{{ $aula->codigo }}</span>
                                        @endif
                                        @if($aula->ubicacion)
                                        <span class="text-xs text-gray-400">{{ $aula->ubicacion }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $aula->municipio?->nombre ?? '-' }}</td>
                        <td class="px-4 py-4 text-center">
                            @if($aula->capacidad)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-utn-blue/10 text-utn-blue-dark">
                                {{ $aula->capacidad }}
                            </span>
                            @else
                            <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $aula->comisiones_count > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $aula->comisiones_count }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $aula->activa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $aula->activa ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('aulas.show', $aula) }}" class="text-utn-blue-dark hover:text-utn-dark" title="Ver detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if(auth()->user()->hasPermission('comisiones.editar'))
                                <a href="{{ route('aulas.edit', $aula) }}" class="text-yellow-600 hover:text-yellow-800" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('aulas.toggle-activa', $aula) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="{{ $aula->activa ? 'text-gray-500 hover:text-gray-700' : 'text-green-600 hover:text-green-800' }}" title="{{ $aula->activa ? 'Desactivar' : 'Activar' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($aula->activa)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            @endif
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @if(auth()->user()->hasPermission('comisiones.eliminar'))
                                @if($aula->comisiones_count === 0)
                                <form action="{{ route('aulas.destroy', $aula) }}" method="POST" class="inline"
                                      data-confirm="Eliminar aula {{ $aula->nombre }}?" data-confirm-type="danger" data-confirm-title="Eliminar aula" data-confirm-text="Eliminar">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p class="mt-3 text-gray-500">No hay aulas registradas.</p>
                            @if(auth()->user()->hasPermission('comisiones.crear'))
                            <div class="mt-4">
                                <a href="{{ route('aulas.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-green-700 hover:bg-green-800">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Nueva Aula
                                </a>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('tab-municipios').classList.toggle('hidden', tab !== 'municipios');
    document.getElementById('tab-aulas').classList.toggle('hidden', tab !== 'aulas');

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-utn-blue-dark', 'text-utn-blue-dark');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    var activeBtn = document.getElementById('tab-btn-' + tab);
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('border-utn-blue-dark', 'text-utn-blue-dark');

    var url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.replaceState({}, '', url);
}

function filtrarMunicipios() {
    var buscar = document.getElementById('filtro-municipio-buscar').value.toLowerCase();
    var estado = document.getElementById('filtro-municipio-estado').value;

    document.querySelectorAll('.fila-municipio').forEach(function(fila) {
        var nombre = fila.dataset.nombre;
        var activo = fila.dataset.activo;
        var matchBuscar = !buscar || nombre.includes(buscar);
        var matchEstado = !estado || activo === estado;
        fila.style.display = (matchBuscar && matchEstado) ? '' : 'none';
    });
}

function filtrarAulas() {
    var buscar = document.getElementById('filtro-aula-buscar').value.toLowerCase();
    var municipio = document.getElementById('filtro-aula-municipio').value;
    var estado = document.getElementById('filtro-aula-estado').value;

    document.querySelectorAll('.fila-aula').forEach(function(fila) {
        var nombre = fila.dataset.nombre;
        var muni = fila.dataset.municipio;
        var activa = fila.dataset.activa;
        var matchBuscar = !buscar || nombre.includes(buscar);
        var matchMunicipio = !municipio || muni === municipio;
        var matchEstado = !estado || activa === estado;
        fila.style.display = (matchBuscar && matchMunicipio && matchEstado) ? '' : 'none';
    });
}
</script>
@endsection
