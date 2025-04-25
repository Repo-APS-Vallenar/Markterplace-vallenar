@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Pedido</h1>
    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="product_id" class="form-label">Producto</label>
            <select class="form-control" id="product_id" name="product_id" required>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="user_id" class="form-label">Usuario</label>
            <select class="form-control" id="user_id" name="user_id" required>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Estado</label>
            <select class="form-control" id="status" name="status" required>
                <option value="pendiente">Pendiente</option>
                <option value="completado">Completado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Crear Pedido</button>
    </form>
</div>
@endsection
