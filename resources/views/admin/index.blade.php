@extends('layouts.app')

@section('content')
@if(auth()->check() && auth()->user()->roles && auth()->user()->roles->pluck('name')->intersect(['admin', 'seller'])->isNotEmpty())
<div class="container mx-auto py-8 px-4">
    <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Panel de Vendedor</h1>

    <div class="text-center mb-6">
        <p class="text-xl font-medium text-gray-600">Bienvenido, {{ auth()->user()->name }} ({{ auth()->user()->roles->pluck('name')->join(', ') }}).</p>
    </div>

    <div class="bg-white shadow-md rounded-md p-3 max-w-md text-center mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Gestiona tu Negocio Online</h2>

        <ul class="space-y-4">
            <li>
                <a href="{{ route('seller.products.index') }}"
                    class="block p-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition duration-300 text-sm max-w-md w-full mx-auto">
                    <i class="fas fa-cogs mr-2"></i> Gestionar Productos
                </a>
            </li>
            <li>
                <a href="{{ route('orders.index') }}"
                    class="block p-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition duration-300 text-sm max-w-md w-full mx-auto">
                    <i class="fas fa-boxes mr-2"></i> Ver Pedidos
                </a>
            </li>
        </ul>
    </div>
</div>
@else
<div class="text-center py-8">
    <p class="text-xl font-medium text-red-600">No tienes permisos para acceder a esta sección.</p>
</div>
@endif

@endsection