@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <h1 class="text-2xl font-bold mb-4">Realizar Pedido</h1>

    <form action="{{ route('orders.store') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div>
            <label class="block text-sm font-medium text-gray-700">Producto</label>
            <p class="mt-1">{{ $product->name }}</p>
        </div>

        <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700">Cantidad</label>
            <input type="number" name="quantity" id="quantity" min="1" value="1" required
                class="mt-1 block w-full border-gray-300 rounded-md p-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Método de entrega</label>
            <select name="delivery_method" class="mt-1 block w-full border-gray-300 rounded-md p-2" required>
                <option value="domicilio">Domicilio</option>
                <option value="acordar">Acordar</option>
            </select>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('buyer.index') }}" class="px-4 py-2 bg-gray-300 rounded">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Enviar Pedido</button>
        </div>
    </form>
</div>
@endsection