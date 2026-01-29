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
                        @foreach (\App\Models\Evaluacion::tiposDisponibles() as $key => $label)
                        <option value="{{ $key }}" {{ old('tipo') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('tipo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Instancia (solo para parciales) --}}
                <div id="instancia-container">
                    <label for="instancia" class="block text-sm font-medium text-gray-700 mb-2">Instancia</label>
                    <select name="instancia" id="instancia"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('instancia') border-red-500 @enderror">
                        <option value="">Sin instancia</option>
                        @foreach (\App\Models\Evaluacion::instanciasDisponibles() as $key => $label)
                        <option value="{{ $key }}" {{ old('instancia') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Para parciales: 1ra, 2da o 3ra instancia</p>
                    @error('instancia')
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

                {{-- Materia (Dinámico) --}}
                <div>
                    <label for="materia_id" class="block text-sm font-medium text-gray-700 mb-2">Materia *</label>
                    <select name="materia_id" id="materia_id" required disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent bg-gray-100">
                        <option value="">Seleccione primero una comisión...</option>
                    </select>
                    @error('materia_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Año --}}
                <div>
                    <label for="anio" class="block text-sm font-medium text-gray-700 mb-2">Año *</label>
                    <input type="number" name="anio" id="anio" value="{{ old('anio', date('Y')) }}" required
                        min="2020" max="2100"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('anio') border-red-500 @enderror">
                    @error('anio')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cuenta para promedio --}}
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="cuenta_promedio" id="cuenta_promedio" value="1"
                            {{ old('cuenta_promedio', true) ? 'checked' : '' }}
                            class="h-5 w-5 text-utn-blue border-gray-300 rounded focus:ring-utn-blue">
                        <span class="text-sm font-medium text-gray-700">Cuenta para el promedio final</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500 ml-8">Si está marcado, esta evaluación se incluirá en el cálculo del promedio del estudiante</p>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const comisionSelect = document.getElementById('comision');
        const materiaSelect = document.getElementById('materia_id');

        // Función para cargar materias
        function cargarMaterias(comisionId) {
            if (!comisionId) {
                materiaSelect.innerHTML = '<option value="">Seleccione primero una comisión...</option>';
                materiaSelect.disabled = true;
                materiaSelect.classList.add('bg-gray-100');
                return;
            }

            // Habilitar y mostrar loading
            materiaSelect.disabled = true;
            materiaSelect.innerHTML = '<option value="">Cargando materias...</option>';
            materiaSelect.classList.remove('bg-gray-100');

            fetch(`{{ url('evaluaciones/getMaterias') }}/${comisionId}`)
                .then(response => response.json())
                .then(data => {
                    materiaSelect.innerHTML = '<option value="">Seleccione una materia...</option>';

                    if (data.length === 0) {
                        materiaSelect.innerHTML = '<option value="">La comisión no tiene materias asignadas</option>';
                    } else {
                        data.forEach(materia => {
                            const option = document.createElement('option');
                            option.value = materia.id;
                            option.textContent = materia.nombre;
                            @if(old('materia_id'))
                            if (materia.id == {
                                    {
                                        old('materia_id')
                                    }
                                }) {
                                option.selected = true;
                            }
                            @endif
                            materiaSelect.appendChild(option);
                        });
                        materiaSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar materias:', error);
                    materiaSelect.innerHTML = '<option value="">Error al cargar materias</option>';
                });
        }

        // Evento change
        comisionSelect.addEventListener('change', function() {
            cargarMaterias(this.value);
        });

        // Carga inicial si hay comisión seleccionada (ej: old input)
        if (comisionSelect.value) {
            cargarMaterias(comisionSelect.value);
        }
    });
</script>
@endpush