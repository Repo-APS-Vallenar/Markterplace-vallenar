<div id="editModal" class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 hidden">
  <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-lg mx-4">
    <div class="px-4 py-2 bg-gray-100 border-b flex justify-between items-center">
      <h3 class="text-lg font-semibold">Editar Producto</h3>
      <button id="closeModal" type="button" class="text-gray-600 hover:text-gray-800">&times;</button>
    </div>
    <form id="editProductForm" method="POST" action="">
      @csrf
      @method('PUT')
      <div class="px-4 py-4">
        <div class="mb-4">
          <label for="editName" class="block text-sm font-medium text-gray-700">Nombre</label>
          <input type="text" name="name" id="editName" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>
        <div class="mb-4">
          <label for="editPrice" class="block text-sm font-medium text-gray-700">Precio</label>
          <input type="number" name="price" id="editPrice" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>
        <div class="mb-4">
          <label for="editDescription" class="block text-sm font-medium text-gray-700">Descripción</label>
          <textarea name="description" id="editDescription" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required></textarea>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Categoría</label>
          <select name="category_id" id="editCategory" class="w-full border rounded px-3 py-2 mt-1">
              <option value="">Seleccione una categoría</option>
              @foreach(App\Models\Category::all() as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
          </select>
        </div>
      </div>
      <div class="px-4 py-3 bg-gray-100 text-right">
        <button type="button" id="cancelModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancelar</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Actualizar</button>
      </div>
    </form>
  </div>
</div>