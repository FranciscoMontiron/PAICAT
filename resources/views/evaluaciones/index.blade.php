@extends('layouts.app')
@section('title', 'Evaluaciones')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Módulo de Evaluaciones y Notas</h1>
                <p class="text-gray-600 mt-1">Administra las evaluaciones</p>
            </div>
            @if(auth()->user()->hasPermission('evaluaciones.crear'))
            <a href="{{ route('evaluaciones.create') }}"
                class="bg-utn-blue text-white px-6 py-3 rounded-lg hover:bg-blue-800 transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                Nueva Evaluación
            </a>
            @endif
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




  {{-- Tabla de Evaluaciones --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripcion</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comision</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Anio</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($evaluaciones as $evaluacion)
                <tr class="{{ $evaluacion->trashed() ? 'bg-gray-100 opacity-60' : '' }}">

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $evaluacion->nombre }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $evaluacion->descripcion ?? 'N/A' }}
                    </td>

                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $evaluacion->tipo}}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $evaluacion->fecha->format('d/m/Y') }}
                    </td>

                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $evaluacion->peso_porcentual}}
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $evaluacion->comision_id ?? 'N/A'}}
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $evaluacion->anio}}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if($evaluacion->trashed())
                            @if(auth()->user()->hasPermission('evaluaciones.eliminar'))
                            <form method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900">Restaurar</button>
                            </form>
                            @endif
                        @else
                            @if($evaluacion->comision_id)
                            <a href="{{ route('evaluaciones.notas.index', $evaluacion->comision_id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver Notas</a>
                            @endif
                            @if(auth()->user()->hasPermission('evaluaciones.editar'))
                            <a href="{{ route('evaluaciones.edit', $evaluacion) }}" class="text-blue-600 hover:text-blue-900 mr-3">Editar</a>
                            @endif
                            @if(auth()->user()->hasPermission('evaluaciones.eliminar'))
                            <form action="{{ route('evaluaciones.destroy', $evaluacion) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta evaluación?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                            </form>
                            @endif
                        @endif
                    </td>

                </tr>



                @empty
                <tr class="px-6 py-12 text-center text-gray-500">
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="mt-2">No hay Evaluaciones registradas</p>
                        @if(auth()->user()->hasPermission('evaluaciones.crear'))
                        <a href="{{ route('evaluaciones.create') }}" class="mt-4 inline-block text-utn-blue hover:text-blue-800">
                            Crear la primera evaluación
                        </a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>


            

        </table>
    </div>

  <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Comisiones-Notas</h1>
                <p class="text-gray-600 mt-1">Administra las notas segun la comision</p>
            </div>
    </div>
    
{{-- Tabla de notas --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comision</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">año</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">turno</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">periodo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($comisiones as $comision)
                    <tr class="{{ $comision->trashed() ? 'bg-gray-100 opacity-60' : '' }}">

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
                            <a href="{{ route('evaluaciones.notas.index', $comision) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver Notas</a>
                    </td>

                </tr>    

                @empty
                    <tr class="px-6 py-12 text-center text-gray-500">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <p class="mt-2">No hay Notas registradas</p>
                            <a  href="" class="mt-4 inline-block text-utn-blue hover:text-blue-800">
                                Crear la primer Nota
                            </a>
                        </td>
                    </tr>
                 @endforelse
            </tbody>

        </table>
    </div>
</div>

 



@endsection



