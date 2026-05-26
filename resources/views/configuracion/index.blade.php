@extends('layouts.app')

@section('title', 'Configuración del Sistema')

@section('content')
<div x-data="configuracionApp()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuración del Sistema</h1>
            <p class="mt-1 text-sm text-gray-500">Gestión centralizada de variables y parámetros de PAICAT.</p>
        </div>
        @if($faltantes->isNotEmpty())
            <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <span class="text-sm text-amber-800 font-medium">{{ $faltantes->count() }} variable(s) requerida(s) sin configurar</span>
            </div>
        @endif
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif
    @if(session('warning'))
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <p class="text-sm text-amber-800">{{ session('warning') }}</p>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Sidebar de grupos --}}
        <div class="lg:w-64 shrink-0">
            <nav class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky top-4">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Categorías</h3>
                </div>
                @foreach($gruposLabels as $key => $label)
                    @if(isset($grupos[$key]))
                        <button
                            @click="activeTab = '{{ $key }}'; window.scrollTo({top: 0, behavior: 'smooth'})"
                            :class="activeTab === '{{ $key }}'
                                ? 'bg-blue-50 text-blue-800 border-l-4 border-blue-800'
                                : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="w-full text-left px-4 py-3 text-sm font-medium flex items-center gap-3 transition-colors">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $gruposIconos[$key] ?? 'M4 6h16M4 12h16M4 18h16' }}"/>
                            </svg>
                            <span>{{ $label }}</span>
                            @php
                                $grupoFaltantes = isset($grupos[$key]) ? $grupos[$key]->where('requerida', true)->filter(fn($v) => empty($v->valor))->count() : 0;
                            @endphp
                            @if($grupoFaltantes > 0)
                                <span class="ml-auto bg-amber-100 text-amber-700 text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ $grupoFaltantes }}</span>
                            @else
                                <span class="ml-auto text-xs text-gray-400">{{ $grupos[$key]->count() }}</span>
                            @endif
                        </button>
                    @endif
                @endforeach
            </nav>
        </div>

        {{-- Contenido principal --}}
        <div class="flex-1 min-w-0">
            @foreach($grupos as $grupoKey => $variables)
                <div x-show="activeTab === '{{ $grupoKey }}'" x-cloak>
                    <div class="mb-4">
                        <h2 class="text-lg font-bold text-gray-900">{{ $gruposLabels[$grupoKey] ?? ucfirst($grupoKey) }}</h2>
                        <p class="text-sm text-gray-500">{{ $variables->count() }} variable(s) en esta categoría</p>
                    </div>

                    <div class="space-y-4">
                        @foreach($variables as $variable)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden
                                {{ $variable->requerida && empty($variable->valor) ? 'ring-2 ring-amber-300' : '' }}">

                                {{-- Header de la variable --}}
                                <div class="px-5 py-4 border-b border-gray-100">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="font-semibold text-gray-900">{{ $variable->nombre }}</h3>
                                                @if($variable->requerida)
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full
                                                        {{ empty($variable->valor) ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ empty($variable->valor) ? 'Requerida - Sin configurar' : 'Requerida' }}
                                                    </span>
                                                @endif
                                                @if($variable->tabla_sincronizacion)
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                        Sincronizable
                                                    </span>
                                                @endif
                                            </div>
                                            @if($variable->descripcion)
                                                <p class="text-sm text-gray-500 mt-1">{{ $variable->descripcion }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            {{-- Sincronizar --}}
                                            @if($variable->tabla_sincronizacion)
                                                <form action="{{ route('configuracion.sincronizar', $variable) }}" method="POST"
                                                    data-confirm="¿Sincronizar desde {{ $variable->tabla_sincronizacion }}? Se agregarán los valores encontrados."
                                                    data-confirm-title="Sincronizar variable">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors" title="Sincronizar desde {{ $variable->tabla_sincronizacion }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                        </svg>
                                                        Sincronizar
                                                    </button>
                                                </form>
                                            @endif
                                            {{-- Historial --}}
                                            <button @click="toggleHistorial({{ $variable->id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Historial
                                            </button>
                                        </div>
                                    </div>
                                    {{-- Metadata --}}
                                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                                        <span>Clave: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ $variable->clave }}</code></span>
                                        <span>Tipo: {{ $variable->tipo }}</span>
                                        <span>Origen: {{ $variable->origen }}</span>
                                        @if(isset($historialReciente[$variable->id]))
                                            <span>Último cambio: {{ $historialReciente[$variable->id]->created_at->format('d/m/Y H:i') }}
                                                por {{ $historialReciente[$variable->id]->usuario?->nombre_completo ?? 'Sistema' }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Formulario de edición --}}
                                <div class="px-5 py-4">
                                    <form action="{{ route('configuracion.update', $variable) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        @if($variable->tipo === 'numero')
                                            {{-- Campo numérico --}}
                                            <div class="flex items-end gap-3">
                                                <div class="w-48">
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Valor</label>
                                                    <input type="number" name="valor" value="{{ $variable->valor }}"
                                                        step="any" min="0"
                                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500
                                                            {{ !$variable->editable ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                                        {{ !$variable->editable ? 'disabled' : '' }}>
                                                </div>
                                                <div class="flex-1">
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Motivo del cambio (opcional)</label>
                                                    <input type="text" name="motivo" placeholder="Ej: Ajuste por resolución..."
                                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                @if($variable->editable)
                                                    <button type="submit" class="px-4 py-2 bg-blue-800 text-white text-sm font-medium rounded-md hover:bg-blue-900 transition-colors shrink-0">
                                                        Guardar
                                                    </button>
                                                @endif
                                            </div>

                                        @elseif($variable->tipo === 'texto')
                                            {{-- Campo de texto --}}
                                            <div class="flex items-end gap-3">
                                                <div class="flex-1">
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Valor</label>
                                                    <input type="text" name="valor" value="{{ $variable->valor }}"
                                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500
                                                            {{ !$variable->editable ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                                        {{ !$variable->editable ? 'disabled' : '' }}>
                                                </div>
                                                @if($variable->editable)
                                                    <button type="submit" class="px-4 py-2 bg-blue-800 text-white text-sm font-medium rounded-md hover:bg-blue-900 transition-colors shrink-0">
                                                        Guardar
                                                    </button>
                                                @endif
                                            </div>

                                        @elseif($variable->tipo === 'booleano')
                                            {{-- Toggle --}}
                                            <div class="flex items-center gap-3">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="hidden" name="valor" value="false">
                                                    <input type="checkbox" name="valor" value="true"
                                                        {{ $variable->valor_decodificado ? 'checked' : '' }}
                                                        class="sr-only peer"
                                                        {{ !$variable->editable ? 'disabled' : '' }}
                                                        onchange="this.form.submit()">
                                                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-800 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                                </label>
                                                <span class="text-sm text-gray-700">{{ $variable->valor_decodificado ? 'Activado' : 'Desactivado' }}</span>
                                            </div>

                                        @elseif($variable->tipo === 'json')
                                            {{-- Editor de opciones (solo etiquetas visibles) --}}
                                            @php $decoded = $variable->valor_decodificado; @endphp
                                            <div x-data="opcionesEditor(@js($decoded ?? []))">
                                                <div class="space-y-2">
                                                    <template x-for="(item, index) in opciones" :key="index">
                                                        <div class="flex items-center gap-2">
                                                            {{-- Clave interna oculta: se envía al backend pero no se muestra --}}
                                                            <input type="hidden"
                                                                :name="`opciones[${index}][clave]`"
                                                                :value="item.clave">
                                                            <div class="flex-1">
                                                                <input type="text"
                                                                    :name="`opciones[${index}][valor]`"
                                                                    x-model="item.valor"
                                                                    class="w-full border-gray-300 rounded-md text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                                                    placeholder="Nombre de la opción"
                                                                    {{ !$variable->editable ? 'disabled' : '' }}>
                                                            </div>
                                                            @if($variable->editable)
                                                                <button type="button" @click="removeOpcion(index)"
                                                                    class="text-red-400 hover:text-red-600 transition-colors p-1.5 rounded hover:bg-red-50"
                                                                    title="Eliminar opción">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </template>
                                                </div>

                                                @if($variable->editable)
                                                    <div class="flex items-center justify-between mt-3">
                                                        <button type="button" @click="addOpcion()"
                                                            class="inline-flex items-center gap-1.5 text-sm text-blue-700 hover:text-blue-900 font-medium">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                            </svg>
                                                            Agregar opción
                                                        </button>
                                                        <div class="flex items-center gap-2">
                                                            <input type="text" name="motivo" placeholder="Motivo del cambio (opcional)"
                                                                class="border-gray-300 rounded-md shadow-sm text-xs focus:ring-blue-500 focus:border-blue-500 w-60">
                                                            <button type="submit"
                                                                class="px-4 py-2 bg-blue-800 text-white text-sm font-medium rounded-md hover:bg-blue-900 transition-colors">
                                                                Guardar cambios
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </form>
                                </div>

                                {{-- Panel de historial (inline, colapsable) --}}
                                <div x-show="historialAbierto === {{ $variable->id }}" x-cloak
                                    class="border-t border-gray-200 bg-gray-50 px-5 py-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Historial de cambios
                                    </h4>
                                    <div x-show="historialLoading" class="text-sm text-gray-500 py-2">Cargando...</div>
                                    <div x-show="!historialLoading && historialData.length === 0" class="text-sm text-gray-400 py-2">
                                        Sin cambios registrados.
                                    </div>
                                    <template x-if="!historialLoading && historialData.length > 0">
                                        <div class="space-y-2 max-h-64 overflow-y-auto">
                                            <template x-for="(entry, i) in historialData" :key="i">
                                                <div class="bg-white rounded border border-gray-200 px-3 py-2 text-xs">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <span class="font-medium text-gray-700" x-text="entry.fecha"></span>
                                                        <span class="text-gray-500" x-text="entry.usuario"></span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs"
                                                            :class="{
                                                                'bg-blue-100 text-blue-700': entry.tipo_cambio === 'manual',
                                                                'bg-green-100 text-green-700': entry.tipo_cambio === 'sincronizacion',
                                                                'bg-gray-100 text-gray-600': entry.tipo_cambio === 'seed'
                                                            }"
                                                            x-text="entry.tipo_cambio"></span>
                                                        <span x-show="entry.motivo" class="text-gray-500 italic" x-text="entry.motivo"></span>
                                                    </div>
                                                    <div class="mt-1 flex items-center gap-2 text-gray-500">
                                                        <span class="line-through" x-text="typeof entry.valor_anterior === 'object' ? JSON.stringify(entry.valor_anterior) : entry.valor_anterior"></span>
                                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                        </svg>
                                                        <span class="font-medium text-gray-800" x-text="typeof entry.valor_nuevo === 'object' ? JSON.stringify(entry.valor_nuevo) : entry.valor_nuevo"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function configuracionApp() {
    return {
        activeTab: '{{ session('grupo', array_key_first($grupos->toArray())) }}',
        historialAbierto: null,
        historialLoading: false,
        historialData: [],

        toggleHistorial(variableId) {
            if (this.historialAbierto === variableId) {
                this.historialAbierto = null;
                return;
            }
            this.historialAbierto = variableId;
            this.historialLoading = true;
            this.historialData = [];

            fetch(`/configuracion/${variableId}/historial`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(data => {
                this.historialData = data;
                this.historialLoading = false;
            })
            .catch(() => {
                this.historialLoading = false;
            });
        }
    }
}

function opcionesEditor(initialData) {
    const opciones = [];
    if (initialData && typeof initialData === 'object') {
        for (const [k, v] of Object.entries(initialData)) {
            opciones.push({ clave: k, valor: v });
        }
    }
    return {
        opciones: opciones,
        addOpcion() {
            this.opciones.push({ clave: '', valor: '' });
        },
        removeOpcion(index) {
            this.opciones.splice(index, 1);
        }
    };
}
</script>
@endpush
