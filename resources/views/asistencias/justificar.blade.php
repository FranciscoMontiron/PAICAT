@extends('layouts.app')
@section('title','Justificar Inasistencia')
@section('content')
<div class="container mx-auto p-4">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
        <a href="{{ route('asistencias.index') }}" class="hover:text-blue-600">Asistencias</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('asistencias.comision', $comision->id) }}" class="hover:text-blue-600">{{ $comision->nombre }}</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-800 font-medium">Justificar Inasistencia</span>
    </div>

    <!-- Mensajes de éxito -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Errores de validación -->
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
            <div class="flex items-start">
                <svg class="h-6 w-6 text-red-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-red-800 font-semibold mb-2">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white border-b border-gray-200 shadow-sm sm:rounded-lg">
        <div class="p-6">
            
            <!-- Encabezado -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-utn-blue mb-1">Justificar Inasistencia</h1>
                    <p class="text-gray-600">{{ $comision->nombre }} - {{ $comision->turno }}</p>
                </div>
                <a href="{{ route('asistencias.comision', $comision->id) }}" 
                   class="flex items-center gap-2 text-gray-600 hover:text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>
            </div>

            <!-- Información importante -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <div class="flex items-start">
                    <svg class="h-6 w-6 text-blue-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-blue-800 font-semibold">Importante</p>
                        <p class="text-blue-700 text-sm mt-1">
                            Las justificaciones deben presentarse con documentación respaldatoria (certificado médico, nota de padres, etc.). 
                            El archivo adjunto es opcional pero recomendado.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ route('asistencias.justificar', $comision->id) }}" enctype="multipart/form-data" id="justificacionForm">
                @csrf

                <div class="space-y-6">
                    
                    <!-- Selección de alumno -->
                    <div>
                        <label for="inscripcion_comision_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            Seleccionar Alumno *
                        </label>
                        <select name="inscripcion_comision_id" 
                                id="inscripcion_comision_id" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">-- Seleccione un alumno --</option>
                            @foreach($alumnos as $alumno)
                                @php
                                    $nombreAlumno = $alumno->academicoDato->user->name ?? $alumno->alumno->name ?? 'Sin nombre';
                                    $estaEnRiesgo = $alumno->estaEnRiesgo();
                                @endphp
                                <option value="{{ $alumno->id }}" {{ old('inscripcion_comision_id') == $alumno->id ? 'selected' : '' }}>
                                    {{ $nombreAlumno }}
                                    @if($estaEnRiesgo) - ⚠ EN RIESGO @endif
                                </option>
                            @endforeach
                        </select>
                        @error('inscripcion_comision_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de la inasistencia -->
                    <div>
                        <label for="fecha" class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            Fecha de la Inasistencia *
                        </label>
                        <input type="date" 
                               name="fecha" 
                               id="fecha" 
                               value="{{ old('fecha', today()->format('Y-m-d')) }}" 
                               max="{{ today()->format('Y-m-d') }}"
                               class="w-full md:w-auto border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('fecha')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Motivo de la justificación -->
                    <div>
                        <label for="motivo" class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Motivo de la Justificación *
                        </label>
                        <textarea name="motivo" 
                                  id="motivo" 
                                  rows="4" 
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y"
                                  placeholder="Ej: Certificado médico por enfermedad. Presentó comprobante del Dr. Juan Pérez."
                                  maxlength="500"
                                  required>{{ old('motivo') }}</textarea>
                        <div class="flex justify-between items-center mt-1">
                            <p class="text-sm text-gray-500">Máximo 500 caracteres</p>
                            <p class="text-sm text-gray-500" id="charCount">0 / 500</p>
                        </div>
                        @error('motivo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Archivo adjunto -->
                    <div>
                        <label for="archivo" class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
                            </svg>
                            Archivo Adjunto (Opcional)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <input type="file" 
                                   name="archivo" 
                                   id="archivo" 
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   class="hidden"
                                   onchange="updateFileName(this)">
                            <label for="archivo" class="cursor-pointer">
                                <span class="text-blue-600 hover:text-blue-700 font-medium">Haz clic para seleccionar</span>
                                <span class="text-gray-600"> o arrastra un archivo</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-2">
                                PDF, JPG, PNG, DOC, DOCX (máx. 2MB)
                            </p>
                            <p id="fileName" class="text-sm text-gray-700 font-medium mt-2"></p>
                        </div>
                        @error('archivo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Botones de acción -->
                <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200">
                    <a href="{{ route('asistencias.comision', $comision->id) }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition font-medium shadow-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Guardar Justificación
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
    // Contador de caracteres para el motivo
    const motivoTextarea = document.getElementById('motivo');
    const charCount = document.getElementById('charCount');
    
    motivoTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = `${count} / 500`;
        
        if (count > 450) {
            charCount.classList.add('text-red-600');
        } else {
            charCount.classList.remove('text-red-600');
        }
    });

    // Mostrar nombre del archivo seleccionado
    function updateFileName(input) {
        const fileName = document.getElementById('fileName');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const size = (file.size / 1024 / 1024).toFixed(2); // MB
            fileName.textContent = `📎 ${file.name} (${size} MB)`;
            fileName.classList.add('text-green-600');
        } else {
            fileName.textContent = '';
        }
    }

    // Drag and drop para el archivo
    const dropZone = document.querySelector('.border-dashed');
    const fileInput = document.getElementById('archivo');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        });
    });

    dropZone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        fileInput.files = files;
        updateFileName(fileInput);
    });
</script>

@endsection