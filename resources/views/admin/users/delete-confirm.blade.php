<div class="text-center">
    <h2 class="text-xl font-bold mb-4">¿Estás seguro de eliminar este usuario?</h2>
    <p class="mb-6 text-gray-700">Esta acción no se puede deshacer.<br><span class="font-semibold">{{ $user->name }} ({{ $user->email }})</span></p>
    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" x-on:submit.prevent="$root.__x.$data.closeModal(); $el.submit();">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded mr-2">Eliminar</button>
        <button type="button" @click="$root.__x.$data.closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded">Cancelar</button>
    </form>
</div> 