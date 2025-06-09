<div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-lg mx-4">
  <div class="px-4 py-2 bg-gray-100 border-b flex justify-between items-center">
    <h3 class="text-lg font-semibold">Confirmar Eliminación</h3>
    <button type="button" onclick="document.getElementById('dynamic-modal').classList.add('hidden')" class="text-gray-600 hover:text-gray-800">&times;</button>
  </div>
  <form method="POST" action="{{ isset($product) ? route('seller.products.destroy', $product) : '' }}" target="_top">
    @csrf
    @method('DELETE')
    <div class="px-4 py-6 text-center">
      <p class="text-gray-700 mb-4">¿Estás seguro de que deseas eliminar el producto <strong>{{ $product->name ?? '' }}</strong>? Esta acción no se puede deshacer.</p>
    </div>
    <div class="px-4 py-3 bg-gray-100 text-right">
      <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400" onclick="document.getElementById('dynamic-modal').classList.add('hidden')">Cancelar</button>
      <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
    </div>
  </form>
</div> 