<div id="createProductModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
        <!-- contenido del formulario -->
        <h2 class="text-xl font-bold mb-4">Crear nuevo producto</h2>
        <form method="POST" action="{{ route('seller.products.store') }}">
            @csrf
            <!-- Nombre -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="name" required
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <!-- Precio -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Precio</label>
                <input type="number" name="price" step="0.01" required
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <!-- Descripción -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="description"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            <!-- Botones -->
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-md">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md">Guardar</button>
            </div>
        </form>
    </div>
</div>
