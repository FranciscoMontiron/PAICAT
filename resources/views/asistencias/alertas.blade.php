@extends('layouts.app')

@section('title', 'Alumnos en Riesgo')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Alumnos en Riesgo por Asistencias</h1>

    <form method="GET" action="{{ route('asistencias.alertas') }}" class="mb-4">
        <label for="comision_id" class="font-semibold">Filtrar por comisión:</label>
        <select name="comision_id" id="comision_id" class="border px-2 py-1 rounded">
            <option value="">-- Todas las comisiones --</option>
            @foreach($comisiones as $com)
                <option value="{{ $com->id }}" @selected($comisionId == $com->id)>{{ $com->nombre }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">Filtrar</button>
    </form>

    @if($alumnosEnRiesgo->isEmpty())
        <p>No hay alumnos en riesgo.</p>
    @else
        <table class="w-full border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">Alumno</th>
                    <th class="border px-2 py-1">Comisión</th>
                    <th class="border px-2 py-1">Porcentaje Asistencia</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumnosEnRiesgo as $inscripcion)
                <tr>
                    <td class="border px-2 py-1">{{ $inscripcion->alumno->name }}</td>
                    <td class="border px-2 py-1">{{ $inscripcion->comision->nombre }}</td>
                    <td class="border px-2 py-1">{{ $inscripcion->calcularPorcentajeAsistencia() }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
