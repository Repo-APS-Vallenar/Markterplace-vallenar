<div class="px-4 py-2 bg-gray-100 border-b flex justify-between items-center">
    <h3 class="text-lg font-semibold">Crear Nuevo Producto</h3>
</div>
<form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="px-4 py-4">
        <div class="mb-4">
            <label for="createName" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" name="name" id="createName" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>
        <div class="mb-4">
            <label for="createPrice" class="block text-sm font-medium text-gray-700">Precio</label>
            <input type="number" name="price" id="createPrice" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>
        <div class="mb-4">
            <label for="createDescription" class="block text-sm font-medium text-gray-700">Descripción</label>
            <textarea name="description" id="createDescription" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required></textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Categoría</label>
            <select name="category_id" class="w-full border rounded px-3 py-2 mt-1">
                <option value="">Seleccione una categoría</option>
                @foreach(App\Models\Category::all() as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="createImage" class="block text-sm font-medium text-gray-700">Imagen</label>
            <input type="file" name="image" id="createImage" class="mt-1 block w-full border border-gray-300 rounded-md p-2" accept="image/*">
        </div>
    </div>
    <div class="px-4 py-3 bg-gray-100 text-right">
        <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400" onclick="document.getElementById('dynamic-modal').classList.add('hidden')">Cancelar</button>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar</button>
    </div>
</form>