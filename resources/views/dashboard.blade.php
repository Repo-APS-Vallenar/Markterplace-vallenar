<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('INICIO MARKETPLACE') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Ya estas en el dashboard") }}
                </div>
            </div>
        </div>
    </div>

    @section('navbar-modulos')
        @php $rol = Auth::user()->role; @endphp
        @if ($rol === 'admin')
            <a href="{{ route('admin.index') }}" class="mx-2">Administrador</a>
            <a href="{{ route('seller.index') }}" class="mx-2">Vendedor</a>
            <a href="{{ route('buyer.index') }}" class="mx-2">Comprador</a>
        @elseif ($rol === 'seller')
            <a href="{{ route('seller.index') }}" class="mx-2">Vendedor</a>
        @elseif ($rol === 'buyer')
            <a href="{{ route('buyer.index') }}" class="mx-2">Comprador</a>
        @endif
    @endsection

</x-app-layout>
