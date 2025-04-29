<!-- resources/views/buyer/products/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Productos disponibles</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($products as $product)
        <div class="bg-white p-4 shadow rounded-lg">
            <h2 class="font-semibold text-xl">{{ $product->name }}</h2>
            <p class="text-gray-600">{{ $product->description }}</p>
            <p class="text-xl font-bold mt-2">${{ number_format($product->price) }}</p>
            <p class="text-sm text-gray-500">Vendedor: {{ $product->user->name }}</p>

            <!-- Botón de Añadir al carrito -->
            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded mt-4">Añadir al carrito</button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Aquí puedes agregar funciones específicas para el comprador si es necesario
    // Por ejemplo, acciones relacionadas con la visualización del carrito, etc.
</script>
@endsection