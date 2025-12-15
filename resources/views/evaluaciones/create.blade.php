@extends('layouts.app')
@section('title', 'Crear Evaluaciones')
@section('content')


<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Evaluacion</h1>
        <p class="text-gray-600 mt-1">Completa el formulario para agregar una nueva evaluacion de un usuario al sistema</p>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <form action="{{ route('evaluaciones.store') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nombre --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descripcion --}}
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripcion</label>
                    <input type="text" name="descripcion" id="descripcion" value="{{ old('descripcion') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('descripcion') border-red-500 @enderror">
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                {{-- Tipo --}}
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
                    <select name="tipo" id="tipo" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('tipo') border-red-500 @enderror">
                        <option value="">Seleccione una opción...</option>
                        @php
                            $tipos = ['parcial', 'recuperatorio', 'examen_final', 'otro'];
                        @endphp
                        @foreach ($tipos as $t)
                            <option value="{{ $t }}"
                                {{ old('tipo', $instancia->tipo ?? '') === $t ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $t)) }}
                            </option>
                        @endforeach
                    </select>

                    @error('tipo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                 {{-- Fecha --}}
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                    <input type="date" name="fecha" id="fecha" value="{{ old('fecha') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('fecha') border-red-500 @enderror">
                    @error('fecha')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                  {{-- Peso porcentual --}}
                <div>
                    <label for="porcentual" class="block text-sm font-medium text-gray-700 mb-2">Peso porcentual *</label>
                    <input type="number" name="porcentual" id="porcentual" value="{{ old('porcentual') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('porcentual') border-red-500 @enderror">
                    @error('porcentual')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                  {{-- Comision --}}
                <div>
                    <label for="comision" class="block text-sm font-medium text-gray-700 mb-2">Comisión</label>
                    <select name="comision" id="comision"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('comision') border-red-500 @enderror">
                        <option value="">Seleccione una comisión...</option>
                        @foreach ($comisiones as $comision)
                            <option value="{{ $comision->id }}" {{ old('comision') == $comision->id ? 'selected' : '' }}>
                                {{ $comision->nombre }} - {{ $comision->turno ?? '' }} ({{ $comision->anio ?? '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('comision')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                   {{-- Año --}}
                <div>
                    <label for="anio" class="block text-sm font-medium text-gray-700 mb-2">Año</label>
                    <input type="number" name="anio" id="anio" value="{{ old('anio') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('anio') border-red-500 @enderror">
                    @error('anio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

              {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('evaluaciones.index') }}"
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