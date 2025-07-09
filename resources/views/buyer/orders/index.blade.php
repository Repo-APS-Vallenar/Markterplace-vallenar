@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-extrabold mb-6 text-gray-800 flex items-center gap-2">
        <i class="fas fa-shopping-bag text-green-600"></i> Mis compras
    </h1>
    @if($orders->isEmpty())
    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500 text-lg">
        <i class="fas fa-box-open text-4xl mb-4 text-gray-300"></i><br>
        ¡Aún no has realizado compras!
    </div>
    @else
    <div x-data="{ showModal: false, showCancelModal: false, order: null }" @keydown.escape.window="showModal = false; showCancelModal = false">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-lg rounded-xl overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left">Producto(s)</th> {{-- Cambiado para reflejar múltiples productos --}}
                        <th class="px-6 py-3 text-center">Cantidad Total</th> {{-- Cambiado para reflejar la cantidad total --}}
                        <th class="px-6 py-3 text-center">Método de pago</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-center">Notas</th>
                        <th class="px-6 py-3 text-center">Fecha</th>
                        <th class="px-6 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-green-50 transition">
                        <td class="px-6 py-4 align-middle font-semibold text-gray-800">
                            {{-- Mostrar el primer producto y una indicación si hay más --}}
                            @if($order->orderItems->isNotEmpty())
                            {{ $order->orderItems->first()->product->name ?? 'Producto desconocido' }}
                            @if($order->orderItems->count() > 1)
                            <span class="text-gray-500 text-sm"> (+{{ $order->orderItems->count() - 1 }} más)</span>
                            @endif
                            @else
                            -
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            {{-- Mostrar la cantidad total de items en el pedido --}}
                            {{ $order->orderItems->sum('quantity') }}
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            @if($order->payment_method === 'cash')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-bold"><i class="fas fa-money-bill-wave"></i> Efectivo</span>
                            @elseif($order->payment_method === 'transfer')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs font-bold"><i class="fas fa-university"></i> Transferencia</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
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
                        </td>
                        <td class="px-6 py-4 align-middle text-center text-gray-600 text-sm max-w-xs truncate" title="{{ $order->notes }}">
                            {{ $order->notes ? \Illuminate\Support\Str::limit($order->notes, 30) : '-' }}
                        </td>
                        <td class="px-6 py-4 align-middle text-center">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 align-middle text-center">
                            <button @click="showModal = true; order = {{ json_encode([
                                    'id' => $order->id,
                                    'payment_method' => $order->payment_method,
                                    'status' => $label, // Usar el label
                                    'status_color' => $color, // Usar el color
                                    'notes' => $order->notes,
                                    'created_at' => $order->created_at->format('d/m/Y'),
                                    'total' => number_format($order->total, 2, ',', '.'), // Formato para moneda
                                    'order_items' => $order->orderItems->map(function($item) {
                                        return [
                                            'product_name' => $item->product->name ?? 'Producto desconocido',
                                            'quantity' => $item->quantity,
                                            'price' => number_format($item->price, 2, ',', '.'),
                                            'subtotal' => number_format($item->subtotal, 2, ',', '.'),
                                        ];
                                    }),
                                    'cancelable' => $order->status === 'pending',
                                ]) }}"
                                class="inline-flex items-center gap-1 px-3 py-1 rounded bg-gray-200 hover:bg-green-200 text-green-800 font-bold text-xs transition">
                                <i class="fas fa-eye"></i> Ver detalle
                            </button>
                            @if($order->status === 'pending')
                            <button @click="showCancelModal = true; order = { id: {{ $order->id }} }" {{-- Solo pasamos el ID para cancelar --}}
                                class="inline-flex items-center gap-1 px-3 py-1 rounded bg-red-100 hover:bg-red-200 text-red-700 font-bold text-xs transition">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Modal de Detalle de Compra --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.away="showModal = false" class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 relative animate-fadeIn border-t-8 border-green-500">
                <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl font-bold">&times;</button>
                <div class="flex flex-col items-center mb-4">
                    <i class="fas fa-receipt text-4xl text-green-500 mb-2"></i>
                    <h2 class="text-2xl font-extrabold mb-2 text-gray-800">Detalle de compra #<span x-text="order.id"></span></h2>
                </div>
                <template x-if="order">
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-semibold text-gray-700">Método de pago:</span>
                            <template x-if="order.payment_method === 'cash'"><span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-bold"><i class='fas fa-money-bill-wave'></i> Efectivo</span></template>
                            <template x-if="order.payment_method === 'transfer'"><span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-bold"><i class='fas fa-university'></i> Transferencia</span></template>
                            <template x-if="order.payment_method !== 'cash' && order.payment_method !== 'transfer'"><span class="text-gray-400">-</span></template>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-700">Estado:</span>
                            <span :class="order.status_color + ' px-3 py-1 rounded-full text-sm font-bold'" x-text="order.status"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-700">Notas:</span>
                            <span class="text-gray-600" x-text="order.notes || '-'" style="word-break: break-word;"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-700">Fecha:</span>
                            <span class="text-gray-800" x-text="order.created_at"></span>
                        </div>

                        <h3 class="text-xl font-bold mt-6 mb-3 text-gray-800">Productos:</h3>
                        <ul class="border rounded-lg divide-y divide-gray-200">
                            <template x-for="item in order.order_items" :key="item.product_name">
                                <li class="p-3 flex justify-between items-center text-sm">
                                    <div>
                                        <span class="font-semibold" x-text="item.product_name"></span>
                                        <span class="text-gray-600"> (x<span x-text="item.quantity"></span>)</span>
                                    </div>
                                    <span class="font-bold text-gray-900">$<span x-text="item.subtotal"></span></span>
                                </li>
                            </template>
                        </ul>
                        <div class="flex justify-between items-center text-lg font-bold text-gray-900 pt-4 border-t border-gray-200">
                            <span>Total del pedido:</span>
                            <span>$<span x-text="order.total"></span></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Modal de Cancelación --}}
        <div x-show="showCancelModal" x-cloak class="fixed inset-0 z-60 flex items-center justify-center bg-black bg-opacity-50">
            <div @click.away="showCancelModal = false" class="bg-white rounded-xl shadow-xl p-8 max-w-xs w-full text-center">
                <h3 class="text-lg font-bold mb-4">¿Cancelar esta compra?</h3>
                <p class="mb-6 text-gray-600">Esta acción no se puede deshacer.</p>
                <form method="POST" :action="'/orders/' + order.id + '/cancel'">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded font-bold hover:bg-red-700 transition">Sí, cancelar</button>
                    <button type="button" @click="showCancelModal = false" class="ml-2 px-4 py-2 bg-gray-200 rounded font-bold hover:bg-gray-300 transition">No</button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@push('styles')
<style>
    [x-cloak] {
        display: none !important;
    }

    /* Asegúrate de que esto esté en tu CSS principal */
    .truncate {
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endpush
@endsection
