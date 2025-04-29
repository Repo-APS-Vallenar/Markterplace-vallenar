@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <h1 class="text-2xl font-bold mb-6">Pedido #{{ $order->id }}</h1>

    <div class="bg-white shadow-md rounded p-6 space-y-4">
        <p><strong>Producto:</strong> {{ $order->product->name }}</p>
        <p><strong>Comprador:</strong> {{ $order->user->name }}</p>
        <p><strong>Cantidad:</strong> {{ $order->quantity }}</p>
        <p>
            <strong>Método de entrega:</strong>
            {{ $order->delivery_method === 'domicilio'
              ? 'En domicilio del vendedor'
              : 'A acordar con el vendedor' }}
        </p>
        <p><strong>Estado:</strong> {{ ucfirst($order->status) }}</p>
    </div>

    <a href="{{ route('orders.index') }}"
        class="mt-6 inline-block text-indigo-600 hover:underline">
        Volver al listado
    </a>
</div>
@endsection