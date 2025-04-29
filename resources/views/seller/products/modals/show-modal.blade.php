<div id="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 hidden">
  <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-lg mx-4">
    <div class="px-4 py-2 bg-gray-100 border-b flex justify-between items-center">
      <h3 class="text-lg font-semibold">Detalle del Producto</h3>
      <button id="closeShowModal" type="button" class="text-gray-600 hover:text-gray-800">&times;</button>
    </div>
    <div class="px-4 py-4 space-y-4">
      <div>
        <h4 class="font-medium text-gray-700">Nombre:</h4>
        <p id="showName" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Precio:</h4>
        <p id="showPrice" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Descripción:</h4>
        <p id="showDescription" class="mt-1 text-gray-900"></p>
      </div>
    </div>
    <div class="px-4 py-3 bg-gray-100 text-right">
      <button type="button" id="cancelShowModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cerrar</button>
    </div>
  </div>
</div>
