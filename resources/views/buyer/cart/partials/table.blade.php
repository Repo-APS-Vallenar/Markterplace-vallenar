@php
    $total = 0;
    $totalUnidades = 0;
    foreach($cart as $item) {
        $total += $item['price'] * $item['quantity'];
        $totalUnidades += $item['quantity'];
    }
@endphp
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
    <div>
        <span class="text-lg font-semibold">Productos en carrito:</span>
        <span class="text-blue-700 font-bold">{{ $totalUnidades }}</span>
    </div>
    <div class="text-right">
        <span class="text-lg font-semibold">Total a pagar:</span>
        <span class="text-green-700 font-bold text-2xl">${{ number_format($total, 0, ',', '.') }}</span>
    </div>
</div>
<div class="overflow-x-auto">
<table class="min-w-full bg-white shadow rounded-lg overflow-hidden mb-6">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-2">Producto</th>
            <th class="px-4 py-2">Cantidad</th>
            <th class="px-4 py-2">Precio</th>
            <th class="px-4 py-2">Subtotal</th>
            <th class="px-4 py-2 text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cart as $id => $item)
        <tr class="border-t hover:bg-gray-50">
            <td class="px-4 py-2">{{ $item['name'] }}</td>
            <td class="px-4 py-2">
                <div class="flex items-center gap-2 justify-center">
                    <button type="button" class="cart-btn cart-btn-dec" data-id="{{ $id }}" title="Disminuir">-</button>
                    <input type="text" readonly value="{{ $item['quantity'] }}" class="w-14 text-center border rounded mx-1 cart-qty-input" data-id="{{ $id }}">
                    <button type="button" class="cart-btn cart-btn-inc" data-id="{{ $id }}" title="Aumentar">+</button>
                </div>
            </td>
            <td class="px-4 py-2">${{ number_format($item['price'], 0, ',', '.') }}</td>
            <td class="px-4 py-2">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
            <td class="px-4 py-2 text-center">
                <button class="cart-btn cart-btn-del" data-id="{{ $id }}" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
<div class="cart-sticky flex flex-col md:flex-row justify-between items-center mt-6 gap-4">
    <a href="{{ route('buyer.products.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Seguir comprando</a>
    <form action="{{ route('cart.checkout') }}" method="POST">
        @csrf
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 text-lg font-semibold">Finalizar compra</button>
    </form>
</div> 