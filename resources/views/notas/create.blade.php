@extends('layouts.app')
@section('title', 'Crear Notas')
@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Crear Nueva Nota</h1>
        <p class="text-gray-600 mt-1">Completa el formulario para agregar una nota</p>
    </div>


    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <form action="{{ route('evaluaciones.store') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nombre --}}
                <!--<div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>-->
                

                {{-- Descripcion --}}
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <input type="text" name="observaciones" id="observaciones" value="{{ old('observaciones') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('observaciones') border-red-500 @enderror">
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Evaluacion --}}
                <div>
                    <label for="evaluacion" class="block text-sm font-medium text-gray-700 mb-2">Parcial *</label>
                    <select name="evaluacion" id="evaluacion" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('evaluacion') border-red-500 @enderror">
                        <option value="">Seleccione una opción...</option>
                        @foreach ($evaluaciones as $evaluacion)
                            <option value="{{ $evaluacion->id }}"
                                {{ old('evaluacion') == $evaluacion->id ? 'selected' : '' }}>
                                {{ ucfirst($evaluacion->nombre) }}
                            </option>
                        @endforeach
                    </select>

                    @error('evaluacion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nota --}}
                <div>
                    <label for="nota" class="block text-sm font-medium text-gray-700 mb-2">Nota *</label>
                    <input type="number" name="nota" id="nota" value="{{ old('nota') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('nota') border-red-500 @enderror">
                    @error('nota')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>



            </div>





             {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('evaluaciones.indexnota', $comision) }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-utn-blue text-white rounded-lg hover:bg-blue-800 transition-colors duration-200">
                    Crear Evaluacion
                </button>
            </div>

        </form>
    </div>


</div>




@endsection