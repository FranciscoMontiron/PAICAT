// resources/views/asistencias/justificar.blade.php
@extends('layouts.app')
@section('title','Justificar Inasistencia')
@section('content')
@php $asistenciaMinima = \App\Services\ConfiguracionService::get('asistencia_minima', 75); @endphp
<div class="container mx-auto p-4">

    <!-- Header con contexto -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-file-medical-alt mr-2"></i>Justificar Inasistencias
        </h1>
        
        <!-- Información de contexto -->
        <div class="mt-4 bg-white rounded-lg shadow-md p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-3 rounded">
                    <p class="text-xs text-utn-blue-dark font-semibold">COMISIÓN</p>
                    <p class="text-lg font-bold text-gray-800">{{ $comision->nombre }}</p>
                    <p class="text-sm text-gray-600">Código: {{ $comision->codigo }}</p>
                </div>
                
                <div class="bg-green-50 p-3 rounded">
                    <p class="text-xs text-green-600 font-semibold">ALUMNO</p>
                    <p class="text-lg font-bold text-gray-800">
                        {{ $alumnoNombre ?? 'Alumno no identificado' }}
                    </p>
                    <p class="text-sm text-gray-600">Documento: {{ $alumnoDocumento ?? 'N/A' }}</p>
                </div>
                
                @if(isset($materia))
                <div class="bg-purple-50 p-3 rounded">
                    <p class="text-xs text-purple-600 font-semibold">MATERIA</p>
                    <p class="text-lg font-bold text-gray-800">{{ $materia->nombre }}</p>
                    <p class="text-sm text-gray-600">Código: {{ $materia->codigo }}</p>
                </div>
                @else
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-xs text-gray-600 font-semibold">MATERIA</p>
                    <p class="text-lg font-bold text-gray-800">Todas las materias</p>
                    <p class="text-sm text-gray-600">Inasistencias generales</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
        <a href="{{ route('asistencias.index') }}" class="hover:text-utn-blue-dark">Asistencias</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('asistencias.comision.materias', $comision) }}" class="hover:text-utn-blue-dark">{{ $comision->nombre }}</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        @if(isset($materia))
        <a href="{{ route('asistencias.materia.historial', [$comision, $materia]) }}" class="hover:text-utn-blue-dark">{{ $materia->nombre }}</a>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        @endif
        <span class="text-gray-800 font-medium">Justificar Inasistencia</span>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Total de Ausencias</p>
                <p class="text-2xl font-bold text-red-600">{{ $totalAusencias ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Período</p>
                <p class="text-2xl font-bold text-utn-blue-dark">
                    @if($ausencias->count() > 0)
                        {{ $ausencias->first()->fecha->format('d/m/Y') }}
                        @if($ausencias->count() > 1)
                         - {{ $ausencias->last()->fecha->format('d/m/Y') }}
                        @endif
                    @else
                        N/A
                    @endif
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">% Asistencia</p>
                <p class="text-2xl font-bold {{ ($porcentajeAsistencia ?? 0) < $asistenciaMinima ? 'text-red-600' : 'text-green-600' }}">
                    {{ round($porcentajeAsistencia ?? 0, 1) }}%
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Estado</p>
                <p class="text-2xl font-bold {{ ($porcentajeAsistencia ?? 0) < $asistenciaMinima ? 'text-red-600' : 'text-green-600' }}">
                    @if(($porcentajeAsistencia ?? 0) < $asistenciaMinima)
                        EN RIESGO
                    @else
                        REGULAR
                    @endif
                </p>
            </div>
        </div>
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

    <!-- Información importante -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex items-start">
            <svg class="h-6 w-6 text-utn-blue-dark mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-utn-blue-dark font-semibold">Instrucciones de Justificación</p>
                <p class="text-utn-blue-dark text-sm mt-1">
                    1. Selecciona las fechas de ausencia que deseas justificar.<br>
                    2. Proporciona un motivo claro y detallado para la justificación.<br>
                    3. Puedes adjuntar un archivo de respaldo (ej: certificado médico).<br>
                    4. Las fechas justificadas cambiarán de "Ausente" a "Justificado".
                </p>
            </div>
        </div>
    </div>

    @if($ausencias->count() > 0)
        <!-- Formulario -->
        <form method="POST" action="{{ route('asistencias.alumno.justificar.store', [$comision, $inscripcion]) }}" enctype="multipart/form-data" id="justificarForm">
            @csrf
            
            <!-- Campo oculto para materia_id si existe -->
            @if(isset($materia))
                <input type="hidden" name="materia_id" value="{{ $materia->id }}">
            @endif

            <!-- Lista de Ausencias -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Ausencias Registradas</h2>
                        <p class="text-sm text-gray-600">
                            {{ $ausencias->count() }} ausencias encontradas
                            @if(isset($materia))
                                en {{ $materia->nombre }}
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="seleccionarTodas()" class="bg-utn-blue-darker hover:bg-utn-dark-light text-white px-3 py-1 rounded text-sm font-medium">
                            <i class="fas fa-check-square mr-1"></i> Seleccionar Todas
                        </button>
                        <button type="button" onclick="deseleccionarTodas()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded text-sm font-medium">
                            <i class="fas fa-times-circle mr-1"></i> Deseleccionar Todas
                        </button>
                    </div>
                </div>
                <div class="p-6 space-y-3">
                    @foreach($ausencias as $ausencia)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="asistencias_ids[]" value="{{ $ausencia->id }}" class="w-5 h-5 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $ausencia->fecha->format('l, d/m/Y') }}
                                                @if($ausencia->materia_id && isset($materiasNombres[$ausencia->materia_id]))
                                                    <span class="ml-2 text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">
                                                        {{ $materiasNombres[$ausencia->materia_id] }}
                                                    </span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $ausencia->fecha->diffForHumans() }}</p>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                            <i class="fas fa-times mr-1"></i> Ausente
                                        </span>
                                    </div>
                                    @if($ausencia->observaciones)
                                        <p class="text-sm text-gray-600 mt-2">
                                            <span class="font-medium">Observación original:</span> {{ $ausencia->observaciones }}
                                        </p>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Motivo y Archivo -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6 p-6">
                <div class="mb-4">
                    <label for="observaciones" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-comment-alt mr-1"></i> Motivo de la Justificación *
                    </label>
                    <textarea name="observaciones" id="observaciones" rows="4" class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500" placeholder="Ej: El alumno presentó certificado médico que acredita enfermedad desde el [fecha] hasta el [fecha]. Certificado N° [número] expedido por [médico/clínica]." required>{{ old('observaciones') }}</textarea>
                    <p class="text-sm text-gray-500 mt-2">Este motivo se aplicará a todas las fechas seleccionadas.</p>
                    @error('observaciones') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    @error('asistencias_ids') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="archivo" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-paperclip mr-1"></i> Archivo Adjunto (Opcional)
                    </label>
                    <input type="file" name="archivo" id="archivo" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-utn-blue-dark hover:file:bg-utn-blue/10">
                    <p class="text-xs text-gray-500 mt-2">Formatos aceptados: PDF, JPG, JPEG, PNG, DOC, DOCX (Máx. 5MB)</p>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                <div>
                    <a href="{{ isset($materia) ? route('asistencias.materia.historial', [$comision, $materia]) : route('asistencias.alumno.historial', [$comision, $inscripcion]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        <i class="fas fa-arrow-left mr-2"></i> Cancelar
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <span id="contadorSeleccionadas" class="text-sm text-gray-600">
                        <span class="font-semibold">0</span> ausencias seleccionadas
                    </span>
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 font-medium">
                        <i class="fas fa-check-circle mr-2"></i> Justificar Seleccionadas
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="text-green-500 text-6xl mb-4">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="mt-4 text-2xl font-medium text-gray-900">¡No hay ausencias para justificar!</h3>
            <p class="mt-2 text-lg text-gray-600">
                @if(isset($materia))
                    El alumno no tiene ausencias sin justificar en {{ $materia->nombre }}.
                @else
                    El alumno no tiene ausencias sin justificar registradas.
                @endif
            </p>
            <div class="mt-8">
                <a href="{{ isset($materia) ? route('asistencias.materia.historial', [$comision, $materia]) : route('asistencias.alumno.historial', [$comision, $inscripcion]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-utn-blue-darker text-white rounded-lg hover:bg-utn-dark-light text-lg">
                    <i class="fas fa-history mr-2"></i> Ver Historial Completo
                </a>
            </div>
        </div>
    @endif
