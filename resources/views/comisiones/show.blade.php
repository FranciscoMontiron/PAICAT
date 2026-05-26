@extends('layouts.app')
@section('title', 'Detalle de Comisión')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

    <!-- Header -->
        <div x-data="{ show: true }" x-show="show">

            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300 flex justify-between items-start">
                    
                    <span>{{ session('success') }}</span>

                    <button @click="show = false" class="ml-4 font-bold text-green-700 hover:text-green-900">
                        ✖
                    </button>

                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300 flex justify-between items-start">
                    
                    <span>{{ session('error') }}</span>

                    <button @click="show = false" class="ml-4 font-bold text-red-700 hover:text-red-900">
                        ✖
                    </button>

                </div>
            @endif

        </div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-3xl font-bold text-gray-800">{{ $comision->nombre }}</h1>
                    @php
                        $estadoClasses = [
                            'activa' => 'bg-green-100 text-green-800',
                            'cerrada' => 'bg-yellow-100 text-yellow-800',
                            'finalizada' => 'bg-utn-blue/10 text-utn-blue-dark',
                            'cancelada' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $estadoClasses[$comision->estado] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($comision->estado) }}
                    </span>
                </div>
                <p class="text-gray-600 mt-1">Código: {{ $comision->codigo }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('comisiones.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg transition duration-200">
                    Volver
                </a>
                @if(auth()->user()->hasPermission('comisiones.editar'))
                <a href="{{ route('comisiones.edit', $comision) }}" class="bg-utn-blue-darker hover:bg-utn-dark-light text-white font-semibold px-4 py-2 rounded-lg transition duration-200">
                    Editar
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Inscriptos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['inscriptos'] }}</p>
                </div>
                <div class="bg-utn-blue/10 p-3 rounded-full">
                    <svg class="w-6 h-6 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Cupos Disponibles</p>
                    @if(is_null($stats['cupos_disponibles']))
                        <p class="text-2xl font-bold text-gray-400">Sin limite</p>
                    @else
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['cupos_disponibles'] }}</p>
                        @if($comision->extracupos_habilitados && $comision->extracupos > 0)
                            <p class="text-xs text-orange-600">Base: {{ $comision->cupo_maximo }} + {{ $comision->extracupos }} extra</p>
                        @endif
                    @endif
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Ocupación</p>
                    @if($comision->esVirtual() || is_null($comision->cupo_maximo))
                        <p class="text-2xl font-bold text-gray-400">N/A</p>
                    @else
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['porcentaje_ocupacion'], 1) }}%</p>
                    @endif
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Evaluaciones</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['evaluaciones'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información General -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Información General</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Año</label>
                            <p class="mt-1 text-gray-900">{{ $comision->anio }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Periodo</label>
                            <p class="mt-1 text-gray-900">{{ $comision->periodo }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Turno</label>
                            @if(!is_null($comision->turno_nombre))
                                <p class="mt-1 text-gray-900">{{ $comision->turno_nombre }}</p>
                            @else
                                <p class="mt-1 text-gray-900">Sin turno asignado</p>
                            @endif
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Modalidad</label>
                            <p class="mt-1 text-gray-900">{{ $comision->modalidad }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Cupo</label>
                            @if($comision->esVirtual() || is_null($comision->cupo_maximo))
                                <p class="mt-1 text-gray-400 italic">Sin limite (Virtual)</p>
                            @else
                                <p class="mt-1 text-gray-900">
                                    {{ $comision->cupo_real }} / {{ $comision->cupo_total }}
                                    @if($comision->extracupos_habilitados && $comision->extracupos > 0)
                                        <span class="text-xs text-orange-600">({{ $comision->cupo_maximo }} base + {{ $comision->extracupos }} extra)</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                        @if($comision->fecha_inicio)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Fecha de Inicio</label>
                            <p class="mt-1 text-gray-900">{{ $comision->fecha_inicio->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        @if($comision->fecha_fin)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Fecha de Fin</label>
                            <p class="mt-1 text-gray-900">{{ $comision->fecha_fin->format('d/m/Y') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($comision->descripcion)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Descripción</label>
                        <p class="mt-1 text-gray-900">{{ $comision->descripcion }}</p>
                    </div>
                    @endif

                    @if($comision->observaciones)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Observaciones</label>
                        <p class="mt-1 text-gray-900">{{ $comision->observaciones }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Alumnos Inscritos -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-xl font-bold text-gray-800">Alumnos Inscritos</h2>
                        <span class="text-sm text-gray-600">{{ $comision->inscripciones->count() }} alumnos</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if(auth()->user()->hasPermission('difusiones.generar-comision') && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('coordinador') || $comision->docente_id === auth()->id() || $comision->docentesActivos()->where('user_id', auth()->id())->exists()))
                        <button onclick="document.getElementById('modal-difundir-comision').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center text-sm shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Enviar Comunicado
                        </button>
                        @endif

                        @if(auth()->user()->hasPermission('comisiones.editar') && $comision->cupos_disponibles > 0)
                        <button onclick="document.getElementById('modal-agregar-alumno').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Agregar Alumno
                        </button>
                        @elseif(auth()->user()->hasPermission('comisiones.editar') && $comision->cupos_disponibles <= 0)
                        <span class="text-sm text-red-600 font-medium">Sin cupos disponibles</span>
                        @endif
                    </div>
                    @if(auth()->user()->hasPermission('comisiones.editar') && $comision->tieneCuposDisponibles())
                    <button onclick="document.getElementById('modal-agregar-alumno').classList.remove('hidden')" class="bg-utn-blue-darker hover:bg-utn-dark-light text-white px-4 py-2 rounded-lg transition duration-200 flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Agregar Alumno
                    </button>
                    @elseif(auth()->user()->hasPermission('comisiones.editar') && !$comision->tieneCuposDisponibles())
                    <span class="text-sm text-red-600 font-medium">Sin cupos disponibles</span>
                    @endif
                </div>
                <div class="p-6">
                    @if($comision->inscripciones->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Alumno</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inscripción</th>
                                    @if(auth()->user()->hasPermission('comisiones.editar'))
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($comision->inscripciones as $inscripcionComision)
                                @php
                                    // Obtener datos del alumno desde inscripcion o academico_dato
                                    if ($inscripcionComision->inscripcion) {
                                        $person = $inscripcionComision->inscripcion->getPerson();
                                        $nombreCompleto = $person ? $person->nombre . ' ' . $person->apellido : 'Sin nombre';
                                        $email = $person?->email ?? 'Sin email';
                                    } elseif ($inscripcionComision->academicoDato && $inscripcionComision->academicoDato->user) {
                                        $nombreCompleto = $inscripcionComision->academicoDato->user->nombre_completo ?? $inscripcionComision->academicoDato->user->name;
                                        $email = $inscripcionComision->academicoDato->user->email;
                                    } else {
                                        $nombreCompleto = 'Sin datos';
                                        $email = 'Sin email';
                                    }
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $nombreCompleto }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $email }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ ucfirst($inscripcionComision->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $inscripcionComision->fecha_inscripcion->format('d/m/Y H:i') }}
                                    </td>
                                    @if(auth()->user()->hasPermission('comisiones.editar'))
                                    <td class="px-4 py-3 text-sm text-center">
                                        <form action="{{ route('comisiones.desinscribirAlumno', [$comision, $inscripcionComision]) }}" method="POST" class="inline" data-confirm="¿Está seguro de desinscribir a este alumno?" data-confirm-type="danger" data-confirm-title="Desinscribir alumno" data-confirm-text="Desinscribir">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Desinscribir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-center text-gray-500 py-8">No hay alumnos inscritos en esta comisión aún.</p>
                    @endif
                </div>
            </div>

            <!-- Evaluaciones -->
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Evaluaciones</h2>
                </div>
                <div class="p-6">
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Gestionar Evaluaciones</h3>
                        <p class="mt-1 text-sm text-gray-500">Ver, crear y administrar las evaluaciones de esta comisión</p>
                        @if(auth()->user()->hasPermission('evaluaciones.ver'))
                        <div class="mt-6">
                            <a href="{{ route('evaluaciones.comision', $comision) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Ver Evaluaciones ({{ $stats['evaluaciones'] }})
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Personal Asignado -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">Personal Asignado</h2>
                    <span class="text-xs text-gray-500">{{ $comision->docentesActivos->count() }} persona{{ $comision->docentesActivos->count() != 1 ? 's' : '' }}</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($comision->docentesActivos as $asignacion)
                    <div class="flex items-center gap-3 px-6 py-3">
                        <div class="w-9 h-9 rounded-full bg-utn-blue/10 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-utn-blue-dark">{{ strtoupper(substr($asignacion->docente->name ?? '', 0, 1) . substr($asignacion->docente->apellido ?? '', 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $asignacion->docente->nombre_completo ?? 'Sin nombre' }}</p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                @foreach($asignacion->docente->roles ?? [] as $role)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                    @if($role->slug === 'docente') bg-blue-100 text-blue-700
                                    @elseif($role->slug === 'tutor') bg-purple-100 text-purple-700
                                    @elseif($role->slug === 'bedel') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">{{ $role->nombre }}</span>
                                @endforeach
                                @if($loop->first)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">Principal</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-6 text-center">
                        <p class="text-gray-500 text-sm italic">Sin personal asignado</p>
                        @if(auth()->user()->hasPermission('comisiones.editar'))
                        <a href="{{ route('comisiones.edit', $comision) }}" class="mt-3 inline-block text-sm text-utn-blue-dark hover:text-utn-dark font-medium">
                            Asignar personal
                        </a>
                        @endif
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Ubicación / Municipio -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Ubicación</h2>
                </div>
                <div class="p-6 space-y-3">
                    @if($comision->municipio)
                    <div class="flex items-center space-x-3">
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $comision->municipio->nombre }}</p>
                            @if($comision->municipio->direccion)
                            <p class="text-sm text-gray-600">{{ $comision->municipio->direccion }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                    <p class="text-gray-500 italic">Sin municipio asignado</p>
                    @endif

                    @if($comision->aula)
                    <div class="flex items-center space-x-3 pt-3 border-t border-gray-200">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $comision->aula->nombre }}</p>
                            @if($comision->aula->capacidad)
                            <p class="text-sm text-gray-600">Capacidad: {{ $comision->aula->capacidad }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Gestión de Cursadas -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Cursadas</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Gestiona el historial de cursadas de los alumnos, estados (cursando, aprobado, libre, etc.) y notas finales.
                    </p>
                    <a href="{{ route('cursadas.index', $comision) }}"
                       class="w-full inline-flex items-center justify-center bg-utn-blue-darker hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Ver Cursadas
                    </a>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            @if(auth()->user()->hasPermission('comisiones.editar'))
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Cambiar Estado</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('comisiones.cambiarEstado', $comision) }}" method="POST">
                        @csrf
                        <select name="estado" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 mb-3">
                            <option value="activa" {{ $comision->estado == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="cerrada" {{ $comision->estado == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                            <option value="finalizada" {{ $comision->estado == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                            <option value="cancelada" {{ $comision->estado == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                        <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg transition duration-200">
                            Actualizar Estado
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Extracupos (solo para presencial/semipresencial) -->
            @if(auth()->user()->hasPermission('comisiones.editar') && !$comision->esVirtual() && !is_null($comision->cupo_maximo))
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-800">Extracupos</h2>
                        @if($comision->extracupos_habilitados)
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">Activo</span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Habilita lugares adicionales por encima del cupo base de <strong>{{ $comision->cupo_maximo }}</strong> alumnos.
                    </p>

                    <button onclick="document.getElementById('modal-extracupos').classList.remove('hidden')"
                        class="w-full inline-flex items-center justify-center {{ $comision->extracupos_habilitados ? 'bg-orange-600 hover:bg-orange-700' : 'bg-gray-600 hover:bg-gray-700' }} text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        {{ $comision->extracupos_habilitados ? 'Modificar Extracupos ('.$comision->extracupos.')' : 'Habilitar Extracupos' }}
                    </button>

                    @if($comision->extracupos_habilitados && $comision->extracupos > 0)
                    <div class="mt-3 p-3 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Cupo base:</span>
                            <span class="font-medium">{{ $comision->cupo_maximo }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-orange-600">Extracupos:</span>
                            <span class="font-medium text-orange-600">+{{ $comision->extracupos }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm font-bold border-t border-orange-200 pt-1 mt-1">
                            <span class="text-gray-700">Total efectivo:</span>
                            <span>{{ $comision->cupo_total }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Información Adicional -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Información</h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Creada:</span>
                        <span class="text-gray-900">{{ $comision->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Última actualización:</span>
                        <span class="text-gray-900">{{ $comision->updated_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asignar Docente -->
@if(auth()->user()->hasPermission('comisiones.editar') && !$comision->docente)
<div id="modal-asignar-docente" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Asignar Docente</h3>
            <button onclick="document.getElementById('modal-asignar-docente').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="{{ route('comisiones.asignarDocente', $comision) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="docente_id" class="block text-sm font-medium text-gray-700 mb-1.5">Seleccionar docente</label>
                <select name="docente_id" id="docente_id" required
                    class="w-full px-3 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                    <option value="">-- Seleccionar --</option>
                    @foreach($docentes as $docente)
                        <option value="{{ $docente->id }}">{{ $docente->nombre_completo ?? $docente->name }} ({{ $docente->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-asignar-docente').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-utn-blue-darker text-white rounded-lg hover:bg-utn-dark-light transition-colors">
                    Asignar
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Extracupos -->
@if(auth()->user()->hasPermission('comisiones.editar') && !$comision->esVirtual() && !is_null($comision->cupo_maximo))
<div id="modal-extracupos" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Configurar Extracupos</h3>
            <button onclick="document.getElementById('modal-extracupos').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="{{ route('comisiones.actualizarExtracupos', $comision) }}" method="POST">
            @csrf

            <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800">
                    Cupo base actual: <strong>{{ $comision->cupo_maximo }}</strong> alumnos
                    <br>Inscriptos actualmente: <strong>{{ $comision->cupo_real }}</strong>
                </p>
            </div>

            <!-- Toggle habilitar -->
            <div class="mb-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="extracupos_habilitados" value="0">
                    <input type="checkbox" name="extracupos_habilitados" value="1" id="toggle-extracupos"
                        {{ $comision->extracupos_habilitados ? 'checked' : '' }}
                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-5 h-5"
                        onchange="document.getElementById('extracupos-cantidad').classList.toggle('hidden', !this.checked)">
                    <span class="text-sm font-medium text-gray-700">Habilitar extracupos</span>
                </label>
            </div>

            <!-- Cantidad -->
            <div id="extracupos-cantidad" class="{{ $comision->extracupos_habilitados ? '' : 'hidden' }} mb-4">
                <label for="extracupos" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Cantidad de extracupos
                </label>
                <div class="relative">
                    <input type="number" name="extracupos" id="extracupos"
                        value="{{ $comision->extracupos }}"
                        min="0" max="200" placeholder="Ej: 10"
                        class="w-full px-4 py-2.5 pr-20 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">alumnos</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Cupo total efectivo sera: <strong>{{ $comision->cupo_maximo }}</strong> + extracupos
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-extracupos').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Agregar Alumno -->
@if(auth()->user()->hasPermission('comisiones.editar'))
<div id="modal-agregar-alumno" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Agregar Alumno a la Comisión</h3>
            <button onclick="document.getElementById('modal-agregar-alumno').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Buscador -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar alumno</label>
            <input type="text" id="buscar-alumno-input" placeholder="Buscar por nombre, apellido, email o DNI..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-utn-blue-dark focus:border-blue-500"
                   onkeyup="buscarAlumnos(this.value)">
        </div>

        <!-- Resultados -->
        <div id="resultados-alumnos" class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg">
            <div class="p-4 text-center text-gray-500">
                Escribe al menos 2 caracteres para buscar alumnos disponibles.
            </div>
        </div>

        <!-- Mensaje de cupos -->
        <div class="mt-4 text-sm text-gray-600">
            @if(is_null($comision->cupos_disponibles))
                <span class="font-medium">Cupos:</span> Sin limite (Virtual)
            @else
                <span class="font-medium">Cupos disponibles:</span> {{ $comision->cupos_disponibles }} de {{ $comision->cupo_total }}
                @if($comision->extracupos_habilitados && $comision->extracupos > 0)
                    <span class="text-orange-600">(incluye {{ $comision->extracupos }} extracupos)</span>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
    let timeoutId = null;
    
    function buscarAlumnos(search) {
        clearTimeout(timeoutId);
        
        if (search.length < 2) {
            document.getElementById('resultados-alumnos').innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    Escribe al menos 2 caracteres para buscar alumnos disponibles.
                </div>
            `;
            return;
        }
        
        document.getElementById('resultados-alumnos').innerHTML = `
            <div class="p-4 text-center text-gray-500">
                <svg class="animate-spin h-5 w-5 mx-auto text-utn-blue-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2">Buscando...</p>
            </div>
        `;
        
        timeoutId = setTimeout(() => {
            fetch(`{{ route('comisiones.alumnosDisponibles', $comision) }}?search=${encodeURIComponent(search)}`)
                .then(response => response.json())
                .then(alumnos => {
                    if (alumnos.length === 0) {
                        document.getElementById('resultados-alumnos').innerHTML = `
                            <div class="p-4 text-center text-gray-500">
                                No se encontraron alumnos disponibles.
                            </div>
                        `;
                        return;
                    }
                    
                    let html = '<div class="divide-y divide-gray-200">';
                    alumnos.forEach(alumno => {
                        html += `
                            <div class="p-3 hover:bg-gray-50 flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">${alumno.nombre}</p>
                                    <p class="text-sm text-gray-600">${alumno.email} • DNI: ${alumno.dni}</p>
                                    <p class="text-xs text-gray-500">${alumno.especialidad}</p>
                                </div>
                                <form action="{{ route('comisiones.inscribirAlumno', $comision) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="inscripcion_id" value="${alumno.id}">
                                    <button type="submit" class="bg-utn-blue-darker hover:bg-utn-dark-light text-white px-3 py-1 rounded text-sm">
                                        Inscribir
                                    </button>
                                </form>
                            </div>
                        `;
                    });
                    html += '</div>';
                    document.getElementById('resultados-alumnos').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('resultados-alumnos').innerHTML = `
                        <div class="p-4 text-center text-red-500">
                            Error al buscar alumnos. Intente nuevamente.
                        </div>
                    `;
                });
        }, 300);
    }
</script>
@endif

<!-- Modal Enviar Comunicado (Difundir) -->
@if(auth()->user()->hasPermission('difusiones.generar-comision') && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('coordinador') || $comision->docente_id === auth()->id() || $comision->docentesActivos()->where('user_id', auth()->id())->exists()))
<div id="modal-difundir-comision" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Enviar Comunicado a {{ $comision->nombre }}</h3>
            <button onclick="document.getElementById('modal-difundir-comision').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="{{ route('comisiones.difundir', $comision) }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 text-sm text-blue-900 rounded-r">
                El mail se enviará a los <strong>{{ $comision->inscripciones()->whereIn('estado', ['inscripto', 'confirmado', 'aprobado'])->count() }}</strong> alumnos activos inscriptos en esta comisión.
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                <input type="text" name="asunto" required placeholder="Ej: Reprogramación de clase / Información importante"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                <textarea name="mensaje" rows="6" required placeholder="Escriba el cuerpo del mail aquí..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-difundir-comision').classList.add('hidden')"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                    Enviar Comunicado
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

