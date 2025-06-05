@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-10 px-4">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar de gestión admin -->
            <aside
                class="w-full md:w-64 bg-gradient-to-b from-gray-50 to-gray-200 shadow-lg rounded-xl p-6 mb-8 md:mb-0 flex-shrink-0">
                <h2 class="text-xl font-extrabold text-gray-700 mb-6 flex items-center gap-2">
                    <i class="fas fa-tools text-blue-500"></i> Panel de administración
                </h2>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('admin.index') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg transition font-medium hover:bg-blue-100 hover:text-blue-700 {{ request()->routeIs('admin.index') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg transition font-medium hover:bg-blue-100 hover:text-blue-700 {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                            <i class="fas fa-users-cog"></i> Gestión de usuarios
                        </a>
                    </li>
                    <li>
                        <span class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-400 cursor-not-allowed">
                            <i class="fas fa-chart-bar"></i> Reportes (próximamente)
                        </span>
                    </li>
                    <li>
                        <span class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-400 cursor-not-allowed">
                            <i class="fas fa-desktop"></i> Monitoreo (próximamente)
                        </span>
                    </li>
                    <li>
                        <a href="{{ route('seller.products.index') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg transition font-medium hover:bg-blue-100 hover:text-blue-700 {{ request()->routeIs('seller.products.*') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                            <i class="fas fa-box"></i> Ver todos los productos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('orders.index') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg transition font-medium hover:bg-blue-100 hover:text-blue-700 {{ request()->routeIs('orders.*') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                            <i class="fas fa-shopping-cart"></i> Ver todos los pedidos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.supervise_sellers') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg transition font-medium hover:bg-blue-100 hover:text-blue-700 {{ request()->routeIs('admin.supervise_sellers') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                            <i class="fas fa-user-tie"></i> Supervisar vendedor
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.buyers.index') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg transition font-medium hover:bg-blue-100 hover:text-blue-700 {{ request()->routeIs('admin.buyers.*') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                            <i class="fas fa-user-check"></i> Supervisar comprador
                        </a>
                    </li>
                </ul>
            </aside>
            <!-- Contenido dinámico -->
            <main id="admin-content" class="flex-1 flex flex-col pt-4 px-8 w-full">
                @yield('admin-content')
            </main>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Interceptar clics en el sidebar
        document.querySelectorAll('aside a[href]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Solo enlaces internos del panel admin
                const url = link.getAttribute('href');
                if (url && url.startsWith(window.location.origin)) return; // Si es absoluta, dejar pasar
                if (link.target === '_blank' || link.hasAttribute('download')) return;
                e.preventDefault();
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error al cargar la vista');
                    return response.text();
                })
                .then(html => {
                    // Extraer solo el contenido de @section('admin-content')
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    let content = temp.querySelector('#admin-content');
                    if (!content) {
                        // fallback: buscar main.flex-1
                        content = temp.querySelector('main.flex-1');
                    }
                    if (content) {
                        document.getElementById('admin-content').innerHTML = content.innerHTML;
                        window.history.pushState({}, '', url);
                        // Ejecutar scripts inline de la nueva vista
                        temp.querySelectorAll('script').forEach(function(script) {
                            if (script.src) {
                                const s = document.createElement('script');
                                s.src = script.src;
                                document.body.appendChild(s);
                            } else {
                                eval(script.innerText);
                            }
                        });
                    } else {
                        document.getElementById('admin-content').innerHTML = '<div class="p-8 text-red-600">No se pudo cargar el contenido dinámicamente.</div>';
                    }
                })
                .catch(() => {
                    document.getElementById('admin-content').innerHTML = '<div class="p-8 text-red-600">Error al cargar el contenido.</div>';
                });
            });
        });
        // Soporte para navegación con el botón atrás/adelante
        window.addEventListener('popstate', function() {
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                let content = temp.querySelector('#admin-content');
                if (!content) content = temp.querySelector('main.flex-1');
                if (content) document.getElementById('admin-content').innerHTML = content.innerHTML;
            });
        });
    });
</script>
@endsection