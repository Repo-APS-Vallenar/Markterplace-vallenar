@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Emprendedores y sus productos</h1>

    @forelse($sellers as $seller)
    <div class="mb-8 border-b pb-4">
        <h2 class="text-xl font-semibold text-gray-700">{{ $seller->name }}</h2>
        <p class="text-gray-500">{{ $seller->email }}</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4">
            @forelse($seller->products as $product)
            <div class="border rounded-lg p-4 shadow hover:shadow-md transition">
                <h3 class="text-lg font-medium">{{ $product->name }}</h3>
                <p class="text-gray-600">{{ $product->description }}</p>
                <p class="font-bold text-green-600 mt-2">${{ number_format($product->price, 0, ',', '.') }}</p>
            </div>
            @empty
            <p class="text-sm text-gray-500">Este vendedor aún no tiene productos.</p>
            @endforelse
        </div>
    </div>
    @empty
    <p>No se encontraron emprendedores con productos.</p>
    @endforelse
</div>
@endsection