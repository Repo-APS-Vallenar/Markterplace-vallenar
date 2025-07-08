<div class="p-6">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Editar Usuario</h2>
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña <span class="text-xs text-gray-400">(opcional)</span></label>
                <input type="password" name="password"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-6">
            <button type="button" onclick="window.closeModal()"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancelar</button>
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold">Actualizar Usuario</button>
        </div>
    </form> 
</div> 
