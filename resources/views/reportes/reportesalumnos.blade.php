@extends('layouts.app')
@section('title', 'Reportes')
@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6 flex justify-end">
        <a href="{{ route('reportes.index') }}"
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
    </div>

    {{-- Mensajes --}}
       @if(session('success'))
        <div id="alert-success" class="relative bg-green-50 border-l-4 border-green-500 p-4 mb-6">
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

            <!-- Botón cerrar -->
            <button
                type="button"
                onclick="document.getElementById('alert-success').remove()"
                class="absolute top-2 right-2 text-green-600 hover:text-green-800"
            >
                ✕
            </button>
        </div>
        @endif


        @if(session('error'))
            <div id="alert-error" class="relative bg-red-50 border-l-4 border-red-500 p-4 mb-6">
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

                <!-- Botón cerrar -->
                <button
                    type="button"
                    onclick="document.getElementById('alert-error').remove()"
                    class="absolute top-2 right-2 text-red-600 hover:text-red-800"
                >
                    ✕
                </button>
            </div>
        @endif

    <div class="p-6 bg-white border-b border-gray-200">
        {{-- Filtros --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <form method="GET"  class="p-6">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        Filtros del Reporte
                    </h3>
                </div>


                 <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    {{-- Anio ingreso --}}
                       <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Año de Ingreso
                            </label>
                            <select name="anio_ingreso"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                                <option value="">Todos los años</option>
                                
                                @foreach($anio_ingreso as $anio)
                                    <option value="{{ $anio->anio_ingreso }}"
                                        {{ request('anio_ingreso') == $anio->anio_ingreso ? 'selected' : '' }}>
                                        {{ $anio->anio_ingreso }}
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

                </div>

                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('reportes.reportealumnos') }}"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Limpiar
                    </a>

                    <button type="submit"
                            class="px-6 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>


        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">

                {{-- CABECERA --}}
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alumno</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">DNI</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Año ingreso</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comisión</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>

                {{-- CUERPO --}}
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($alumnos as $alumno)
                    <tr class="hover:bg-gray-50">

                        {{-- Alumno --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-utn-blue/10 flex items-center justify-center">
                                    <span class="text-utn-blue-dark font-semibold text-xs">
                                        {{ strtoupper(substr($alumno->nombre, 0, 1)) }}
                                        {{ strtoupper(substr($alumno->apellido, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $alumno->apellido }}, {{ $alumno->nombre }}
                                </div>
                            </div>
                        </td>

                        {{-- DNI --}}
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $alumno->documento ?? 'N/A' }}
                        </td>

                        {{-- Año ingreso --}}
                        <td class="px-4 py-3 text-center text-sm font-semibold text-gray-700">
                            {{ $alumno->anio_ingreso }}
                        </td>

                        {{-- Comisión --}}
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $alumno->comision_nombre }}
                        </td>

                        {{-- Acciones --}}
                        <td class="px-4 py-3 text-sm text-gray-700">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('reportes.reportealumnosdetalle', $alumno->inscripcion_id) }}" 
                                    class="p-1.5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors" 
                                    title="Ver detalle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-500">
                            No hay registros
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{-- PAGINADOR --}}
            @if($alumnos->hasPages())
            <div class="px-4 py-3 border-t bg-gray-50">
                {{ $alumnos->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
