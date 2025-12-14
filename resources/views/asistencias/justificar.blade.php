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

    <!-- Información importante -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex items-start">
            <svg class="h-6 w-6 text-blue-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-blue-800 font-semibold">Importante</p>
                <p class="text-blue-700 text-sm mt-1">
                    Selecciona las fechas de ausencia que deseas justificar y proporciona el motivo. El archivo adjunto es opcional.
                </p>
            </div>
        </div>
    </div>

    @if($ausencias->count() > 0)
        <!-- Formulario -->
        <form method="POST" action="{{ route('asistencias.alumno.justificar.store', [$comision, $inscripcion]) }}" enctype="multipart/form-data" id="justificarForm">
            @csrf

            <!-- Lista de Ausencias -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Ausencias Registradas ({{ $ausencias->count() }})</h2>
                    <div class="flex gap-2">
                        <button type="button" onclick="seleccionarTodas()" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded text-sm font-medium">Seleccionar Todas</button>
                        <button type="button" onclick="deseleccionarTodas()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded text-sm font-medium">Deseleccionar Todas</button>
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
                                            <p class="text-sm font-medium text-gray-900">{{ $ausencia->fecha->format('l, d/m/Y') }}</p>
                                            <p class="text-xs text-gray-500">{{ $ausencia->fecha->diffForHumans() }}</p>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">Ausente</span>
                                    </div>
                                    @if($ausencia->observaciones)
                                        <p class="text-sm text-gray-600 mt-2"><span class="font-medium">Obs:</span> {{ $ausencia->observaciones }}</p>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Motivo y Archivo -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6 p-6">
                <label for="observaciones" class="block text-sm font-semibold text-gray-700 mb-2">Motivo de la Justificación *</label>
                <textarea name="observaciones" id="observaciones" rows="4" class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500" placeholder="Ej: Certificado médico presentado" required>{{ old('observaciones') }}</textarea>
                <p class="text-sm text-gray-500 mt-2">Este motivo se aplicará a todas las fechas seleccionadas.</p>
                @error('observaciones') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                @error('asistencias_ids') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

                <label for="archivo" class="block text-sm font-semibold text-gray-700 mt-4">Archivo Adjunto (Opcional)</label>
                <input type="file" name="archivo" id="archivo" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="mt-1">
            </div>

            <!-- Botones -->
            <div class="flex justify-between items-center">
                <a href="{{ route('asistencias.alumno.historial', [$comision, $inscripcion]) }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-medium">Justificar Seleccionadas</button>
            </div>
        </form>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No hay ausencias para justificar</h3>
            <p class="mt-2 text-sm text-gray-500">Este alumno no tiene ausencias sin justificar registradas.</p>
            <div class="mt-6">
                <a href="{{ route('asistencias.alumno.historial', [$comision, $inscripcion]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Ver Historial</a>
            </div>
        </div>
    @endif
</div>

<script>
function seleccionarTodas() {
    document.querySelectorAll('input[name="asistencias_ids[]"]').forEach(c => c.checked = true);
}
function deseleccionarTodas() {
    document.querySelectorAll('input[name="asistencias_ids[]"]').forEach(c => c.checked = false);
}
document.getElementById('justificarForm')?.addEventListener('submit', function(e) {
    const checkboxes = document.querySelectorAll('input[name="asistencias_ids[]"]:checked');
    if (checkboxes.length === 0) {
        e.preventDefault();
        alert('Debes seleccionar al menos una fecha para justificar.');
        return;
    }
    if(!confirm(`¿Confirmar justificación de ${checkboxes.length} ausencia(s)?`)) e.preventDefault();
});
</script>

@endsection
