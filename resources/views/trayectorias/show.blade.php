@extends('layouts.app')
@section('title', 'Detalle de Trayectoria')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('trayectorias.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $person->nombre ?? 'N/A' }} {{ $person->apellido ?? '' }}</h1>
                <p class="text-gray-600">DNI: {{ $person->documento ?? 'N/A' }} | {{ $inscripcion->especialidad_nombre ?? 'Sin especialidad' }}</p>
            </div>
        </div>
        @if(auth()->user()->hasPermission('inscripciones.editar'))
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('modalCambiarEstado').classList.remove('hidden')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                Cambiar Estado
            </button>
            <button onclick="document.getElementById('modalBaja').classList.remove('hidden')"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                Registrar Baja
            </button>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna Principal --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info del estudiante --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Información de Inscripción</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">Año de Ingreso</span>
                        <p class="font-medium">{{ $inscripcion->anio_ingreso }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Modalidad</span>
                        <p class="font-medium">{{ $inscripcion->modalidad }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Turno</span>
                        <p class="font-medium">{{ $inscripcion->turno_ingreso ?? 'No especificado' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Estado Inscripción</span>
                        <p class="font-medium">{{ ucfirst($inscripcion->estado) }}</p>
                    </div>
                </div>
            </div>

            {{-- Historial de Trayectoria --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Historial de Trayectoria</h2>
                @if($inscripcion->trayectorias && $inscripcion->trayectorias->count() > 0)
                <div class="relative">
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    <div class="space-y-4">
                        @foreach($inscripcion->trayectorias as $trayectoria)
                        @php
                        $colors = [
                        'activo' => 'bg-green-500',
                        'pausado' => 'bg-yellow-500',
                        'libre' => 'bg-orange-500',
                        'baja' => 'bg-red-500',
                        'reincorporado' => 'bg-blue-500',
                        'aprobado' => 'bg-emerald-500',
                        'desaprobado' => 'bg-red-500',
                        ];
                        @endphp
                        <div class="relative pl-10">
                            <div class="absolute left-2 w-4 h-4 rounded-full {{ $colors[$trayectoria->estado] ?? 'bg-gray-400' }} border-2 border-white"></div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-semibold text-gray-900 capitalize">{{ $trayectoria->estado }}</span>
                                    <span class="text-sm text-gray-500">{{ $trayectoria->fecha_inicio->format('d/m/Y') }}</span>
                                </div>
                                @if($trayectoria->motivo)
                                <p class="text-sm text-gray-600">{{ $trayectoria->motivo }}</p>
                                @endif
                                @if($trayectoria->fecha_fin)
                                <p class="text-xs text-gray-400 mt-2">Hasta: {{ $trayectoria->fecha_fin->format('d/m/Y') }}</p>
                                @else
                                <span class="inline-block mt-2 text-xs px-2 py-1 bg-green-100 text-green-700 rounded">Vigente</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <p class="text-gray-500 text-center py-8">No hay registros de trayectoria aún.</p>
                @endif
            </div>

            {{-- Comisiones Inscriptas --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Comisiones</h2>
                    @if(auth()->user()->hasPermission('inscripciones.editar'))
                    <button onclick="document.getElementById('modalSolicitudCambio').classList.remove('hidden')"
                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        + Solicitar Cambio
                    </button>
                    @endif
                </div>
                @if($inscripcion->inscripcionesComision && $inscripcion->inscripcionesComision->count() > 0)
                <div class="space-y-3">
                    @foreach($inscripcion->inscripcionesComision as $ic)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $ic->comision->nombre ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $ic->comision->materia->nombre ?? 'Sin materia' }} • {{ $ic->comision->turno ?? '' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $ic->estado == 'inscripto' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($ic->estado) }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-6">No está inscripto en ninguna comisión.</p>
                @endif
            </div>
        </div>

        {{-- Columna Lateral --}}
        <div class="space-y-6">
            {{-- Condiciones Particulares --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Condiciones Particulares</h2>
                    @if(auth()->user()->hasPermission('inscripciones.editar'))
                    <button onclick="document.getElementById('modalCondicion').classList.remove('hidden')"
                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        + Agregar
                    </button>
                    @endif
                </div>
                @if($inscripcion->condicionesParticulares && $inscripcion->condicionesParticulares->where('activa', true)->count() > 0)
                <div class="space-y-3">
                    @foreach($inscripcion->condicionesParticulares->where('activa', true) as $condicion)
                    <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-purple-900 text-sm">{{ $condicion->titulo }}</p>
                                <p class="text-xs text-purple-600 capitalize">{{ str_replace('_', ' ', $condicion->tipo) }}</p>
                            </div>
                            @if(auth()->user()->hasPermission('inscripciones.editar'))
                            <form action="{{ route('trayectorias.desactivar-condicion', $condicion) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-purple-400 hover:text-purple-600" title="Desactivar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                        @if($condicion->descripcion)
                        <p class="text-xs text-purple-700 mt-2">{{ Str::limit($condicion->descripcion, 100) }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4 text-sm">Sin condiciones registradas.</p>
                @endif
            </div>

            {{-- Solicitudes Pendientes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Solicitudes de Cambio</h2>
                @if($inscripcion->solicitudesCambio && $inscripcion->solicitudesCambio->count() > 0)
                <div class="space-y-3">
                    @foreach($inscripcion->solicitudesCambio->take(5) as $solicitud)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $solicitud->tipo) }}</span>
                            @php
                            $estadoColors = [
                            'pendiente' => 'bg-yellow-100 text-yellow-700',
                            'aprobada' => 'bg-green-100 text-green-700',
                            'rechazada' => 'bg-red-100 text-red-700',
                            'trueque_detectado' => 'bg-blue-100 text-blue-700',
                            ];
                            @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $estadoColors[$solicitud->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4 text-sm">Sin solicitudes.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Cambiar Estado --}}
<div id="modalCambiarEstado" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cambiar Estado de Trayectoria</h3>
            <form action="{{ route('trayectorias.cambiar-estado', $inscripcion) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Estado</label>
                        <select name="estado" required class="w-full rounded-lg border-gray-300">
                            <option value="activo">Activo</option>
                            <option value="pausado">Pausado</option>
                            <option value="libre">Libre</option>
                            <option value="reincorporado">Reincorporado</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="desaprobado">Desaprobado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                        <textarea name="motivo" required rows="3" class="w-full rounded-lg border-gray-300" placeholder="Explique el motivo del cambio..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalCambiarEstado').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Registrar Baja --}}
<div id="modalBaja" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-red-600 mb-4">Registrar Baja</h3>
            <form action="{{ route('trayectorias.baja', $inscripcion) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de la Baja</label>
                        <textarea name="motivo" required rows="3" class="w-full rounded-lg border-gray-300" placeholder="Ingrese el motivo de la baja..."></textarea>
                    </div>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="es_voluntaria" value="1" class="rounded border-gray-300">
                            <span class="text-sm text-gray-700">Baja voluntaria (solicitada por el estudiante)</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalBaja').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Confirmar Baja</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Agregar Condición --}}
<div id="modalCondicion" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Condición Particular</h3>
            <form action="{{ route('trayectorias.agregar-condicion', $inscripcion) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select name="tipo" required class="w-full rounded-lg border-gray-300">
                            <option value="discapacidad">Discapacidad</option>
                            <option value="enfermedad_cronica">Enfermedad Crónica</option>
                            <option value="situacion_laboral">Situación Laboral</option>
                            <option value="situacion_familiar">Situación Familiar</option>
                            <option value="condicionalidad_academica">Condicionalidad Académica</option>
                            <option value="otra">Otra</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                        <input type="text" name="titulo" required class="w-full rounded-lg border-gray-300" placeholder="Ej: Hipoacusia leve">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" required rows="3" class="w-full rounded-lg border-gray-300" placeholder="Describa la condición y su impacto..."></textarea>
                    </div>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="requiere_adecuacion" value="1" class="rounded border-gray-300">
                            <span class="text-sm text-gray-700">Requiere adecuaciones pedagógicas</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalCondicion').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Solicitud de Cambio --}}
<div id="modalSolicitudCambio" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Solicitar Cambio</h3>
            <form action="{{ route('trayectorias.crear-solicitud', $inscripcion) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Cambio</label>
                        <select name="tipo" id="tipoSolicitud" required class="w-full rounded-lg border-gray-300" onchange="toggleCamposCambio()">
                            <option value="comision">Cambio de Comisión</option>
                            <option value="modalidad">Cambio de Modalidad</option>
                            <option value="turno">Cambio de Turno</option>
                        </select>
                    </div>
                    <div id="campoComision">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Comisión Destino</label>
                        <select name="comision_destino_id" class="w-full rounded-lg border-gray-300">
                            <option value="">Seleccionar comisión...</option>
                            @if(isset($comisionesDisponibles) && $comisionesDisponibles->count() > 0)
                                @php
                                    $grupoAnterior = null;
                                @endphp
                                @foreach($comisionesDisponibles->groupBy(fn($c) => ($c->turno ?? 'Sin turno') . ' - ' . ($c->modalidad ?? 'Sin modalidad')) as $grupo => $comisiones)
                                    <optgroup label="{{ $grupo }}">
                                        @foreach($comisiones as $comision)
                                            <option value="{{ $comision->id }}">
                                                {{ $comision->nombre }}
                                                @if($comision->municipio) - {{ $comision->municipio->nombre }}@endif
                                                @if($comision->cupo_maximo && !$comision->esVirtual())
                                                    ({{ $comision->cupos_disponibles ?? 0 }} cupos)
                                                @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @else
                                <option value="" disabled>No hay comisiones disponibles</option>
                            @endif
                        </select>
                        @if(isset($comisionesDisponibles) && $comisionesDisponibles->count() == 0)
                            <p class="mt-1 text-xs text-amber-600">No hay comisiones con cupo disponible para cambio.</p>
                        @endif
                    </div>
                    <div id="campoModalidad" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Modalidad Destino</label>
                        <select name="modalidad_destino" class="w-full rounded-lg border-gray-300">
                            @foreach($modalidades ?? ['Presencial' => 'Presencial', 'Virtual' => 'Virtual', 'Semipresencial' => 'Semipresencial'] as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="campoTurno" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Turno Destino</label>
                        <select name="turno_destino" class="w-full rounded-lg border-gray-300">
                            @foreach($turnos ?? ['mañana' => 'Mañana', 'tardenoche' => 'TardeNoche'] as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                        <textarea name="motivo" required rows="3" class="w-full rounded-lg border-gray-300" placeholder="Explique el motivo de la solicitud..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalSolicitudCambio').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Enviar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleCamposCambio() {
        const tipo = document.getElementById('tipoSolicitud').value;
        document.getElementById('campoComision').classList.toggle('hidden', tipo !== 'comision');
        document.getElementById('campoModalidad').classList.toggle('hidden', tipo !== 'modalidad');
        document.getElementById('campoTurno').classList.toggle('hidden', tipo !== 'turno');
    }
</script>
@endpush
@endsection