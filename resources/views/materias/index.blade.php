@extends('layouts.app')
@section('title', 'Gestión de Materias')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Materias</h1>
            <p class="text-gray-600 mt-1">Administra las materias del curso de ingreso</p>
        </div>
        @if(auth()->user()->hasPermission('comisiones.crear'))
        <a href="{{ route('materias.create') }}" class="inline-flex items-center px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nueva Materia
        </a>
        @endif
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Total Materias</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Activas</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['activas'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Nivelación</p>
            <p class="text-2xl font-bold text-utn-blue-dark">{{ $stats['nivelacion'] }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Código o nombre..."
                    class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="tipo" class="rounded-lg border-gray-300">
                    <option value="">Todos</option>
                    @foreach(\App\Models\Materia::getTipos() as $key => $label)
                        <option value="{{ $key }}" {{ request('tipo') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="activa" class="rounded-lg border-gray-300">
                    <option value="">Todos</option>
                    <option value="1" {{ request('activa') == '1' ? 'selected' : '' }}>Activas</option>
                    <option value="0" {{ request('activa') == '0' ? 'selected' : '' }}>Inactivas</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue-darker text-white rounded-lg hover:bg-utn-dark-light">Filtrar</button>
            <a href="{{ route('materias.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Limpiar</a>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carga Horaria</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comisiones</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($materias as $materia)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-sm font-medium text-gray-900">{{ $materia->codigo }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $materia->nombre }}</div>
                        @if($materia->descripcion)
                        <div class="text-sm text-gray-500">{{ Str::limit($materia->descripcion, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                            {{ $materia->tipo == 'obligatoria' ? 'bg-utn-blue/10 text-utn-blue-dark' : 
                               ($materia->tipo == 'nivelacion' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ \App\Models\Materia::getTipos()[$materia->tipo] ?? ucfirst($materia->tipo) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $materia->carga_horaria ?? '-' }} hs</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $materia->activa ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $materia->activa ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $materia->comisiones->count() }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @if(auth()->user()->hasPermission('comisiones.editar'))
                        <a href="{{ route('materias.edit', $materia) }}" class="text-yellow-600 hover:text-yellow-800">Editar</a>
                        @endif
                        @if(auth()->user()->hasPermission('comisiones.eliminar'))
                        <form action="{{ route('materias.destroy', $materia) }}" method="POST" class="inline" data-confirm="¿Eliminar esta materia?" data-confirm-type="danger" data-confirm-title="Eliminar materia" data-confirm-text="Eliminar">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">No hay materias registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($materias->hasPages())
        <div class="px-6 py-4 border-t">{{ $materias->links() }}</div>
        @endif
    </div>
</div>
@endsection