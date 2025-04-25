@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Bienvenido, {{ auth()->user()->name }}</h1>
    <p>Explora productos de nuestros productores locales.</p>

    <div class="mt-4">
        <a href="{{ route('products.index') }}" class="btn btn-primary">Ver Productos</a>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Mis Pedidos</a>
    </div>
</div>
@endsection
