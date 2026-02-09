@extends('layouts.app')

@section('title', 'Solicitudes de Cambio')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Solicitudes de Cambio</h1>
            <p class="text-gray-600 mt-1">Gestión de solicitudes de cambio de comisión</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <form action="{{ route('solicitudes.detectar-trueques') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Detectar Trueques
                </button>
            </form>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600">Pendientes</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $estadisticas['pendientes'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-sm text-gray-600">Trueques Detectados</p>
            <p class="text-2xl font-bold text-purple-600">{{ $estadisticas['trueques'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600">En Revisión</p>
            <p class="text-2xl font-bold text-blue-600">{{ $estadisticas['en_revision'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600">Aprobadas Hoy</p>
            <p class="text-2xl font-bold text-green-600">{{ $estadisticas['aprobadas_hoy'] }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form action="{{ route('solicitudes.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar alumno</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Nombre, apellido o documento..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                    <option value="">Todos</option>
                    @foreach(\App\Models\SolicitudCambio::ESTADOS as $key => $label)
                        <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-utn-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                Filtrar
            </button>
            @if(request()->hasAny(['buscar', 'estado']))
                <a href="{{ route('solicitudes.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-lg">
            {{ session('info') }}
        </div>
    @endif

    {{-- Tabla de solicitudes --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumno</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cambio Solicitado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cupos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($solicitudes as $solicitud)
                    @php
                        $alumno = $solicitud->inscripcion?->alumno;
                        $cuposDestino = $solicitud->comisionDestino?->cupos_disponibles;
                        $tieneCupos = $solicitud->comisionDestino?->tieneCuposDisponibles() ?? true;
                    @endphp
                    <tr class="{{ $solicitud->estado === 'trueque_detectado' ? 'bg-purple-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-sm">
                                        {{ $alumno ? strtoupper(substr($alumno->nombre, 0, 1) . substr($alumno->apellido, 0, 1)) : '?' }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $alumno ? $alumno->apellido . ', ' . $alumno->nombre : 'Alumno no encontrado' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $alumno?->documento ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-500">{{ $solicitud->comisionOrigen?->nombre ?? 'Sin comisión' }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                    <span class="font-medium text-gray-900">{{ $solicitud->comisionDestino?->nombre ?? '-' }}</span>
                                </div>
                                @if($solicitud->modalidad_origen && $solicitud->modalidad_destino && $solicitud->modalidad_origen !== $solicitud->modalidad_destino)
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $solicitud->modalidad_origen }} → {{ $solicitud->modalidad_destino }}
                                    </div>
                                @endif
                                @if($solicitud->turno_origen && $solicitud->turno_destino && $solicitud->turno_origen !== $solicitud->turno_destino)
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ ucfirst($solicitud->turno_origen) }} → {{ ucfirst($solicitud->turno_destino) }}
                                    </div>
                                @endif
                            </div>

                            @if($solicitud->solicitud_trueque_id)
                                <div class="mt-1 flex items-center gap-1 text-xs text-purple-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    Trueque con: {{ $solicitud->solicitudTrueque?->inscripcion?->alumno?->apellido ?? 'Otro alumno' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                @switch($solicitud->estado)
                                    @case('pendiente') bg-yellow-100 text-yellow-800 @break
                                    @case('en_revision') bg-blue-100 text-blue-800 @break
                                    @case('aprobada') bg-green-100 text-green-800 @break
                                    @case('rechazada') bg-red-100 text-red-800 @break
                                    @case('cancelada') bg-gray-100 text-gray-800 @break
                                    @case('trueque_detectado') bg-purple-100 text-purple-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch">
                                {{ $solicitud->estado_nombre }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($solicitud->comisionDestino)
                                @if($solicitud->comisionDestino->esVirtual())
                                    <span class="text-sm text-green-600">Sin límite</span>
                                @elseif($tieneCupos)
                                    <span class="text-sm text-green-600">{{ $cuposDestino }} disponibles</span>
                                @else
                                    <span class="text-sm text-red-600 font-medium">Sin cupos</span>
                                @endif
                            @else
                                <span class="text-sm text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $solicitud->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($solicitud->puedeSerProcesada())
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Botón Aprobar --}}
                                    @if($tieneCupos)
                                        <form action="{{ route('solicitudes.aprobar', $solicitud) }}" method="POST" class="inline"
                                              onsubmit="return confirm('{{ $solicitud->solicitud_trueque_id ? '¿Aprobar este trueque? Ambos alumnos serán cambiados de comisión.' : '¿Aprobar esta solicitud?' }}')">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Aprobar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400" title="Sin cupos disponibles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                    @endif

                                    {{-- Botón Rechazar --}}
                                    <button type="button" onclick="abrirModalRechazo({{ $solicitud->id }})"
                                            class="text-red-600 hover:text-red-900" title="Rechazar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>

                                    {{-- Ver detalle --}}
                                    <a href="{{ route('inscripciones.show', $solicitud->inscripcion_id) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Ver inscripción">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            @else
                                <div class="text-sm text-gray-500">
                                    @if($solicitud->procesadoPor)
                                        Por: {{ $solicitud->procesadoPor->name }}
                                        <br>
                                        {{ $solicitud->fecha_procesamiento?->format('d/m/Y') }}
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-2 text-sm">No hay solicitudes de cambio.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($solicitudes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $solicitudes->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal de Rechazo --}}
<div id="modalRechazo" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Rechazar Solicitud</h3>
            <form id="formRechazo" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="motivo_rechazo" class="block text-sm font-medium text-gray-700 mb-1">
                        Motivo del rechazo <span class="text-red-500">*</span>
                    </label>
                    <textarea name="motivo_rechazo" id="motivo_rechazo" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Explique el motivo del rechazo..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="cerrarModalRechazo()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Rechazar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalRechazo(solicitudId) {
    document.getElementById('formRechazo').action = '/solicitudes/' + solicitudId + '/rechazar';
    document.getElementById('modalRechazo').classList.remove('hidden');
}

function cerrarModalRechazo() {
    document.getElementById('modalRechazo').classList.add('hidden');
    document.getElementById('motivo_rechazo').value = '';
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalRechazo').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalRechazo();
    }
});
</script>
@endsection