</div>

<script>
// Funciones de selección
function seleccionarTodas() {
    document.querySelectorAll('input[name="asistencias_ids[]"]').forEach(c => c.checked = true);
    actualizarContador();
}
function deseleccionarTodas() {
    document.querySelectorAll('input[name="asistencias_ids[]"]').forEach(c => c.checked = false);
    actualizarContador();
}

// Actualizar contador
function actualizarContador() {
    const seleccionadas = document.querySelectorAll('input[name="asistencias_ids[]"]:checked').length;
    const contador = document.getElementById('contadorSeleccionadas');
    contador.innerHTML = `<span class="font-semibold">${seleccionadas}</span> ausencia(s) seleccionada(s)`;
    
    // Cambiar color si hay selecciones
    if (seleccionadas > 0) {
        contador.classList.remove('text-gray-600');
        contador.classList.add('text-green-600');
    } else {
        contador.classList.remove('text-green-600');
        contador.classList.add('text-gray-600');
    }
}

// Contador en tiempo real
document.querySelectorAll('input[name="asistencias_ids[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', actualizarContador);
});

// Validación del formulario
document.getElementById('justificarForm')?.addEventListener('submit', async function(e) {
    const checkboxes = document.querySelectorAll('input[name="asistencias_ids[]"]:checked');
    const observacion = document.getElementById('observaciones').value.trim();
    
    if (checkboxes.length === 0) {
        e.preventDefault();
        alert('⚠️ Debes seleccionar al menos una fecha para justificar.');
        return;
    }
    
    if (observacion.length < 10) {
        e.preventDefault();
        alert('⚠️ El motivo de justificación debe tener al menos 10 caracteres.');
        document.getElementById('observaciones').focus();
        return;
    }

    if (this.dataset.confirmBypassed) return;
    e.preventDefault();
    const ok = await paiConfirm({
        title: 'Confirmar justificación',
        message: `¿Confirmar justificación de ${checkboxes.length} ausencia(s)? Esta acción cambiará el estado de "Ausente" a "Justificado".`
    });
    if (ok) {
        this.dataset.confirmBypassed = 'true';
        this.requestSubmit ? this.requestSubmit() : this.submit();
        delete this.dataset.confirmBypassed;
    }
});

// Inicializar contador
actualizarContador();
</script>

@endsection