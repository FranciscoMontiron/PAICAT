@extends('layouts.app')
@section('title', 'Solicitudes de Cambio')
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
                <h1 class="text-2xl font-bold text-gray-900">Solicitudes de Cambio</h1>
                <p class="text-gray-600">Gestión de solicitudes de cambio de comisión, modalidad y turno</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pendientes</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pendientes'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Trueques Detectados</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['trueques'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Por Procesar Hoy</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ ($stats['pendientes'] ?? 0) + ($stats['trueques'] ?? 0) }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="trueque_detectado" {{ request('estado') == 'trueque_detectado' ? 'selected' : '' }}>Trueque Detectado</option>
                    <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="tipo" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Todos</option>
                    <option value="comision" {{ request('tipo') == 'comision' ? 'selected' : '' }}>Comisión</option>
                    <option value="modalidad" {{ request('tipo') == 'modalidad' ? 'selected' : '' }}>Modalidad</option>
                    <option value="turno" {{ request('tipo') == 'turno' ? 'selected' : '' }}>Turno</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                Filtrar
            </button>
            @if(request()->hasAny(['estado', 'tipo']))
            <a href="{{ route('solicitudes.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">
                Limpiar
            </a>
            @endif
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($solicitudes as $solicitud)
                    @php
                    $person = $solicitud->inscripcion?->getPerson();
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $solicitud->estado == 'trueque_detectado' ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-indigo-600">{{ substr($person->nombre ?? 'N', 0, 1) }}{{ substr($person->apellido ?? 'A', 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $person->nombre ?? 'N/A' }} {{ $person->apellido ?? '' }}</div>
                                    <div class="text-sm text-gray-500">DNI: {{ $person->documento ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $solicitud->tipo == 'comision' ? 'bg-purple-100 text-purple-800' : 
                                       ($solicitud->tipo == 'modalidad' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($solicitud->tipo) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($solicitud->tipo == 'comision')
                            {{ $solicitud->comisionOrigen?->nombre ?? 'N/A' }} → {{ $solicitud->comisionDestino?->nombre ?? 'N/A' }}
                            @elseif($solicitud->tipo == 'modalidad')
                            {{ $solicitud->modalidad_origen ?? 'N/A' }} → {{ $solicitud->modalidad_destino ?? 'N/A' }}
                            @elseif($solicitud->tipo == 'turno')
                            {{ $solicitud->turno_origen ?? 'N/A' }} → {{ $solicitud->turno_destino ?? 'N/A' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $estadoColors = [
                            'pendiente' => 'bg-yellow-100 text-yellow-800',
                            'en_revision' => 'bg-orange-100 text-orange-800',
                            'aprobada' => 'bg-green-100 text-green-800',
                            'rechazada' => 'bg-red-100 text-red-800',
                            'trueque_detectado' => 'bg-blue-100 text-blue-800',
                            ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $estadoColors[$solicitud->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                @if($solicitud->estado == 'trueque_detectado')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                </svg>
                                @endif
                                {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $solicitud->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if(in_array($solicitud->estado, ['pendiente', 'trueque_detectado']))
                            <button onclick="openModal('{{ $solicitud->id }}')" class="text-green-600 hover:text-green-900 mr-2">
                                Aprobar
                            </button>
                            <button onclick="openRejectModal('{{ $solicitud->id }}')" class="text-red-600 hover:text-red-900">
                                Rechazar
                            </button>
                            @else
                            <span class="text-gray-400">Procesada</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No hay solicitudes con los filtros aplicados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($solicitudes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $solicitudes->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal Aprobar --}}
<div id="modalAprobar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-green-600 mb-4">Aprobar Solicitud</h3>
        <p class="text-gray-600 mb-4">¿Está seguro de aprobar esta solicitud? Se aplicarán los cambios automáticamente.</p>
        <form id="formAprobar" method="POST">
            @csrf
            <input type="hidden" name="accion" value="aprobar">
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalAprobar')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Confirmar Aprobación</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Rechazar --}}
<div id="modalRechazar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-red-600 mb-4">Rechazar Solicitud</h3>
        <form id="formRechazar" method="POST">
            @csrf
            <input type="hidden" name="accion" value="rechazar">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo del Rechazo</label>
                <textarea name="motivo_rechazo" required rows="3" class="w-full rounded-lg border-gray-300" placeholder="Explique el motivo del rechazo..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalRechazar')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Confirmar Rechazo</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById('formAprobar').action = `/solicitudes/${id}/procesar`;
        document.getElementById('modalAprobar').classList.remove('hidden');
    }

    function openRejectModal(id) {
        document.getElementById('formRechazar').action = `/solicitudes/${id}/procesar`;
        document.getElementById('modalRechazar').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endpush
@endsection