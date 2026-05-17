@extends('layouts.app')
@section('title', 'Ficha de Alumnos')
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
            <h1 class="text-3xl font-bold text-gray-800">Ficha de Alumnos</h1>
            <p class="text-gray-600 mt-1">Listado de alumnos con acceso a ficha individual</p>
        </div>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div id="alert-success" class="relative bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-green-600 hover:text-green-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div id="alert-error" class="relative bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-600 hover:text-red-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Anio de Ingreso</label>
                <select name="anio_ingreso" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todos los anios</option>
                    @foreach($anio_ingreso as $anio)
                        <option value="{{ $anio->anio_ingreso }}" {{ request('anio_ingreso') == $anio->anio_ingreso ? 'selected' : '' }}>{{ $anio->anio_ingreso }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Comision</label>
                <select name="comision_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">Todas</option>
                    @foreach($comisiones as $comision)
                        <option value="{{ $comision->id }}" {{ request('comision_id') == $comision->id ? 'selected' : '' }}>{{ $comision->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">Filtrar</button>
            @if(request()->hasAny(['anio_ingreso', 'comision_id']))
            <a href="{{ route('reportes.reportealumnos') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Limpiar</a>
            @endif
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">DNI</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Anio ingreso</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Comision</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($alumnos as $alumno)
                <tr class="hover:bg-gray-50 transition-colors cursor-pointer group" onclick="window.location='{{ route('reportes.reportealumnosdetalle', $alumno->inscripcion_id) }}'">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-purple-600">
                                    {{ strtoupper(substr($alumno->apellido, 0, 1) . substr($alumno->nombre, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $alumno->apellido }}, {{ $alumno->nombre }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600">{{ $alumno->documento ?? 'N/A' }}</td>
                    <td class="px-4 py-4 text-sm text-center font-semibold text-gray-700">{{ $alumno->anio_ingreso }}</td>
                    <td class="px-4 py-4 text-sm text-gray-600">{{ $alumno->comision_nombre }}</td>
                    <td class="px-4 py-4 text-center">
                        <a href="{{ route('reportes.reportealumnosdetalle', $alumno->inscripcion_id) }}"
                           class="p-1.5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors inline-flex"
                           title="Ver ficha" onclick="event.stopPropagation()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <p class="mt-3 text-gray-500">No se encontraron alumnos con los criterios seleccionados.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($alumnos->hasPages())
    <div class="mt-6">
        {{ $alumnos->links() }}
    </div>
    @endif

</div>

@endsection
