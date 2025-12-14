@extends('layouts.app')
@section('title', 'Registrar Asistencias')
@section('content')
<div class="container mx-auto p-4">

    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-utn-blue">Registrar Asistencias</h1>
            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">Módulo 3</span>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <form action="{{ route('asistencias.guardar') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="comision_id" class="block text-gray-700 font-semibold mb-2">Comisión:</label>
                <select name="comision_id" id="comision_id" class="w-full border-gray-300 rounded p-2" onchange="this.form.submit()">
                    <option value="">-- Seleccionar comisión --</option>
                    @foreach($comisiones as $comision)
                        <option value="{{ $comision->id }}" 
                            {{ optional($comisionSeleccionada)->id == $comision->id ? 'selected' : '' }}>
                            {{ $comision->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="fecha" class="block text-gray-700 font-semibold mb-2">Fecha:</label>
                <input type="date" name="fecha" id="fecha" value="{{ $fecha }}" class="border-gray-300 rounded p-2 w-full">
            </div>

            @if($inscripciones->count() > 0)
                <table class="min-w-full border border-gray-200 rounded-lg mb-4">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Alumno</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Estado</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inscripciones as $inscripcion)
                            @php
                                $asistencia = $asistenciasExistentes[$inscripcion->id] ?? null;
                            @endphp
                            <tr class="border-t">
                                <td class="px-4 py-2 text-gray-700">
                                    {{ $inscripcion->alumno?->name ?? 'Alumno Desconocido' }}
                                </td>
                                <td class="px-4 py-2">
                                    <select name="asistencias[{{ $loop->index }}][estado]" class="border-gray-300 rounded p-1 w-full">
                                        @foreach(['presente','ausente','tardanza','justificado'] as $estado)
                                            <option value="{{ $estado }}" 
                                                {{ $asistencia?->estado == $estado ? 'selected' : '' }}>
                                                {{ ucfirst($estado) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="asistencias[{{ $loop->index }}][inscripcion_comision_id]" value="{{ $inscripcion->id }}">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="asistencias[{{ $loop->index }}][observaciones]" 
                                        value="{{ $asistencia?->observaciones }}" 
                                        class="border-gray-300 rounded p-1 w-full">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="submit" class="bg-utn-blue text-white px-4 py-2 rounded hover:bg-blue-700">Guardar Asistencias</button>
            @else
                <p class="text-gray-500">Seleccione una comisión para ver sus alumnos confirmados.</p>
            @endif

        </form>
    </div>

    <a href="{{ route('asistencias.index') }}" class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Volver al Módulo de Asistencias</a>

</div>
@endsection
