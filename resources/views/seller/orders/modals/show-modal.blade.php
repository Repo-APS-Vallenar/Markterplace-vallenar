<div class="p-6">
    <h2 class="text-xl font-semibold mb-4">Detalle del Pedido #{{ $order->id }}</h2>
    <div class="mb-2"><strong>Producto:</strong> {{ $order->product->name ?? '-' }}</div>
    <div class="mb-2"><strong>Comprador:</strong> {{ $order->user->name ?? '-' }}</div>
    <div class="mb-2"><strong>Cantidad:</strong> {{ $order->quantity }}</div>
    <div class="mb-2"><strong>Total:</strong> ${{ number_format($order->total, 0, ',', '.') }}</div>
    <div class="mb-2"><strong>Estado:</strong> {{ ucfirst($order->status) }}</div>
    <div class="mt-6 text-right">
        <button type="button" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300" @click="$dispatch('close-modal')">Cerrar</button>
    </div>
</div> 