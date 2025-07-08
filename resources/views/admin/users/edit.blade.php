@extends('layouts.admin')

@section('admin-content')
<form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4 max-w-lg mx-auto mt-8 bg-white p-6 rounded shadow">
    @csrf
    @method('PUT')
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña (opcional)</label>
        <input type="password" name="password" id="password"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div class="flex justify-end space-x-2">
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded">Cancelar</a>
        <button type="submit"
            class="px-4 py-2 bg-blue-500 text-white rounded">Actualizar Usuario</button>
    </div>
</form>
@endsection 