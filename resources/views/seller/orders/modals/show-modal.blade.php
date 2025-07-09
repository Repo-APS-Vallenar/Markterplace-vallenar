{{-- resources/views/seller/orders/modals/show-modal.blade.php --}}

<div class="space-y-3 text-gray-800">
    <p><span class="font-semibold">ID de Pedido:</span> {{ $order->id }}</p>
    <p><span class="font-semibold">Comprador:</span> {{ $order->user->name ?? 'Desconocido' }}</p>
    <p><span class="font-semibold">Método de Pago:</span> {{ ucfirst($order->payment_method) }}</p>
    <p><span class="font-semibold">Estado:</span>
        @php
            $statusColors = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'processing' => 'bg-blue-100 text-blue-800',
                'completed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800',
            ];
            $statusLabels = [
                'pending' => 'Pendiente',
                'processing' => 'En proceso',
                'completed' => 'Completado',
                'cancelled' => 'Cancelado',
            ];
            $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-500';
            $label = $statusLabels[$order->status] ?? ucfirst($order->status);
        @endphp
        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $color }}">{{ $label }}</span>
    </p>
    <p><span class="font-semibold">Notas:</span> {{ $order->notes ?? 'N/A' }}</p>
    <p><span class="font-semibold">Fecha del Pedido:</span> {{ $order->created_at->format('d/m/Y H:i') }}</p>

    <h4 class="font-bold mt-4 mb-2 text-gray-900">Productos en este pedido:</h4>
    <ul class="list-disc list-inside space-y-1 pl-4">
        @forelse ($order->orderItems as $item)
            <li>
                <span class="font-semibold">{{ $item->product->name ?? 'Producto desconocido' }}</span>
                (x{{ $item->quantity }}) -
                Precio Unitario: ${{ number_format($item->price, 0, ',', '.') }} -
                Subtotal: ${{ number_format($item->subtotal, 0, ',', '.') }}
                @if($item->seller_id && $item->seller_id !== Auth::id())
                    <span class="text-sm text-gray-500">(Vendido por otro vendedor)</span>
                @endif
            </li>
        @empty
            <li>No hay productos en este pedido.</li>
        @endforelse
    </ul>

    <p class="font-bold text-lg mt-4 text-gray-900">Total del Pedido: ${{ number_format($order->total, 0, ',', '.') }}</p>
</div>