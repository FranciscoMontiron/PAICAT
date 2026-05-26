@extends('layouts.app')
@section('title', 'Roles y Permisos')
@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('usuarios.index') }}" class="text-utn-blue-dark hover:text-utn-blue-dark flex items-center gap-1 text-sm mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver a usuarios
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Roles y Permisos</h1>
            <p class="text-gray-600 mt-1">Administra los roles del sistema y sus permisos asociados</p>
        </div>
        @if(auth()->user()->hasPermission('roles.gestionar'))
        <div class="flex items-center gap-3">
            <form action="{{ route('roles.sync-permissions') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Sincronizar Permisos
                </button>
            </form>
            <a href="{{ route('roles.create') }}" class="px-4 py-2.5 bg-utn-blue text-white rounded-lg hover:bg-utn-dark transition-colors flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Rol
            </a>
        </div>
        @endif
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="relative bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-green-600 hover:text-green-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="relative bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-600 hover:text-red-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    <!-- Grid de Roles -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($roles as $role)
        <div class="bg-white shadow-md rounded-xl overflow-hidden hover:shadow-lg transition-shadow group">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-utn-blue/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $role->nombre }}</h3>
                            <span class="text-xs text-gray-400">{{ $role->slug }}</span>
                        </div>
                    </div>
                </div>

                @if($role->descripcion)
                <p class="text-sm text-gray-500 mb-4">{{ $role->descripcion }}</p>
                @endif

                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $role->users_count }} usuario{{ $role->users_count != 1 ? 's' : '' }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <span>{{ $role->permissions_count }} permiso{{ $role->permissions_count != 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('roles.show', $role) }}" class="text-sm text-utn-blue-dark hover:text-utn-dark font-medium">Ver detalle</a>
                @if(auth()->user()->hasPermission('roles.gestionar'))
                <div class="flex items-center gap-2">
                    <a href="{{ route('roles.edit', $role) }}" class="p-1.5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors" title="Editar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    @if($role->users_count === 0)
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" data-confirm="Se eliminara el rol '{{ $role->nombre }}' y todos sus permisos asociados." data-confirm-type="danger" data-confirm-title="Eliminar rol" data-confirm-text="Eliminar">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 rounded bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="Eliminar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white shadow-md rounded-xl p-16 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <p class="mt-3 text-gray-500">No hay roles definidos en el sistema.</p>
            <a href="{{ route('roles.create') }}" class="mt-4 inline-block text-utn-blue-dark hover:text-utn-dark font-medium">Crear el primer rol</a>
        </div>
        @endforelse
    </div>

</div>
@endsection
