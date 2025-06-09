<div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-lg mx-4">
  <div class="px-4 py-2 bg-gray-100 border-b flex justify-between items-center">
    <h3 class="text-lg font-semibold">Detalle del Producto</h3>
    <button type="button" onclick="document.getElementById('dynamic-modal').classList.add('hidden')" class="text-gray-600 hover:text-gray-800">&times;</button>
  </div>
  <div class="px-4 py-4 space-y-4">
    <div>
      <h4 class="font-medium text-gray-700">Nombre:</h4>
      <p id="showName" class="mt-1 text-gray-900">{{ $product->name ?? '' }}</p>
    </div>
    <div>
      <h4 class="font-medium text-gray-700">Precio:</h4>
      <p id="showPrice" class="mt-1 text-gray-900">{{ $product->price ?? '' }}</p>
    </div>
    <div>
      <h4 class="font-medium text-gray-700">Descripción:</h4>
      <p id="showDescription" class="mt-1 text-gray-900">{{ $product->description ?? '' }}</p>
    </div>
    @if(!empty($product->image))
    <div>
      <h4 class="font-medium text-gray-700">Imagen:</h4>
      <img src="{{ asset('storage/' . $product->image) }}" alt="Imagen de {{ $product->name }}" class="w-32 h-32 object-cover rounded mb-2">
    </div>
    @endif
  </div>
  <div class="px-4 py-3 bg-gray-100 text-right">
    <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400" onclick="document.getElementById('dynamic-modal').classList.add('hidden')">Cerrar</button>
  </div>
</div>
