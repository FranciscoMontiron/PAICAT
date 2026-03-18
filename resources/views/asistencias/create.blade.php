@extends('layouts.app')
@section('title', 'Pasar Asistencia')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Pasar Asistencia</h1>
                <p class="text-gray-600 mt-2">{{ $comision->codigo }} - {{ $comision->nombre }}</p>
                @if(isset($materia))
                <p class="text-utn-blue-dark mt-1 font-medium">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Materia: {{ $materia->nombre }}
                </p>
                @endif
            </div>
            <a href="{{ route('asistencias.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('asistencias.store', $comision) }}" method="POST" id="asistenciaForm">
        @csrf
        @if(isset($materia))
        <input type="hidden" name="materia_id" value="{{ $materia->id }}">
        @endif

        <!-- Info y Fecha -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Información de la Clase</h2>
            </div>
            <div class="p-4">
                <div class="flex flex-col md:flex-row gap-4 items-stretch">
                    <!-- Fecha de la clase -->
                    <div class="flex items-center gap-3 flex-1">
                        <div id="fechaDisplay" class="bg-utn-blue/10 border border-utn-blue/20 rounded-lg px-4 py-2 text-center min-w-[100px] flex-shrink-0">
                            <p class="text-[10px] text-utn-blue-dark font-semibold uppercase tracking-wider" id="fechaDia">-</p>
                            <p class="text-2xl font-bold text-utn-blue-darker leading-tight" id="fechaNumero">-</p>
                            <p class="text-xs text-utn-blue-dark" id="fechaMes">-</p>
                        </div>
                        <div class="flex-1">
                            <label for="fecha" class="block text-xs font-medium text-gray-500 mb-1">Fecha de la Clase <span class="text-red-500">*</span></label>
                            <input type="date"
                                name="fecha"
                                id="fecha"
                                value="{{ $fecha }}"
                                max="{{ date('Y-m-d') }}"
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent text-sm font-medium px-3 py-2"
                                required>
                            <p id="fechaHoyMsg" class="text-xs mt-1 font-medium {{ $fecha == date('Y-m-d') ? 'text-green-600' : 'hidden' }}">Registrando asistencia del día de hoy</p>
                        </div>
                    </div>

                    <!-- Separador -->
                    <div class="hidden md:block w-px bg-gray-200"></div>

                    <!-- Info -->
                    <div class="flex items-center gap-4 flex-1">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-medium uppercase">Docente</p>
                                <p class="text-sm font-semibold text-gray-900 leading-tight">{{ $comision->docente->name ?? 'Sin asignar' }}</p>
                            </div>
                        </div>
                        @if(isset($materia))
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-utn-blue/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-medium uppercase">Materia</p>
                                <p class="text-sm font-semibold text-gray-900 leading-tight">{{ $materia->nombre }}</p>
                            </div>
                        </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-medium uppercase">Comisión</p>
                                <p class="text-sm font-semibold text-gray-900 leading-tight">{{ $comision->codigo }} &bull; {{ $comision->turno ? ucfirst($comision->turno) : '' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Separador -->
                    <div class="hidden md:block w-px bg-gray-200"></div>

                    <!-- Alumnos -->
                    <div class="bg-utn-blue-darker rounded-lg px-5 py-3 flex items-center gap-3 flex-shrink-0">
                        <p class="text-3xl font-bold text-white">{{ $inscripciones->count() }}</p>
                        <p class="text-xs text-white/70 leading-tight">alumnos<br>a registrar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de Alumnos -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Listado de Alumnos</h2>
                <div class="flex gap-2">
                    <button type="button" onclick="marcarTodos('presente')" class="bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded text-sm font-medium">
                        Marcar Todos Presentes
                    </button>
                    <button type="button" onclick="marcarTodos('ausente')" class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded text-sm font-medium">
                        Marcar Todos Ausentes
                    </button>
                </div>
            </div>

            @if($inscripciones->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Alumno
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Presente
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ausente
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tardanza
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Justificado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Observaciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($inscripciones as $index => $inscripcion)
                        @php
                        $estadoActual = null;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $inscripcion->alumno->name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $inscripcion->alumno->email }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="radio"
                                    name="asistencias[{{ $index }}][estado]"
                                    value="presente"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500"
                                    {{ $estadoActual == 'presente' ? 'checked' : '' }}
                                    required>
                                <input type="hidden" name="asistencias[{{ $index }}][inscripcion_id]" value="{{ $inscripcion->id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="radio"
                                    name="asistencias[{{ $index }}][estado]"
                                    value="ausente"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500"
                                    {{ $estadoActual == 'ausente' ? 'checked' : '' }}>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="radio"
                                    name="asistencias[{{ $index }}][estado]"
                                    value="tardanza"
                                    class="w-5 h-5 text-yellow-600 focus:ring-yellow-500"
                                    {{ $estadoActual == 'tardanza' ? 'checked' : '' }}>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="radio"
                                    name="asistencias[{{ $index }}][estado]"
                                    value="justificado"
                                    class="w-5 h-5 text-utn-blue-dark focus:ring-utn-blue-dark"
                                    {{ $estadoActual == 'justificado' ? 'checked' : '' }}>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text"
                                    name="asistencias[{{ $index }}][observaciones]"
                                    value=""
                                    placeholder="Observaciones..."
                                    class="w-full rounded border-gray-300 text-sm">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Botones de Acción -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span class="font-medium">{{ $inscripciones->count() }}</span> alumnos inscritos
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('asistencias.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg transition duration-200 font-medium">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Asistencia
                    </button>
                </div>
            </div>
            @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No hay alumnos inscritos</h3>
                <p class="mt-2 text-sm text-gray-500">Esta comisión no tiene alumnos inscritos actualmente.</p>
            </div>
            @endif
        </div>
    </form>
</div>

<script>
    // Mostrar fecha legible
    const diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    function actualizarFechaDisplay() {
        const input = document.getElementById('fecha');
        if (!input.value) return;
        const parts = input.value.split('-');
        const fecha = new Date(parts[0], parts[1] - 1, parts[2]);
        document.getElementById('fechaDia').textContent = diasSemana[fecha.getDay()];
        document.getElementById('fechaNumero').textContent = fecha.getDate();
        document.getElementById('fechaMes').textContent = meses[fecha.getMonth()] + ' ' + fecha.getFullYear();

        const hoy = new Date().toISOString().split('T')[0];
        const msg = document.getElementById('fechaHoyMsg');
        if (input.value === hoy) {
            msg.classList.remove('hidden');
            msg.textContent = 'Registrando asistencia del día de hoy';
        } else {
            msg.classList.remove('hidden');
            msg.textContent = 'Registrando asistencia de otra fecha';
            msg.classList.replace('text-green-600', 'text-amber-600');
        }
    }

    document.getElementById('fecha').addEventListener('change', actualizarFechaDisplay);
    actualizarFechaDisplay();

    function marcarTodos(estado) {
        // Obtener todos los nombres de grupo únicos (asistencias[0][estado], asistencias[1][estado], etc.)
        const grupos = new Set();
        document.querySelectorAll('input[type="radio"][name*="[estado]"]').forEach(r => grupos.add(r.name));

        grupos.forEach(nombre => {
            const radio = document.querySelector(`input[type="radio"][name="${nombre}"][value="${estado}"]`);
            if (radio) radio.click();
        });
    }

    // Confirmación antes de enviar
    document.getElementById('asistenciaForm').addEventListener('submit', async function(e) {
        if (this.dataset.confirmBypassed) return;
        e.preventDefault();
        const fecha = document.querySelector('input[name="fecha"]').value;
        const esHoy = fecha === new Date().toISOString().split('T')[0];
        const msg = esHoy
            ? '¿Confirmar el registro de asistencia para hoy?'
            : `¿Confirmar el registro de asistencia para el ${fecha.split('-').reverse().join('/')}?`;
        const ok = await paiConfirm({
            title: 'Confirmar asistencia',
            message: msg
        });
        if (ok) {
            this.dataset.confirmBypassed = 'true';
            this.requestSubmit ? this.requestSubmit() : this.submit();
            delete this.dataset.confirmBypassed;
        }
    });
</script>
@endsection