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
           class="bg-utn-blue text-white px-6 py-2 rounded-lg hover:bg-utn-dark transition-colors duration-200">
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
                <div class="h-20 w-20 rounded-full bg-utn-blue/10 flex items-center justify-center">
                    <span class="text-utn-blue-dark font-bold text-2xl">
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

    {{-- Roles y Permisos --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Roles y Permisos</h2>
            @php
                $allPermissions = collect();
                foreach($usuario->roles as $role) {
                    $allPermissions = $allPermissions->merge($role->permissions);
                }
                $uniquePermissions = $allPermissions->unique('id')->sortBy('slug');
                $permissionsByModule = $uniquePermissions->groupBy(function ($p) {
                    return \Illuminate\Support\Str::before($p->slug, '.');
                });
            @endphp
            <span class="text-sm text-gray-500">{{ $usuario->roles->count() }} rol{{ $usuario->roles->count() != 1 ? 'es' : '' }} · {{ $uniquePermissions->count() }} permiso{{ $uniquePermissions->count() != 1 ? 's' : '' }}</span>
        </div>
        <div class="p-6">
            @forelse($usuario->roles as $role)
            <div class="mb-4 last:mb-0 border border-gray-200 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                            @if($role->slug === 'admin') bg-red-100
                            @elseif($role->slug === 'coordinador') bg-purple-100
                            @elseif($role->slug === 'docente') bg-utn-blue/10
                            @else bg-gray-100
                            @endif">
                            <svg class="w-4 h-4
                                @if($role->slug === 'admin') text-red-600
                                @elseif($role->slug === 'coordinador') text-purple-600
                                @elseif($role->slug === 'docente') text-utn-blue-dark
                                @else text-gray-600
                                @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-gray-900">{{ $role->nombre }}</span>
                            @if($role->descripcion)
                            <p class="text-xs text-gray-500">{{ $role->descripcion }}</p>
                            @endif
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">{{ $role->permissions->count() }} permiso{{ $role->permissions->count() != 1 ? 's' : '' }}</span>
                </div>
                @if($role->permissions->count() > 0)
                <div class="px-4 py-3">
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($role->permissions->sortBy('slug') as $perm)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs bg-green-50 text-green-700 border border-green-100">
                            <svg class="w-3 h-3 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $perm->nombre }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="px-4 py-3">
                    <p class="text-xs text-amber-600">Este rol no tiene permisos asignados</p>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-8">
                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500">Este usuario no tiene roles asignados</p>
                <a href="{{ route('usuarios.edit', $usuario) }}" class="mt-1 inline-block text-sm text-utn-blue-dark hover:text-utn-dark font-medium">Asignar roles</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Resumen de permisos por modulo --}}
    @if($permissionsByModule->count() > 0)
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Resumen de permisos por modulo</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($permissionsByModule as $module => $perms)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $module }}</h4>
                    </div>
                    <div class="p-3 space-y-1">
                        @foreach($perms as $perm)
                        <div class="flex items-center gap-2 px-2 py-1">
                            <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $perm->nombre }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Botón volver --}}
    <div class="mt-6">
        <a href="{{ route('usuarios.index') }}"
           class="inline-block px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
            Volver al Listado
        </a>
    </div>
</div>
@endsection
