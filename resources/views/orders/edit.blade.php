@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <h1 class="text-2xl font-bold mb-6">Editar Pedido #{{ $order->id }}</h1>

    <form action="{{ route('orders.update', $order) }}" method="POST"
        class="bg-white shadow-md rounded p-6 space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Cantidad</label>
            <input type="number" name="quantity" min="1"
                value="{{ $order->quantity }}" required
                class="mt-1 block w-full border-gray-300 rounded p-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Método de entrega</label>
            <select name="delivery_method" required
                class="mt-1 block w-full border-gray-300 rounded p-2">
                <option value="domicilio"
                    {{ $order->delivery_method==='domicilio'? 'selected':'' }}>
                    En domicilio del vendedor
                </option>
                <option value="acordar"
                    {{ $order->delivery_method==='acordar'? 'selected':'' }}>
                    A acordar con el vendedor
                </option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Estado</label>
            <select name="status" required
                class="mt-1 block w-full border-gray-300 rounded p-2">
                <option value="pending" {{ $order->status==='pending'?    'selected':'' }}>Pendiente</option>
                <option value="processing" {{ $order->status==='processing'? 'selected':'' }}>En proceso</option>
                <option value="completed" {{ $order->status==='completed'?  'selected':'' }}>Completado</option>
                <option value="cancelled" {{ $order->status==='cancelled'?  'selected':'' }}>Cancelado</option>
            </select>
        </div>

        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Guardar cambios
        </button>
        <a href="{{ route('orders.index') }}"
            class="ml-4 text-gray-600 hover:underline">
            Cancelar
        </a>
    </form>
</div>
@endsection