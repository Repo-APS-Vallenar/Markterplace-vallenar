@php
    $total = 0;
    $totalUnidades = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
        $totalUnidades += $item['quantity'];
    }
@endphp
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-4">
    <div class="text-lg font-semibold flex items-center gap-2">
        <span>Productos en carrito:</span>
        <span id="cart-total-items" class="text-blue-700 font-bold text-xl">{{ $totalUnidades }}</span>
    </div>
    <div class="text-right text-lg font-semibold flex items-center gap-2">
        <span>Total a pagar:</span>
        <span id="cart-total-price" class="text-green-700 font-bold text-2xl">${{ number_format($total, 0, ',', '.') }}</span>
    </div>
</div>
<div class="overflow-x-auto">
    <table class="min-w-full bg-white shadow rounded-lg overflow-hidden mb-8">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Producto</th>
                <th class="px-6 py-3 text-center text-sm font-bold text-gray-700">Cantidad</th>
                <th class="px-6 py-3 text-right text-sm font-bold text-gray-700">Precio</th>
                <th class="px-6 py-3 text-right text-sm font-bold text-gray-700">Subtotal</th>
                <th class="px-6 py-3 text-center text-sm font-bold text-gray-700">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cart as $id => $item)
                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition">
                    <td class="px-6 py-4 align-middle text-base">{{ $item['name'] }}</td>
                    <td class="px-6 py-4 align-middle">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" class="cart-btn cart-btn-dec shadow-sm" data-id="{{ $id }}" aria-label="Disminuir">
                                <span class="text-xl">-</span>
                            </button>
                            <input type="text" readonly value="{{ $item['quantity'] }}" class="cart-qty-input w-14 text-center border border-gray-300 rounded font-semibold bg-gray-100" data-id="{{ $id }}">
                            <button type="button" class="cart-btn cart-btn-inc shadow-sm" data-id="{{ $id }}" aria-label="Aumentar">
                                <span class="text-xl">+</span>
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-middle text-right text-base">${{ number_format($item['price'], 0, ',', '.') }}</td>
                    <td class="px-6 py-4 align-middle text-right text-base">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                    <td class="px-6 py-4 align-middle text-center">
                        <button type="button" class="cart-btn cart-btn-del shadow-md hover:scale-110 transition" data-id="{{ $id }}" title="Eliminar">
                            <i class="fas fa-trash-alt text-xl"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="cart-sticky flex flex-col md:flex-row justify-between items-center mt-8 gap-4 bg-white rounded-lg shadow px-4 py-4">
    <a href="{{ route('buyer.products.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 font-semibold">Seguir comprando</a>
    <form action="{{ route('cart.checkout') }}" method="POST">
        @csrf
        <button type="submit" class="bg-green-600 shadow-lg text-white px-8 py-3 rounded-lg hover:bg-green-700 text-lg font-bold transition">Finalizar compra</button>
    </form>
</div>