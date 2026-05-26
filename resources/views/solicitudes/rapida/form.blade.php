@extends('layouts.app')

@section('title', 'Solicitar Cambio de Comisión')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-5">
        <a href="{{ route('solicitud-cambio.index') }}" class="hover:text-utn-blue-dark transition-colors">
            Cambio de Comisión
        </a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-800 font-medium">
            {{ $persona?->apellido ?? '—' }}, {{ $persona?->nombre ?? '—' }}
        </span>
    </div>

    {{-- Header del alumno --}}
    <div class="bg-white shadow-md rounded-xl overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-utn-dark to-utn-dark-light px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-utn-blue flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-white">
                        {{ substr($persona?->apellido ?? 'A', 0, 1) }}
                    </span>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white">
                        {{ $persona?->apellido ?? '—' }}, {{ $persona?->nombre ?? '—' }}
                    </h1>
                    <p class="text-white/70 text-sm">DNI: {{ $persona?->documento ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($tieneSolicitudPendiente)
    {{-- Ya tiene solicitud pendiente --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
        <svg class="mx-auto h-10 w-10 text-yellow-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="font-semibold text-yellow-800">Este alumno ya tiene una solicitud de cambio en proceso.</p>
        <p class="text-sm text-yellow-700 mt-1">No se pueden crear nuevas solicitudes mientras haya una pendiente.</p>
        <div class="mt-4 flex justify-center gap-3">
            <a href="{{ route('solicitud-cambio.index') }}"
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                Volver
            </a>
            <a href="{{ route('inscripciones.show', $inscripcion) }}"
               class="px-4 py-2 bg-utn-blue-darker text-white rounded-lg hover:bg-utn-dark text-sm font-medium transition-colors">
                Ver ficha del alumno
            </a>
        </div>
    </div>

    @elseif(!$comisionActual)
    {{-- Sin comisión activa --}}
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center">
        <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="font-semibold text-gray-700">El alumno no tiene una comisión activa asignada.</p>
        <p class="text-sm text-gray-500 mt-1">Primero debe asignarse a una comisión para poder solicitar un cambio.</p>
        <a href="{{ route('solicitud-cambio.index') }}"
           class="mt-4 inline-block px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
            Volver
        </a>
    </div>

    @else
    {{-- Formulario de cambio --}}
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <div class="px-5 py-4 bg-gray-50 border-b flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <h2 class="text-base font-semibold text-gray-800">Solicitud de Cambio de Comisión</h2>
        </div>

        <div class="p-6 space-y-5">

            {{-- Comisión actual --}}
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-xs font-semibold text-utn-blue-dark uppercase tracking-wide mb-1">Comisión actual</p>
                <p class="text-sm font-medium text-gray-900">{{ $comisionActual->nombre }}</p>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $comisionActual->turno ? ucfirst($comisionActual->turno) . ' · ' : '' }}{{ $comisionActual->modalidad ?? '' }}
                    {{ $comisionActual->periodo ? ' · ' . $comisionActual->periodo : '' }}
                </p>
            </div>

            <form action="{{ route('solicitud-cambio.store', $inscripcion) }}" method="POST">
                @csrf
                <input type="hidden" name="tipo" value="comision">

                {{-- Errores de validación --}}
                @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="space-y-5">
                    {{-- Select de comisión destino --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Comisión Destino <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="comision_destino_id"
                            id="comisionDestinoSelect"
                            required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('comision_destino_id') border-red-500 @enderror"
                        >
                            <option value="">Seleccionar comisión destino...</option>
                            @forelse($comisionesDisponibles as $comisionDisp)
                                <option
                                    value="{{ $comisionDisp->id }}"
                                    data-cupos="{{ $comisionDisp->cupos_disponibles }}"
                                    data-virtual="{{ $comisionDisp->esVirtual() ? '1' : '0' }}"
                                    data-turno="{{ $comisionDisp->turno }}"
                                    data-modalidad="{{ $comisionDisp->modalidad }}"
                                    data-periodo="{{ $comisionDisp->periodo }}"
                                    {{ old('comision_destino_id') == $comisionDisp->id ? 'selected' : '' }}
                                >
                                    {{ $comisionDisp->nombre }}
                                    — {{ $comisionDisp->turno ? ucfirst($comisionDisp->turno) . ' · ' : '' }}{{ $comisionDisp->modalidad ?? '' }}
                                    @if($comisionDisp->esVirtual())
                                        (Sin límite de cupo)
                                    @elseif($comisionDisp->cupos_disponibles !== null)
                                        ({{ $comisionDisp->cupos_disponibles }} cupos disponibles)
                                    @endif
                                </option>
                            @empty
                                <option disabled>No hay comisiones con cupo disponible</option>
                            @endforelse
                        </select>

                        {{-- Alerta cupos bajos --}}
                        <p id="alertaCupos" class="hidden mt-1.5 text-sm text-orange-600 flex items-center gap-1">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Esta comisión tiene pocos cupos disponibles.
                        </p>

                        {{-- Info de cambios de modalidad/turno --}}
                        <div id="infoCambios" class="hidden mt-2 p-3 bg-amber-50 rounded-lg border border-amber-200">
                            <p class="text-xs font-medium text-amber-800 mb-1">Al cambiar a esta comisión se actualizarán:</p>
                            <ul id="listaCambios" class="text-xs text-amber-700 space-y-0.5"></ul>
                        </div>
                    </div>

                    {{-- Motivo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Motivo del cambio <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="motivo"
                            required
                            rows="3"
                            minlength="10"
                            placeholder="Explicá el motivo del cambio (mínimo 10 caracteres)..."
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('motivo') border-red-500 @enderror"
                        >{{ old('motivo') }}</textarea>
                        @error('motivo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-100">
                    <a href="{{ route('solicitud-cambio.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-utn-blue-darker text-white rounded-lg hover:bg-utn-dark text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Enviar solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
(function () {
    const select = document.getElementById('comisionDestinoSelect');
    if (!select) return;

    const alertaCupos = document.getElementById('alertaCupos');
    const infoCambios = document.getElementById('infoCambios');
    const listaCambios = document.getElementById('listaCambios');

    const turnoActual = @json($comisionActual->turno ?? '');
    const modalidadActual = @json($comisionActual->modalidad ?? '');
    const periodoActual = @json($comisionActual->periodo ?? '');

    function actualizarInfo() {
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) {
            alertaCupos.classList.add('hidden');
            infoCambios.classList.add('hidden');
            return;
        }

        const cupos = parseInt(opt.dataset.cupos ?? '0');
        const esVirtual = opt.dataset.virtual === '1';
        alertaCupos.classList.toggle('hidden', esVirtual || cupos > 3);

        const cambios = [];
        if (opt.dataset.turno && opt.dataset.turno !== turnoActual) {
            cambios.push('Turno: ' + turnoActual + ' → ' + opt.dataset.turno);
        }
        if (opt.dataset.modalidad && opt.dataset.modalidad !== modalidadActual) {
            cambios.push('Modalidad: ' + modalidadActual + ' → ' + opt.dataset.modalidad);
        }
        if (opt.dataset.periodo && opt.dataset.periodo !== periodoActual) {
            cambios.push('Período: ' + periodoActual + ' → ' + opt.dataset.periodo);
        }

        listaCambios.textContent = '';
        if (cambios.length > 0) {
            cambios.forEach(function (texto) {
                const li = document.createElement('li');
                li.textContent = '• ' + texto;
                listaCambios.appendChild(li);
            });
            infoCambios.classList.remove('hidden');
        } else {
            infoCambios.classList.add('hidden');
        }
    }

    select.addEventListener('change', actualizarInfo);

    // Triggerear si hay valor previo (old input tras error de validación)
    if (select.value) actualizarInfo();
})();
</script>
@endpush
