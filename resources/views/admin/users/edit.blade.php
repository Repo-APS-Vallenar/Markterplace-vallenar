<form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
        <input type="text" name="name" id="name" value="{{ $user->name }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" id="email" value="{{ $user->email }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña (opcional)</label>
        <input type="password" name="password" id="password"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
        <select name="role" id="role" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrador</option>
            <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>Vendedor</option>
            <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Comprador</option>
        </select>
    </div>
    <div class="flex justify-end space-x-2">
        <button type="button" @click="$root.__x.$data.closeModal()"
            class="px-4 py-2 bg-gray-300 text-gray-800 rounded">Cancelar</button>
        <button type="submit"
            class="px-4 py-2 bg-blue-500 text-white rounded">Actualizar Usuario</button>
    </div>
</form> 