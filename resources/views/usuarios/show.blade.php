@extends('layouts.app')
@section('title', 'Detalle de Usuario')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detalle del Usuario</h1>
            <p class="text-gray-600 mt-1">Información completa y permisos del usuario</p>
        </div>
        <a href="{{ route('usuarios.edit', $usuario) }}"
           class="bg-utn-blue text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-200">
            Editar Usuario
        </a>
    </div>

    {{-- Información Personal --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Información Personal</h2>
        </div>
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-indigo-600 font-bold text-2xl">
                        {{ strtoupper(substr($usuario->name, 0, 1)) }}{{ strtoupper(substr($usuario->apellido, 0, 1)) }}
                    </span>
                </div>
                <div class="ml-6">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $usuario->name }} {{ $usuario->apellido }}</h3>
                    <p class="text-gray-600">{{ $usuario->email }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">DNI</label>
                    <p class="text-gray-900">{{ $usuario->dni ?? 'No especificado' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Teléfono</label>
                    <p class="text-gray-900">{{ $usuario->telefono ?? 'No especificado' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                    <span class="px-3 py-1 text-sm rounded-full inline-block
                        @if($usuario->estado === 'activo') bg-green-100 text-green-800
                        @elseif($usuario->estado === 'inactivo') bg-gray-100 text-gray-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($usuario->estado) }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Registrado</label>
                    <p class="text-gray-900">{{ $usuario->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Roles Asignados --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Roles Asignados</h2>
        </div>
        <div class="p-6">
            @forelse($usuario->roles as $role)
            <div class="mb-4 last:mb-0">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <span class="inline-block px-3 py-1 text-sm rounded-full
                            @if($role->slug === 'admin') bg-red-100 text-red-800
                            @elseif($role->slug === 'coordinador') bg-purple-100 text-purple-800
                            @elseif($role->slug === 'docente') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $role->nombre }}
                        </span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">{{ $role->descripcion }}</p>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">Este usuario no tiene roles asignados</p>
            @endforelse
        </div>
    </div>

    {{-- Permisos --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Permisos</h2>
        </div>
        <div class="p-6">
            @php
                $allPermissions = collect();
                foreach($usuario->roles as $role) {
                    $allPermissions = $allPermissions->merge($role->permissions);
                }
                $uniquePermissions = $allPermissions->unique('id')->sortBy('nombre');
            @endphp

            @if($uniquePermissions->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($uniquePermissions as $permission)
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $permission->nombre }}</p>
                        <p class="text-xs text-gray-500">{{ $permission->descripcion }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">Este usuario no tiene permisos asignados</p>
            @endif
        </div>
    </div>

    {{-- Botón volver --}}
    <div class="mt-6">
        <a href="{{ route('usuarios.index') }}"
           class="inline-block px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
            Volver al Listado
        </a>
    </div>
</div>
@endsection
