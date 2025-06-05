@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Panel de Comprador</h1>
    @if(Auth::user()->role === 'admin')
        <p>Estás viendo el panel de comprador como <strong>administrador</strong>. Aquí puedes supervisar los pedidos de todos los compradores.</p>
    @else
        <p>Bienvenido, {{ auth()->user()->name }}. Eres un comprador.</p>
        <p>Aquí puedes ver tus pedidos y explorar productos de los vendedores.</p>
    @endif
</div>
@endsection 