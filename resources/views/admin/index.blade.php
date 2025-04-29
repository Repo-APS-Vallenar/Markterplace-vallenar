@extends('layouts.app')

@section('content')
@hasanyrole('admin|seller')
<div class="container mx-auto py-8 px-4">
    <!-- Título del panel de administración -->
    <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Panel de Vendedor</h1>

    <!-- Mensaje de bienvenida -->
    <div class="text-center mb-6">
    <p class="text-xl font-medium text-gray-600">Bienvenido, {{ auth()->user()->name }} ({{ auth()->user()->getRoleNames()->join(', ') }}).</p>
    </div>

    <!-- Opciones de navegación -->
    <div class="bg-white shadow-md rounded-md p-3 max-w-md text-center mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Gestiona tu Negocio Online</h2>

        <ul class="space-y-4">
            <!-- Gestionar productos -->
            <li>
                <a href="{{ route('seller.products.index') }}"
                    class="block p-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition duration-300 text-sm max-w-md w-full mx-auto">
                    <i class="fas fa-cogs mr-2"></i> Gestionar Productos
                </a>
            </li>

            <!-- Ver pedidos -->
            <li>
                <a href="{{ route('orders.index') }}"
                    class="block p-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition duration-300 text-sm max-w-md w-full mx-auto">
                    <i class="fas fa-boxes mr-2"></i> Ver Pedidos
                </a>
            </li>

            <!-- Otras opciones pueden ser agregadas aquí -->

        </ul>
    </div>
</div>
@else
<div class="text-center py-8">
    <p class="text-xl font-medium text-red-600">No tienes permisos para acceder a esta sección.</p>
</div>
@endhasanyrole
@endsection