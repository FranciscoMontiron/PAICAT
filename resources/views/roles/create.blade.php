@extends('layouts.app')
@section('title', 'Crear Rol')
@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('roles.index') }}" class="text-utn-blue-dark hover:text-utn-blue-dark flex items-center gap-1 text-sm mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver a roles
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Crear Rol</h1>
            <p class="text-gray-600 mt-1">Define un nuevo rol y asigna los permisos correspondientes</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <ul class="list-disc list-inside text-sm text-red-700">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <!-- Datos del rol -->
        <div class="bg-white shadow-md rounded-xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Datos del Rol</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del rol *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                        placeholder="Ej: Coordinador, Bedel, Tutor...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                    <input type="text" name="descripcion" value="{{ old('descripcion') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent"
                        placeholder="Breve descripcion del rol...">
                </div>
            </div>

            {{-- Visibilidad --}}
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-semibold text-gray-800 mb-3">Visibilidad del contenido</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-all hover:border-utn-blue-dark/30 has-[:checked]:border-utn-blue-dark has-[:checked]:bg-utn-blue/5">
                        <input type="checkbox" name="solo_contenido_asignado" value="1"
                            {{ old('solo_contenido_asignado', true) ? 'checked' : '' }}
                            class="mt-0.5 h-4 w-4 text-utn-blue-dark border-gray-300 rounded focus:ring-utn-blue-dark">
                        <div>
                            <span class="text-sm font-medium text-gray-900">Solo ve contenido asignado</span>
                            <p class="text-xs text-gray-500 mt-0.5">El usuario solo vera las comisiones donde este asignado como personal. Se usa para docentes, tutores, bedeles, etc.</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-all hover:border-utn-blue-dark/30 has-[:checked]:border-utn-blue-dark has-[:checked]:bg-utn-blue/5">
                        <input type="checkbox" name="visibilidad_general" value="1"
                            {{ old('visibilidad_general', false) ? 'checked' : '' }}
                            class="mt-0.5 h-4 w-4 text-utn-blue-dark border-gray-300 rounded focus:ring-utn-blue-dark">
                        <div>
                            <span class="text-sm font-medium text-gray-900">Visibilidad general</span>
                            <p class="text-xs text-gray-500 mt-0.5">El usuario puede ver todo el contenido del sistema sin restriccion. Se usa para administradores y coordinadores.</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Permisos -->
        <div class="bg-white shadow-md rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Permisos</h3>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleAll(true)" class="text-xs text-utn-blue-dark hover:text-utn-dark font-medium">Seleccionar todos</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" onclick="toggleAll(false)" class="text-xs text-gray-500 hover:text-gray-700 font-medium">Deseleccionar todos</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($permissions as $module => $perms)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-200 flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-700 uppercase">{{ $module }}</h4>
                        <button type="button" onclick="toggleModule('{{ $module }}')" class="text-xs text-utn-blue-dark hover:text-utn-dark">Todo</button>
                    </div>
                    <div class="p-3 space-y-2">
                        @foreach($perms as $perm)
                        <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 rounded px-2 py-1.5 transition-colors">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                data-module="{{ $module }}"
                                {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}
                                class="w-4 h-4 text-utn-blue-dark border-gray-300 rounded focus:ring-utn-blue-dark">
                            <div>
                                <span class="text-sm text-gray-700">{{ $perm->nombre }}</span>
                                @if($perm->descripcion)
                                <p class="text-xs text-gray-400">{{ $perm->descripcion }}</p>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('roles.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancelar</a>
            <button type="submit" class="px-6 py-2.5 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors">Crear Rol</button>
        </div>
    </form>

</div>

<script>
function toggleAll(checked) {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = checked);
}
function toggleModule(module) {
    const cbs = document.querySelectorAll(`input[data-module="${module}"]`);
    const allChecked = [...cbs].every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}
</script>
@endsection
