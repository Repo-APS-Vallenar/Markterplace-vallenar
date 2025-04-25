<!-- Modal genérico, sirve para editar o mostrar -->
<div id="showProductModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-4">Detalles del producto</h2>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nombre</label>
            <p class="mt-1 text-gray-700">{{ $product->name }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Precio</label>
            <p class="mt-1 text-gray-700">${{ number_format($product->price) }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Descripción</label>
            <p class="mt-1 text-gray-700">{{ $product->description }}</p>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeModalShow()" class="px-4 py-2 bg-gray-300 rounded-md">Cerrar</button>
        </div>
    </div>
</div>