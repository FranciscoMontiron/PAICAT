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
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Apellido --}}
                <div>
                    <label for="apellido" class="block text-sm font-medium text-gray-700 mb-2">Apellido *</label>
                    <input type="text" name="apellido" id="apellido" value="{{ old('apellido', $usuario->apellido) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('apellido') border-red-500 @enderror">
                    @error('apellido')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DNI --}}
                <div>
                    <label for="dni" class="block text-sm font-medium text-gray-700 mb-2">DNI</label>
                    <input type="text" name="dni" id="dni" value="{{ old('dni', $usuario->dni) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('dni') border-red-500 @enderror">
                    @error('dni')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $usuario->telefono) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('telefono') border-red-500 @enderror">
                    @error('telefono')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('password') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Dejar en blanco para mantener la contraseña actual</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmar Contraseña --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent">
                </div>

                {{-- Estado --}}
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                    <select name="estado" id="estado" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-utn-blue-dark focus:border-transparent @error('estado') border-red-500 @enderror">
                        @foreach(['activo' => 'Activo', 'inactivo' => 'Inactivo', 'suspendido' => 'Suspendido'] as $key => $label)
                            <option value="{{ $key }}" {{ old('estado', $usuario->estado) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('estado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Roles --}}
            <div class="mt-6 border-t border-gray-200 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Roles</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Selecciona los roles que tendra el usuario. Cada rol otorga un conjunto de permisos.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($roles as $role)
                    <label for="role_{{ $role->id }}" class="relative flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-all hover:border-utn-blue-dark/30 hover:bg-utn-blue/5 has-[:checked]:border-utn-blue-dark has-[:checked]:bg-utn-blue/5 has-[:checked]:ring-1 has-[:checked]:ring-utn-blue-dark">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                               {{ in_array($role->id, old('roles', $usuario->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                               class="mt-0.5 h-4 w-4 text-utn-blue-dark border-gray-300 rounded focus:ring-utn-blue-dark">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $role->nombre }}</span>
                                @if($role->permissions_count > 0)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ $role->permissions_count }} permiso{{ $role->permissions_count != 1 ? 's' : '' }}</span>
                                @endif
                            </div>
                            @if($role->descripcion)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $role->descripcion }}</p>
                            @endif
                            @if($role->permissions->count() > 0)
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($role->permissions->take(5) as $perm)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-green-50 text-green-700">{{ $perm->nombre }}</span>
                                @endforeach
                                @if($role->permissions->count() > 5)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-gray-50 text-gray-500">+{{ $role->permissions->count() - 5 }} mas</span>
                                @endif
                            </div>
                            @else
                            <p class="text-xs text-amber-600 mt-1">Sin permisos asignados</p>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-xs text-gray-400">{{ $role->users_count }} usuario{{ $role->users_count != 1 ? 's' : '' }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botones --}}
            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('usuarios.index') }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors duration-200">
                    Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
