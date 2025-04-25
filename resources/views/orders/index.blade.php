@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Pedidos</h1>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">Crear Pedido</a>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Usuario</th>
                <th>Cantidad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->product->name }}</td>
                <td>{{ $order->user->name }}</td>
                <td>{{ $order->quantity }}</td>
                <td>{{ $order->status }}</td>
                <td>
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info">Ver</a>
                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
