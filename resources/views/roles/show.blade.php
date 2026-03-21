@extends('layouts.app')
@section('title', 'Rol: ' . $role->nombre)
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
            <h1 class="text-3xl font-bold text-gray-800">{{ $role->nombre }}</h1>
            <p class="text-gray-600 mt-1">{{ $role->descripcion ?? 'Sin descripcion' }}</p>
        </div>
        @if(auth()->user()->hasPermission('roles.gestionar'))
        <a href="{{ route('roles.edit', $role) }}" class="px-4 py-2.5 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar Rol
        </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Permisos -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Permisos asignados ({{ $role->permissions->count() }})</h3>
                </div>
                <div class="p-6">
                    @if($permissionsByModule->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($permissionsByModule as $module => $perms)
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ $module }}</h4>
                            <div class="space-y-1">
                                @foreach($perms as $perm)
                                <div class="flex items-center gap-2 px-3 py-2 bg-green-50 rounded-lg">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ $perm->nombre }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <p>Este rol no tiene permisos asignados.</p>
                        <a href="{{ route('roles.edit', $role) }}" class="mt-2 inline-block text-utn-blue-dark hover:text-utn-dark font-medium text-sm">Asignar permisos</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Usuarios con este rol -->
        <div>
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Usuarios ({{ $role->users->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($role->users as $user)
                    <a href="{{ route('usuarios.show', $user) }}" class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-utn-blue/10 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-utn-blue-dark">{{ strtoupper(substr($user->name, 0, 1) . substr($user->apellido ?? '', 0, 1)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }} {{ $user->apellido }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                        </div>
                    </a>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500 text-sm">
                        Ningun usuario tiene este rol
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
