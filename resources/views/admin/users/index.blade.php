@extends('layouts.admin')
@section('admin-content')
<div class="container mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Gestión de Usuarios</h1>
  <a href="{{ route('admin.users.create') }}" class="mb-4 inline-block px-4 py-2 bg-green-500 text-white rounded">Nuevo Usuario</a>
  <table class="min-w-full bg-white shadow rounded">
    <thead class="bg-gray-100"><tr>
      <th class="px-4 py-2">Nombre</th><th class="px-4 py-2">Email</th><th class="px-4 py-2">Rol</th><th class="px-4 py-2">Acciones</th>
    </tr></thead>
    <tbody>
      @forelse($users as $u)
      <tr class="border-t hover:bg-gray-50">
        <td class="px-4 py-2">{{ $u->name }}</td>
        <td class="px-4 py-2">{{ $u->email }}</td>
        <td class="px-4 py-2">{{ $u->role }}</td>
        <td class="px-4 py-2 space-x-2">
          <a href="{{ route('admin.users.edit',$u) }}" class="px-3 py-1 bg-yellow-500 text-white rounded">Editar</a>
          <form action="{{ route('admin.users.destroy',$u) }}" method="POST" class="inline">@csrf @method('DELETE')
            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded" onclick="return confirm('¿Eliminar?');">Borrar</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="4" class="px-4 py-4 text-center text-gray-600">No hay usuarios.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection