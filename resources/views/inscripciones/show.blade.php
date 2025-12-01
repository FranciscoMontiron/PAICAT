@extends('layouts.app')

@section('title', 'Detalle de Inscripción')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detalle de Inscripción</h1>
            <p class="text-gray-600 mt-1">Inscripción #{{ $inscripcion->id }} - Año {{ $inscripcion->anio_ingreso }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('inscripciones.index') }}"
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
            @if(auth()->user()->hasPermission('inscripciones.editar') && $inscripcion->puedeModificarse())
            <a href="{{ route('inscripciones.edit', $inscripcion) }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            @endif
        </div>
    </div>

    {{-- Mensajes --}}
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

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
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
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna principal --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Estado de la inscripción --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Estado de la Inscripción</h2>
                    <span class="px-3 py-1 text-sm rounded-full
                        @if($inscripcion->estado === 'pendiente') bg-yellow-100 text-yellow-800
                        @elseif($inscripcion->estado === 'documentacion_ok') bg-blue-100 text-blue-800
                        @elseif($inscripcion->estado === 'confirmado') bg-green-100 text-green-800
                        @elseif($inscripcion->estado === 'cancelado') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ \App\Models\Inscripcion::ESTADOS[$inscripcion->estado] ?? $inscripcion->estado }}
                    </span>
                </div>

                {{-- Acciones de estado --}}
                @if(auth()->user()->hasPermission('inscripciones.editar'))
                <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t">
                    @if($inscripcion->estado === 'documentacion_ok')
                    <form action="{{ route('inscripciones.confirmar', $inscripcion) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                            Confirmar Inscripción
                        </button>
                    </form>
                    @endif

                    @if($inscripcion->puedeCancelarse())
                    <button type="button" onclick="document.getElementById('modal-cancelar').classList.remove('hidden')"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm">
                        Cancelar Inscripción
                    </button>
                    @endif
                </div>
                @endif
            </div>

            {{-- Datos del alumno --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Datos del Alumno</h2>

                @if($persona)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">Nombre completo</span>
                        <p class="font-medium">{{ $persona->apellido }}, {{ $persona->nombre }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">DNI</span>
                        <p class="font-medium">{{ $persona->documento }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Email</span>
                        <p class="font-medium">{{ $persona->email }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Teléfono</span>
                        <p class="font-medium">{{ $persona->telefono_celular ?? $persona->telefono_fijo ?? 'No registrado' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Fecha de nacimiento</span>
                        <p class="font-medium">{{ $persona->nacimiento_fecha?->format('d/m/Y') ?? 'No registrada' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Estado en sistema de alumnos</span>
                        <p class="font-medium">
                            <span class="px-2 py-1 text-xs rounded-full {{ $persona->__estado === 'Verificado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $persona->__estado }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($persona->secundariaDato)
                <div class="mt-6 pt-4 border-t">
                    <h3 class="text-md font-semibold text-gray-700 mb-3">Datos del Secundario</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Título</span>
                            <p class="font-medium">{{ $persona->secundariaDato->titulo ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Institución</span>
                            <p class="font-medium">{{ $persona->secundariaDato->institucion ?? 'No registrada' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Año de egreso</span>
                            <p class="font-medium">{{ $persona->secundariaDato->anio_egreso ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Promedio</span>
                            <p class="font-medium">{{ $persona->secundariaDato->promedio ?? 'No registrado' }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @else
                <div class="text-center py-8 text-gray-500">
                    <p>No se encontraron datos del alumno en el sistema de alumnos.</p>
                </div>
                @endif
            </div>

            {{-- Datos de la inscripción --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Datos de la Inscripción</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">Año de ingreso</span>
                        <p class="font-medium">{{ $inscripcion->anio_ingreso }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Especialidad</span>
                        <p class="font-medium">{{ $especialidad?->nombre ?? 'No especificada' }}</p>
                    </div>
                    @if($especialidadAlternativa)
                    <div>
                        <span class="text-sm text-gray-500">Especialidad alternativa</span>
                        <p class="font-medium">{{ $especialidadAlternativa->nombre }}</p>
                    </div>
                    @endif
                    <div>
                        <span class="text-sm text-gray-500">Modalidad</span>
                        <p class="font-medium">{{ $inscripcion->modalidad }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Tipo de ingreso</span>
                        <p class="font-medium">{{ $inscripcion->tipo_ingreso }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Turno de ingreso</span>
                        <p class="font-medium">{{ $inscripcion->turno_ingreso ?? 'No especificado' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Turno de carrera</span>
                        <p class="font-medium">{{ $inscripcion->turno_carrera ?? 'No especificado' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Fecha de inscripción</span>
                        <p class="font-medium">{{ $inscripcion->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if($inscripcion->observaciones)
                <div class="mt-4 pt-4 border-t">
                    <span class="text-sm text-gray-500">Observaciones</span>
                    <p class="font-medium">{{ $inscripcion->observaciones }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Columna lateral --}}
        <div class="space-y-6">
            {{-- Validación de documentación --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Validación de Documentación</h2>

                @if(auth()->user()->hasPermission('inscripciones.editar'))
                <form action="{{ route('inscripciones.validar-documentacion', $inscripcion) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="doc_dni_validado" value="1" {{ $inscripcion->doc_dni_validado ? 'checked' : '' }}
                                   class="h-4 w-4 text-utn-blue border-gray-300 rounded focus:ring-utn-blue">
                            <span class="ml-2 text-sm text-gray-700">DNI validado</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="doc_titulo_validado" value="1" {{ $inscripcion->doc_titulo_validado ? 'checked' : '' }}
                                   class="h-4 w-4 text-utn-blue border-gray-300 rounded focus:ring-utn-blue">
                            <span class="ml-2 text-sm text-gray-700">Título secundario validado</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="doc_analitico_validado" value="1" {{ $inscripcion->doc_analitico_validado ? 'checked' : '' }}
                                   class="h-4 w-4 text-utn-blue border-gray-300 rounded focus:ring-utn-blue">
                            <span class="ml-2 text-sm text-gray-700">Analítico validado</span>
                        </label>

                        <div>
                            <label for="observaciones_documentacion" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                            <textarea name="observaciones_documentacion" id="observaciones_documentacion" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent text-sm">{{ $inscripcion->observaciones_documentacion }}</textarea>
                        </div>

                        <button type="submit" class="w-full bg-utn-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors text-sm">
                            Guardar Validación
                        </button>
                    </div>
                </form>
                @else
                <div class="space-y-3">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full {{ $inscripcion->doc_dni_validado ? 'bg-green-500' : 'bg-gray-300' }} mr-2"></span>
                        <span class="text-sm text-gray-700">DNI {{ $inscripcion->doc_dni_validado ? 'validado' : 'pendiente' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full {{ $inscripcion->doc_titulo_validado ? 'bg-green-500' : 'bg-gray-300' }} mr-2"></span>
                        <span class="text-sm text-gray-700">Título {{ $inscripcion->doc_titulo_validado ? 'validado' : 'pendiente' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full {{ $inscripcion->doc_analitico_validado ? 'bg-green-500' : 'bg-gray-300' }} mr-2"></span>
                        <span class="text-sm text-gray-700">Analítico {{ $inscripcion->doc_analitico_validado ? 'validado' : 'pendiente' }}</span>
                    </div>
                </div>
                @endif

                @if($inscripcion->usuario_validacion_id)
                <div class="mt-4 pt-4 border-t text-sm text-gray-500">
                    <p>Validado por: {{ $inscripcion->usuarioValidacion?->nombre_completo ?? 'Usuario eliminado' }}</p>
                    <p>Fecha: {{ $inscripcion->fecha_validacion?->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>

            {{-- Información de auditoría --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información de Auditoría</h2>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-500">Registrado por:</span>
                        <p class="font-medium">{{ $inscripcion->usuarioRegistro?->nombre_completo ?? 'Usuario eliminado' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Fecha de creación:</span>
                        <p class="font-medium">{{ $inscripcion->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Última modificación:</span>
                        <p class="font-medium">{{ $inscripcion->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de cancelación --}}
<div id="modal-cancelar" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Cancelar Inscripción</h3>
        <form action="{{ route('inscripciones.cancelar', $inscripcion) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="motivo_cancelacion" class="block text-sm font-medium text-gray-700 mb-2">Motivo de cancelación</label>
                <textarea name="motivo_cancelacion" id="motivo_cancelacion" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent"
                          placeholder="Ingrese el motivo de la cancelación..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-cancelar').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirmar Cancelación
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
