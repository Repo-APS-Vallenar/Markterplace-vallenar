<!-- resources/views/seller/products/index.blade.php -->
@extends('layouts.admin')

@section('admin-content')
<div>
    <h1 class="text-2xl font-bold mb-4">Lista de Productos</h1>
    @if(session('success'))
    <div class="alert-success mb-4 p-4 rounded border border-green-400 bg-green-100 text-green-800 flex justify-between items-center">
        <div>
            <strong>¡Éxito!</strong> {{ session('success') }}
        </div>
        <button onclick="this.parentElement.remove()"
            class="text-green-700 hover:text-green-900">
            &times;
        </button>
    </div>
    @endif
    <!-- Botón Crear Producto -->
    <div class="mb-4">
        <button type="button"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded"
            onclick="window.openModal('{{ route('seller.products.create') }}')">
            Nuevo Producto
        </button>
    </div>

    <div class="overflow-x-auto bg-white shadow-md rounded">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-gray-600">Imagen</th>
                    <th class="px-4 py-2 text-left text-gray-600">Nombre</th>
                    <th class="px-4 py-2 text-left text-gray-600">Precio</th>
                    <th class="px-4 py-2 text-left text-gray-600">Descripción</th>
                    <th class="px-4 py-2 text-center text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="Imagen de {{ $product->name }}" class="w-16 h-16 object-cover rounded">
                        @else
                            <span class="text-gray-400">Sin imagen</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $product->name }}</td>
                    <td class="px-4 py-2">{{ number_format($product->price, 2) }}</td>
                    <td class="px-4 py-2">{{ $product->description }}</td>
                    <td class="px-4 py-2 text-center space-x-2">
                        <button type="button"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                            onclick="window.openModal('{{ route('seller.products.edit', $product) }}')">
                            Editar
                        </button>
                        <button type="button"
                            class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded"
                            onclick="window.openModal('{{ route('seller.products.show', $product) }}')">
                            Ver
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection