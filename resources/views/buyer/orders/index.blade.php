@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Mis pedidos</h1>
    @if($orders->isEmpty())
        <p>No tienes pedidos aún.</p>
    @else
        <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
            <thead>
                <tr>
                    <th class="px-4 py-2">Producto</th>
                    <th class="px-4 py-2">Cantidad</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td class="px-4 py-2">{{ $order->product->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $order->quantity }}</td>
                    <td class="px-4 py-2">{{ $order->status }}</td>
                    <td class="px-4 py-2">{{ $order->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection 