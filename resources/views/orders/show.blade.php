@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles del Pedido</h1>
    <p><strong>Producto:</strong> {{ $order->product->name }}</p>
    <p><strong>Usuario:</strong> {{ $order->user->name }}</p>
    <p><strong>Cantidad:</strong> {{ $order->quantity }}</p>
    <p><strong>Estado:</strong> {{ $order->status }}</p>
    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Volver a la lista</a>
</div>
@endsection
