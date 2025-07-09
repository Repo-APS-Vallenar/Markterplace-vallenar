{{-- resources/views/seller/orders/index.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Pedidos Recibidos</h1>

    {{-- Mensajes de sesión --}}
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif
    @if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    @if($orders->isEmpty())
    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500 text-lg">
        <i class="fas fa-box-open text-4xl mb-4 text-gray-300"></i><br>
        No tienes pedidos recibidos.
    </div>
    @else
    <div class="overflow-x-auto bg-white shadow-md rounded my-6">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        ID Pedido
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Productos
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Comprador
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Cantidad Total
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Total Pedido
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Estado
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Fecha Pedido
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        {{ $order->id }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        @if($order->orderItems->isNotEmpty())
                        {{ $order->orderItems->first()->product->name ?? 'Producto desconocido' }}
                        @if($order->orderItems->count() > 1)
                        <span class="text-gray-500 text-sm"> (+{{ $order->orderItems->count() - 1 }} más)</span>
                        @endif
                        @else
                        <span class="text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        {{ $order->user->name ?? 'Usuario Desconocido' }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                        {{ $order->orderItems->sum('quantity') }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        ${{ number_format($order->total, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
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
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                        <div class="flex flex-col items-center justify-center space-y-2"> {{-- Añade este div --}}
                            {{-- Usa onclick y showOrderDetailsModal para JavaScript vanilla --}}
                            <button type="button" onclick="showOrderDetailsModal({{ $order->id }})" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition w-full max-w-[150px]">
                                Ver Detalle
                            </button>
                            @if($order->status === 'pending')
                            <form action="{{ route('seller.orders.update-status', $order->id) }}" method="POST" class="w-full max-w-[150px]"> {{-- Remueve inline-block ml-2 y ajusta el ancho --}}
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition w-full">
                                    Marcar como Procesado
                                </button>
                            </form>
                            @endif
                        </div> {{-- Cierra el div --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- MODAL DE DETALLE DEL PEDIDO (Mismo modal que te he proporcionado antes) --}}
<div id="orderDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-900">Detalle del Pedido</h3>
            <button onclick="hideOrderDetailsModal()" class="text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none">&times;</button>
        </div>
        <div id="orderDetailsContent" class="py-4 text-gray-700">
            Cargando detalles del pedido...
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="hideOrderDetailsModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">Cerrar</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showOrderDetailsModal(orderId) {
        document.getElementById('orderDetailsModal').classList.remove('hidden');
        // Cambia esta línea:
        // A esta:
        fetch("{{ route('seller.orders.showModal', ['id' => 'TEMP_ID_PLACEHOLDER']) }}".replace('TEMP_ID_PLACEHOLDER', orderId))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('orderDetailsContent').innerHTML = html;
            })
            .catch(error => {
                console.error('Error al cargar los detalles del pedido:', error);
                document.getElementById('orderDetailsContent').innerHTML = '<p class="text-red-500">Error al cargar los detalles del pedido.</p>';
            });
    }

    function hideOrderDetailsModal() {
        document.getElementById('orderDetailsModal').classList.add('hidden');
        document.getElementById('orderDetailsContent').innerHTML = 'Cargando detalles del pedido...';
    }
</script>
@endpush
@endsection