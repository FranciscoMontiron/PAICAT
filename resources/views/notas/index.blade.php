@extends('layouts.app')
@section('title', 'Notas')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Módulo Notas Comision</h1>
                    <p class="text-gray-600 mt-1">Administra las Notas de los aumnos de la comision </p>
                </div>
                <a href="{{ route('evaluaciones.createnota', $comision) }}"
                
                    class="bg-utn-blue text-white px-6 py-3 rounded-lg hover:bg-blue-800 transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    Cargar Nota
                </a>
        </div>

        <div class="flex justify-between items-center mb-6">
             <div>
                    <h3 class="text-3xl font-bold text-gray-900">Comision {{$comision->nombre}} - {{$comision->anio}} - {{$comision->periodo}}</h3>
            </div>

        </div>


            
{{-- Tabla de notas --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumno</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evaluacion</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha carga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargado por</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">observaciones</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($notas as $nota)
                    <tr class="{{ $evaluacion->trashed() ? 'bg-gray-100 opacity-60' : '' }}">

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $comision->nombre }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $comision->anio }}
                    </td>

                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $comision->turno }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $comision->periodo }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">                      
                            <a href="{{ route('evaluaciones.indexnota', $comision) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                    </td>

                </tr>    

                @empty
                    <tr class="px-6 py-12 text-center text-gray-500">
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <p class="mt-2">No hay Notas registradas</p>
                            <a  href="{{ route('evaluaciones.createnota', $comision) }}" class="mt-4 inline-block text-utn-blue hover:text-blue-800">
                                Crear la primer Nota
                            </a>
                        </td>
                    </tr>
                 @endforelse
            </tbody>

        </table>
    </div>














    {{-- Mensajes de éxito --}}
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



</div>
@endsection
