@extends('layouts.admin')

@section('admin-content')
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
                @foreach($orders as $i => $order)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2">{{ optional($order->product)->name }}</td>
                    <td class="px-4 py-2">{{ optional($order->user)->name }}</td>
                    <td class="px-4 py-2">{{ $order->quantity }}</td>
                    <td class="px-4 py-2">{{ ucfirst($order->delivery_method) }}</td>
                    <td class="px-4 py-2">{{ ucfirst($order->status) }}</td>
                    <td class="px-4 py-2">{{ $order->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-2 text-center space-x-2">
                        <button type="button"
                            class="show-order-button bg-teal-500 hover:bg-teal-600 text-white px-3 py-1 rounded text-sm"
                            data-order='@json($ordersForJson[$i])'>
                            Ver
                        </button>
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

{{-- Modal de Detalle de Pedido --}}
<div id="orderDetailModal" class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 hidden z-50">
  <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-lg mx-4">
    <div class="px-4 py-2 bg-gray-100 border-b flex justify-between items-center">
      <h3 class="text-lg font-semibold">Detalle del Pedido</h3>
      <button id="closeOrderDetailModal" type="button" class="text-gray-600 hover:text-gray-800">&times;</button>
    </div>
    <div class="px-4 py-4 space-y-4">
      <div>
        <h4 class="font-medium text-gray-700">ID Pedido:</h4>
        <p id="orderDetailId" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Producto:</h4>
        <p id="orderDetailProduct" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Comprador:</h4>
        <p id="orderDetailUser" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Cantidad:</h4>
        <p id="orderDetailQuantity" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Método de entrega:</h4>
        <p id="orderDetailDelivery" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Estado:</h4>
        <p id="orderDetailStatus" class="mt-1 text-gray-900"></p>
      </div>
    </div>
    <div class="px-4 py-3 bg-gray-100 text-right">
      <button type="button" id="cancelOrderDetailModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cerrar</button>
    </div>
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

        // Modal de detalle de pedido
        const orderDetailModal = document.getElementById('orderDetailModal');
        document.querySelectorAll('.show-order-button').forEach(btn => {
            btn.addEventListener('click', () => {
                const o = JSON.parse(btn.getAttribute('data-order'));
                document.getElementById('orderDetailId').textContent = o.id;
                document.getElementById('orderDetailProduct').textContent = o.product;
                document.getElementById('orderDetailUser').textContent = o.user;
                document.getElementById('orderDetailQuantity').textContent = o.quantity;
                document.getElementById('orderDetailDelivery').textContent = o.delivery_method === 'domicilio' ? 'En domicilio del vendedor' : 'A acordar con el vendedor';
                document.getElementById('orderDetailStatus').textContent = o.status.charAt(0).toUpperCase() + o.status.slice(1);
                orderDetailModal.classList.remove('hidden');
            });
        });
        document.getElementById('closeOrderDetailModal').onclick = () => orderDetailModal.classList.add('hidden');
        document.getElementById('cancelOrderDetailModal').onclick = () => orderDetailModal.classList.add('hidden');
    });
</script>
@endsection