@extends('layouts.app')

@section('title', 'Detalle Cursada')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-4 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('comisiones.index') }}" class="text-utn-blue hover:underline">Comisiones</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="{{ route('comisiones.show', $comision) }}" class="text-utn-blue hover:underline">{{ $comision->codigo }}</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="{{ route('cursadas.index', $comision) }}" class="text-utn-blue hover:underline">Cursadas</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li class="text-gray-500">Detalle</li>
        </ol>
    </nav>

    @php
        $estudiante = $cursada->getEstudiante();
        $estadoColors = [
            'cursando' => 'bg-blue-100 text-blue-800 border-blue-200',
            'aprobado' => 'bg-green-100 text-green-800 border-green-200',
            'desaprobado' => 'bg-red-100 text-red-800 border-red-200',
            'libre' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'abandono' => 'bg-gray-100 text-gray-800 border-gray-200',
            'baja' => 'bg-gray-100 text-gray-800 border-gray-200',
        ];
    @endphp

    {{-- Header --}}
    <div class="mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                {{ $estudiante ? $estudiante->apellido . ', ' . $estudiante->nombre : 'Sin datos del estudiante' }}
            </h1>
            <p class="text-gray-600 mt-1">
                Cursada {{ $cursada->anio }} - {{ $comision->nombre }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border {{ $estadoColors[$cursada->estado] ?? 'bg-gray-100 text-gray-800' }}">
                {{ \App\Models\Cursada::ESTADOS[$cursada->estado] ?? $cursada->estado }}
            </span>
            <a href="{{ route('cursadas.edit', [$comision, $cursada]) }}"
               class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                Editar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Información principal --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Datos del estudiante --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Datos del Estudiante</h2>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $estudiante ? $estudiante->apellido . ', ' . $estudiante->nombre : 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Documento</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $estudiante?->documento ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $estudiante?->email ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $estudiante?->telefono ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Notas --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Notas</h2>
                    <form action="{{ route('cursadas.recalcular-nota', [$comision, $cursada]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-utn-blue hover:underline">
                            Recalcular promedio
                        </button>
                    </form>
                </div>
                @if($cursada->notas->isEmpty())
                    <p class="text-gray-500 text-sm">No hay notas cargadas.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Evaluación</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nota</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cursada->notas as $nota)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $nota->evaluacion?->nombre ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ ucfirst($nota->evaluacion?->tipo ?? '-') }}</td>
                                        <td class="px-4 py-2 text-sm font-medium {{ ($nota->nota ?? 0) >= 6 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $nota->nota !== null ? number_format($nota->nota, 2) : '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ $nota->fecha_carga?->format('d/m/Y') ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Asistencias resumen --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Asistencia</h2>
                @php
                    $totalAsistencias = $cursada->asistencias->count();
                    $presentes = $cursada->asistencias->where('estado', 'presente')->count();
                    $ausentes = $cursada->asistencias->where('estado', 'ausente')->count();
                    $tardanzas = $cursada->asistencias->where('estado', 'tardanza')->count();
                    $justificadas = $cursada->asistencias->where('estado', 'justificado')->count();
                    $porcentaje = $cursada->porcentajeAsistencia();
                @endphp
                @if($totalAsistencias == 0)
                    <p class="text-gray-500 text-sm">No hay registros de asistencia.</p>
                @else
                    <div class="grid grid-cols-5 gap-4 mb-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $totalAsistencias }}</p>
                            <p class="text-xs text-gray-500">Total clases</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ $presentes }}</p>
                            <p class="text-xs text-gray-500">Presentes</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-red-600">{{ $ausentes }}</p>
                            <p class="text-xs text-gray-500">Ausentes</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-yellow-600">{{ $tardanzas }}</p>
                            <p class="text-xs text-gray-500">Tardanzas</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $justificadas }}</p>
                            <p class="text-xs text-gray-500">Justificadas</p>
                        </div>
                    </div>
                    <div class="bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div class="h-full {{ $porcentaje >= 75 ? 'bg-green-500' : ($porcentaje >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                             style="width: {{ $porcentaje }}%"></div>
                    </div>
                    <p class="text-center text-sm mt-2 {{ $porcentaje >= 75 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($porcentaje, 1) }}% de asistencia
                        @if($porcentaje < 75)
                            <span class="text-red-600 font-medium">(mínimo requerido: 75%)</span>
                        @endif
                    </p>
                @endif
            </div>
        </div>

        {{-- Panel lateral --}}
        <div class="space-y-6">
            {{-- Datos de la cursada --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Datos de Cursada</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Comisión</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $comision->codigo }} - {{ $comision->nombre }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Año</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $cursada->anio }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Modalidad</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $cursada->modalidad ?? $comision->modalidad ?? 'N/A' }}</dd>
                    </div>
                    @if($comision->municipio)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Municipio</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $comision->municipio->nombre }}</dd>
                    </div>
                    @endif
                    @if($comision->aula)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Aula</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $comision->aula->nombre }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nota Final</dt>
                        <dd class="mt-1 text-lg font-bold {{ ($cursada->nota_final ?? 0) >= 6 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $cursada->nota_final !== null ? number_format($cursada->nota_final, 2) : 'Sin calcular' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Recursante</dt>
                        <dd class="mt-1">
                            @if($cursada->es_recursante)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    Sí
                                </span>
                            @else
                                <span class="text-sm text-gray-500">No</span>
                            @endif
                        </dd>
                    </div>
                    @if($cursada->fecha_inicio)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fecha Inicio</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $cursada->fecha_inicio->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                    @if($cursada->fecha_fin)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fecha Fin</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $cursada->fecha_fin->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Cambiar estado --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Cambiar Estado</h2>
                <form action="{{ route('cursadas.cambiar-estado', [$comision, $cursada]) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Estado</label>
                            <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                                @foreach(\App\Models\Cursada::ESTADOS as $key => $label)
                                    <option value="{{ $key }}" {{ $cursada->estado == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observación (opcional)</label>
                            <textarea name="observacion" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent"
                                      placeholder="Motivo del cambio..."></textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                            Guardar Estado
                        </button>
                    </div>
                </form>
            </div>

            {{-- Observaciones --}}
            @if($cursada->observaciones)
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Observaciones</h2>
                <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $cursada->observaciones }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
