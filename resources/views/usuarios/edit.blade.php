@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Editar Usuario</h1>
        <p class="text-gray-600 mt-1">Modifica la información del usuario {{ $usuario->nombre_completo }}</p>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nombre --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Apellido --}}
                <div>
                    <label for="apellido" class="block text-sm font-medium text-gray-700 mb-2">Apellido *</label>
                    <input type="text" name="apellido" id="apellido" value="{{ old('apellido', $usuario->apellido) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('apellido') border-red-500 @enderror">
                    @error('apellido')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DNI --}}
                <div>
                    <label for="dni" class="block text-sm font-medium text-gray-700 mb-2">DNI</label>
                    <input type="text" name="dni" id="dni" value="{{ old('dni', $usuario->dni) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('dni') border-red-500 @enderror">
                    @error('dni')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $usuario->telefono) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('telefono') border-red-500 @enderror">
                    @error('telefono')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('password') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Dejar en blanco para mantener la contraseña actual</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmar Contraseña --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent">
                </div>

                {{-- Estado --}}
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                    <select name="estado" id="estado" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue focus:border-transparent @error('estado') border-red-500 @enderror">
                        <option value="activo" {{ old('estado', $usuario->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $usuario->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        <option value="suspendido" {{ old('estado', $usuario->estado) === 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                    </select>
                    @error('estado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Roles --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($roles as $role)
                        <div class="flex items-center">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                                   {{ in_array($role->id, old('roles', $usuario->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                                   class="h-4 w-4 text-utn-blue border-gray-300 rounded focus:ring-utn-blue">
                            <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-700">{{ $role->nombre }}</label>
                        </div>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('usuarios.index') }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-utn-blue text-white rounded-lg hover:bg-blue-800 transition-colors duration-200">
                    Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
