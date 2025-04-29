@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Pedidos</h1>

    {{-- Alerta de éxito --}}
    @if(session('success'))
    <div class="mb-4 p-4 rounded border border-green-400 bg-green-100 text-green-800 flex justify-between items-center alert-success">
        <div>
            <strong>¡Éxito!</strong> {{ session('success') }}
        </div>
        <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">&times;</button>
    </div>
    @endif

    <div class="overflow-x-auto bg-white shadow-md rounded">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-gray-600">Producto</th>
                    <th class="px-4 py-2 text-left text-gray-600">Comprador</th>
                    <th class="px-4 py-2 text-left text-gray-600">Cantidad</th>
                    <th class="px-4 py-2 text-left text-gray-600">Entrega</th>
                    <th class="px-4 py-2 text-left text-gray-600">Estado</th>
                    <th class="px-4 py-2 text-left text-gray-600">Fecha</th>
                    <th class="px-4 py-2 text-center text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $order->product->name }}</td>
                    <td class="px-4 py-2">{{ $order->user->name }}</td>
                    <td class="px-4 py-2">{{ $order->quantity }}</td>
                    <td class="px-4 py-2">{{ ucfirst($order->delivery_method) }}</td>
                    <td class="px-4 py-2">{{ ucfirst($order->status) }}</td>
                    <td class="px-4 py-2">{{ $order->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-2 text-center space-x-2">
                        @can('view', $order)
                        <a href="{{ route('orders.show', $order) }}" class="bg-teal-500 hover:bg-teal-600 text-white px-3 py-1 rounded text-sm">Ver</a>
                        @endcan
                        @can('update', $order)
                        <a href="{{ route('orders.edit', $order) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">Editar</a>
                        @endcan
                        @can('delete', $order)
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar pedido?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Eliminar</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Desaparece la alerta automáticamente
    document.addEventListener('DOMContentLoaded', () => {
        const alert = document.querySelector('.alert-success');
        if (alert) {
            setTimeout(() => alert.remove(), 5000);
        }
    });
</script>
@endsection