@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">Pedidos Recibidos</h1>
    @if($orders->isEmpty())
        <p>No tienes pedidos recibidos.</p>
    @else
    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Producto</th>
                <th class="px-4 py-2">Comprador</th>
                <th class="px-4 py-2">Cantidad</th>
                <th class="px-4 py-2">Total</th>
                <th class="px-4 py-2">Estado</th>
                <th class="px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td class="border px-4 py-2">{{ $order->id }}</td>
                <td class="border px-4 py-2">{{ $order->product->name ?? '-' }}</td>
                <td class="border px-4 py-2">{{ $order->user->name ?? '-' }}</td>
                <td class="border px-4 py-2">{{ $order->quantity }}</td>
                <td class="border px-4 py-2">${{ number_format($order->total, 0, ',', '.') }}</td>
                <td class="border px-4 py-2">{{ ucfirst($order->status) }}</td>
                <td class="border px-4 py-2 text-center">
                    <button type="button"
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                        @click="openModal('{{ route('seller.orders.show-modal', $order->id) }}')">
                        Ver Detalle
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection 