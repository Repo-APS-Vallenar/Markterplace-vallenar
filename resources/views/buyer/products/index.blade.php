<!-- resources/views/buyer/products/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 flex gap-6">
    <!-- Sidebar de filtros -->
    <aside class="w-64 bg-white p-4 rounded shadow h-fit">
        <form method="GET" id="sidebarFilters">
            <h2 class="font-semibold mb-2">Vendedores/Emprendedores</h2>
            <div class="mb-4 max-h-40 overflow-y-auto">
                @foreach(App\Models\User::where('role', 'seller')->get() as $seller)
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="seller_ids[]" value="{{ $seller->id }}" {{ collect(request('seller_ids'))->contains($seller->id) ? 'checked' : '' }}>
                            <span class="ml-2">{{ $seller->name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
            <h2 class="font-semibold mb-2">Categorías</h2>
            <div class="mb-4 max-h-40 overflow-y-auto">
                @foreach(App\Models\Category::all() as $category)
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" {{ collect(request('category_ids'))->contains($category->id) ? 'checked' : '' }}>
                            <span class="ml-2">{{ $category->name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700 mb-2">Filtrar</button>
            <a href="{{ route('buyer.products.index') }}" class="w-full block text-center bg-gray-200 text-gray-700 rounded px-4 py-2 hover:bg-gray-300">Limpiar</a>
        </form>
    </aside>
    <!-- Contenido principal -->
    <div class="flex-1">
        <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 rounded shadow">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre..." class="rounded border-gray-300 focus:ring focus:ring-blue-200">
            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Precio mínimo" class="rounded border-gray-300 focus:ring focus:ring-blue-200">
            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Precio máximo" class="rounded border-gray-300 focus:ring focus:ring-blue-200">
            <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">Filtrar</button>
        </form>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
            <div class="bg-white p-4 shadow rounded-lg">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="Imagen de {{ $product->name }}" class="w-full h-48 object-cover rounded mb-2">
                @endif
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
</div>
@endsection

@section('scripts')
<script>
    // Aquí puedes agregar funciones específicas para el comprador si es necesario
    // Por ejemplo, acciones relacionadas con la visualización del carrito, etc.
</script>
@endsection