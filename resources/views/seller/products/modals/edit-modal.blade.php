<div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-lg mx-4">
  <div class="px-4 py-2 bg-gray-100 border-b flex justify-between items-center">
    <h3 class="text-lg font-semibold">Editar Producto</h3>
    <button type="button" onclick="document.getElementById('dynamic-modal').classList.add('hidden')" class="text-gray-600 hover:text-gray-800">&times;</button>
  </div>
  <form id="editProductForm" method="POST" action="{{ isset($product) ? route('seller.products.update', $product) : '' }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="px-4 py-4">
      <div class="mb-4">
        <label for="editName" class="block text-sm font-medium text-gray-700">Nombre</label>
        <input type="text" name="name" id="editName" class="mt-1 block w-full border border-gray-300 rounded-md p-2" value="{{ $product->name ?? '' }}" required>
      </div>
      <div class="mb-4">
        <label for="editPrice" class="block text-sm font-medium text-gray-700">Precio</label>
        <input type="number" name="price" id="editPrice" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md p-2" value="{{ $product->price ?? '' }}" required>
      </div>
      <div class="mb-4">
        <label for="editDescription" class="block text-sm font-medium text-gray-700">Descripción</label>
        <textarea name="description" id="editDescription" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>{{ $product->description ?? '' }}</textarea>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Categoría</label>
        <select name="category_id" id="editCategory" class="w-full border rounded px-3 py-2 mt-1">
            <option value="">Seleccione una categoría</option>
            @foreach(App\Models\Category::all() as $category)
                <option value="{{ $category->id }}" @if(isset($product) && $product->category_id == $category->id) selected @endif>{{ $category->name }}</option>
            @endforeach
        </select>
      </div>
      <div class="mb-4">
        <label for="editImage" class="block text-sm font-medium text-gray-700">Imagen</label>
        <input type="file" name="image" id="editImage" class="mt-1 block w-full border border-gray-300 rounded-md p-2" accept="image/*">
      </div>
    </div>
    <div class="px-4 py-3 bg-gray-100 text-right flex justify-between items-center">
      <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" onclick="window.openModal('{{ route('seller.products.delete-modal', $product) }}')">Eliminar</button>
      <div>
        <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400" onclick="document.getElementById('dynamic-modal').classList.add('hidden')">Cancelar</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Actualizar</button>
      </div>
    </div>
  </form>
</div>