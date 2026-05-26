@extends('layouts.app')
@section('title', 'Difusiones')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ 
        showForm: false, 
        selectedModalidad: '',
        asistenciaOperador: '>=',
        asistenciaPorcentaje: '',
        esRango: false,
        asistenciaDesde: '',
        asistenciaHasta: '',
        showConfirmModal: false,
        formData: {},
        resetForm() {
            this.showForm = false;
            this.selectedModalidad = '';
            this.asistenciaOperador = '>=';
            this.asistenciaPorcentaje = '';
            this.esRango = false;
            this.asistenciaDesde = '';
            this.asistenciaHasta = '';
            document.querySelector('input[name=asunto]').value = '';
            document.querySelector('textarea[name=mensaje]').value = '';
        },
        toggleForm() {
            if (this.showForm) {
                this.resetForm();
            } else {
                this.showForm = true;
            }
        },
        showPreview(event) {
            event.preventDefault();
            this.formData = {
                modalidad: this.selectedModalidad || 'Sin especificar',
                asunto: document.querySelector('input[name=asunto]').value,
                mensaje: document.querySelector('textarea[name=mensaje]').value,
                asistencia: this.esRango 
                    ? `Entre ${this.asistenciaDesde}% y ${this.asistenciaHasta}%`
                    : `${this.asistenciaOperador} ${this.asistenciaPorcentaje}%`
            };
            this.showConfirmModal = true;
        },
        confirmSubmit() {
            this.showConfirmModal = false;
            document.getElementById('difusion-form').submit();
        }
    }">
    <!-- Alertas -->
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <ul class="text-red-700">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Difusiones</h1>
            <p class="text-gray-600 mt-1">Permite generar nuevas difusiones por mail</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="toggleForm()" type="button" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path :d="showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
                <span x-text="showForm ? 'Cancelar' : 'Nueva Difusión'"></span>
            </button>
        </div>
    </div>

    <!-- Formulario de Redacción (Se muestra/oculta con Alpine) -->
    <div x-show="showForm" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="mb-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-700">Redactar Comunicado Masivo</h3>
        </div>

        <form id="difusion-form" @submit="showPreview($event)" action="{{ route('difusiones.create') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modalidad</label>
                    <select name="modalidad" x-model="selectedModalidad"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">-- Seleccionar modalidad --</option>
                        <option value="Presencial">Presencial</option>
                        <option value="Semipresencial">Semipresencial</option>
                        <option value="Virtual">Virtual</option>
                    </select>
                </div>
                <div x-show="!esRango">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Condición de Asistencia</label>
                    <select name="asistencia_operador" x-model="asistenciaOperador"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value=">=">Mayor o igual (≥)</option>
                        <option value=">">Mayor que (>)</option>
                        <option value="<=">Menor o igual (≤)</option>
                        <option value="<">Menor que (<)</option>
                        <option value="=">Igual (=)</option>
                    </select>
                </div>
                <div x-show="!esRango">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Porcentaje (%)</label>
                    <input type="number" name="asistencia_porcentaje" x-model="asistenciaPorcentaje" min="0" max="100" step="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                        placeholder="Ej: 80">
                </div>
                <div x-show="esRango">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desde (%)</label>
                    <input type="number" name="asistencia_desde" x-model="asistenciaDesde" min="0" max="100" step="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                        placeholder="Ej: 60">
                </div>
                <div x-show="esRango">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hasta (%)</label>
                    <input type="number" name="asistencia_hasta" x-model="asistenciaHasta" min="0" max="100" step="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                        placeholder="Ej: 90">
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <input type="checkbox" id="esRango" x-model="esRango" 
                    class="h-4 w-4 border-gray-300 rounded focus:ring-green-500 cursor-pointer">
                <label for="esRango" class="text-sm font-medium text-gray-700 cursor-pointer">
                    Usar rango de asistencia
                </label>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                <input type="text" name="asunto" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                    placeholder="Ej: Información sobre el examen de ingreso">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                <textarea name="mensaje" rows="5" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                    placeholder="Escriba el contenido aquí..."></textarea>
            </div>


            <div class="flex justify-end space-x-3">
                <button type="button" @click="resetForm()" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                    Descartar
                </button>
                <button type="submit" class="bg-utn-blue hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm transition"
                    x-text="selectedModalidad ? `Enviar al grupo ${selectedModalidad}` : 'Enviar a cada estudiante'">
                </button>
            </div>
        </form>
    </div>

    <!-- Modal de Confirmación -->
    <div x-show="showConfirmModal" 
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="bg-utn-blue px-6 py-4 border-b border-gray-200">
                <h3 class="text-white text-lg font-bold text-gray-900">Confirmar envío de difusión</h3>
            </div>
            <div class="h-1 bg-utn-orange"></div>
            <div class="p-6 space-y-4">
                <div class="border-l-4 border-blue-500 bg-blue-50 p-4">
                    <p class="text-sm text-blue-900">Revisa los detalles antes de confirmar el envío</p>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Modalidad</label>
                        <p class="text-gray-900 font-medium" x-text="formData.modalidad"></p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Asunto</label>
                        <p class="text-gray-900" x-text="formData.asunto"></p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Filtro de Asistencia</label>
                        <p class="text-gray-900" x-text="formData.asistencia"></p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Mensaje</label>
                        <div class="bg-gray-50 p-3 rounded text-sm text-gray-800 max-h-32 overflow-y-auto whitespace-pre-wrap" x-text="formData.mensaje"></div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" @click="showConfirmModal = false" 
                    class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium">
                    Cancelar
                </button>
                <button type="button" @click="confirmSubmit()" 
                    class="px-4 py-2 text-white bg-green-600 hover:bg-green-700 rounded-md text-sm font-medium">
                    Confirmar envío
                </button>
            </div>
        </div>
    </div>

</div>
@endsection