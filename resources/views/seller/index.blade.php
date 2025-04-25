<!-- resources/views/seller/index.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Panel de Vendedor</h1>
    <p>Bienvenido, {{ auth()->user()->name }}. Eres un vendedor.</p>
    <p>Aquí puedes gestionar tus productos, ver tus pedidos y actualizar tu información.</p>
</div>
@endsection
