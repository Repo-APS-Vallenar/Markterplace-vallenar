@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-10 px-4">
        <div class="flex flex-col md:flex-row gap-8">
            @php $rol = Auth::user()->role; @endphp
            @if ($rol === 'admin')
                <!-- Menú completo de administración -->
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
            @endif
            <!-- Contenido dinámico -->
            <main id="admin-content" class="flex-1 flex flex-col pt-4 px-8 w-full">
                @yield('admin-content')
            </main>
        </div>
    </div>
@endsection

{{-- Modal reutilizable para AJAX --}}
<div id="dynamic-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div id="dynamic-modal-content" class="bg-white rounded-lg shadow-lg w-full max-w-lg"></div>
</div>

@section('scripts')
    <!-- Navegación AJAX eliminada para navegación tradicional -->
    <script>
    function openModal(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar el modal: ' + response.status + ' ' + response.statusText);
            }
            return response.text();
        })
        .then(html => {
            const modalContent = document.getElementById('dynamic-modal-content');
            modalContent.innerHTML = '';
            modalContent.innerHTML = html;
            document.getElementById('dynamic-modal').classList.remove('hidden');
        })
        .catch(error => {
            alert(error.message);
        });
    }
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('dynamic-modal');
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
                document.getElementById('dynamic-modal-content').innerHTML = '';
            }
        });
    });
    // Script global para cerrar cualquier modal
    window.closeModal = function() {
        // Oculta el modal AJAX reutilizable
        const dynModal = document.getElementById('dynamic-modal');
        if (dynModal) {
            dynModal.classList.add('hidden');
            document.getElementById('dynamic-modal-content').innerHTML = '';
        }
        // Oculta cualquier otro modal con la clase de fondo
        document.querySelectorAll('.fixed.inset-0.bg-gray-500').forEach(function(modal) {
            modal.classList.add('hidden');
        });
    };
    </script>

    <!-- Script de modales de productos del vendedor -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Abrir modal de edición y cargar datos
    const editButtons = document.querySelectorAll('.edit-button');
    const editModal = document.getElementById('editModal');
    const editProductForm = document.getElementById('editProductForm');
    const deleteProductBtn = document.getElementById('deleteProductBtn');
    const deleteModal = document.getElementById('deleteModal');
    const deleteProductForm = document.getElementById('deleteProductForm');
    let currentProductId = null;

    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const product = JSON.parse(this.getAttribute('data-product'));
            currentProductId = product.id;
            editProductForm.action = `/seller/products/${product.id}`;
            document.getElementById('editName').value = product.name;
            document.getElementById('editPrice').value = product.price;
            document.getElementById('editDescription').value = product.description;
            document.getElementById('editCategory').value = product.category_id ?? '';
            editModal.classList.remove('hidden');
        });
    });

    // Botón eliminar en modal de edición
    if(deleteProductBtn) {
        deleteProductBtn.addEventListener('click', function() {
            if(currentProductId) {
                deleteProductForm.action = `/seller/products/${currentProductId}`;
                deleteModal.classList.remove('hidden');
            }
        });
    }

    // Cerrar modales
    ['closeModal', 'cancelModal'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.onclick = () => editModal.classList.add('hidden');
    });
    ['closeDeleteModal', 'cancelDeleteModal'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.onclick = () => deleteModal.classList.add('hidden');
    });

    // Modal de ver producto
    const showButtons = document.querySelectorAll('.show-button');
    const showModal = document.getElementById('showModal');
    ['closeShowModal', 'cancelShowModal'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.onclick = () => showModal.classList.add('hidden');
    });
    showButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const product = JSON.parse(this.getAttribute('data-product'));
            document.getElementById('showName').textContent = product.name;
            document.getElementById('showPrice').textContent = product.price;
            document.getElementById('showDescription').textContent = product.description;
            showModal.classList.remove('hidden');
        });
    });
    });
    </script>
@endsection