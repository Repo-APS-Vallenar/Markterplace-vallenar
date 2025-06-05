<!-- resources/views/seller/products/index.blade.php -->
@extends('layouts.admin')

@section('admin-content')
    <h1 class="text-2xl font-bold mb-4">Lista de Productos</h1>
    @if(session('success'))
    <div class="alert-success mb-4 p-4 rounded border border-green-400 bg-green-100 text-green-800 flex justify-between items-center">
        <div>
            <strong>¡Éxito!</strong> {{ session('success') }}
        </div>
        <button onclick="this.parentElement.remove()"
            class="text-green-700 hover:text-green-900">
            &times;
        </button>
    </div>
    @endif
    <!-- Botón Crear Producto -->
    <div class="mb-4">
        <button id="createButton"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
            Nuevo Producto
        </button>
    </div>

    <div class="overflow-x-auto bg-white shadow-md rounded">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-gray-600">Nombre</th>
                    <th class="px-4 py-2 text-left text-gray-600">Precio</th>
                    <th class="px-4 py-2 text-left text-gray-600">Descripción</th>
                    <th class="px-4 py-2 text-center text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $product->name }}</td>
                    <td class="px-4 py-2">{{ number_format($product->price, 2) }}</td>
                    <td class="px-4 py-2">{{ $product->description }}</td>
                    <td class="px-4 py-2 text-center space-x-2">
                        <button type="button"
                            class="edit-button bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                            data-product='@json($product)'>
                            Editar
                        </button>
                        <button type="button"
                            class="show-button bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded"
                            data-product='@json($product)'>
                            Ver
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modales Dinámicos --}}
    @include('seller.products.modals.create-modal')
    @include('seller.products.modals.edit-modal')
    @include('seller.products.modals.show-modal')
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- CREATE MODAL ---
        const createModal = document.getElementById('createModal');
        const openCreateBtn = document.getElementById('createButton');
        const closeCreateBtn = document.getElementById('closeCreateModal');
        const cancelCreateBtn = document.getElementById('cancelCreateModal');

        openCreateBtn.onclick = () => createModal.classList.remove('hidden');
        closeCreateBtn.onclick = () => createModal.classList.add('hidden');
        cancelCreateBtn.onclick = () => createModal.classList.add('hidden');

        // --- EDIT MODAL ---
        const editModal = document.getElementById('editModal');
        document.querySelectorAll('.edit-button').forEach(btn => {
            btn.addEventListener('click', () => {
                const p = JSON.parse(btn.getAttribute('data-product'));
                const modal = editModal;
                const nameInput = modal.querySelector('#editName');
                const priceInput = modal.querySelector('#editPrice');
                const descInput = modal.querySelector('#editDescription');
                const form = modal.querySelector('#editProductForm');

                nameInput.value = p.name;
                priceInput.value = p.price;
                descInput.value = p.description;
                form.action = `${window.location.origin}/seller/products/${p.id}`;
                modal.classList.remove('hidden');
            });
        });
        document.getElementById('closeModal').onclick = () => editModal.classList.add('hidden');
        document.getElementById('cancelModal').onclick = () => editModal.classList.add('hidden');

        // --- SHOW MODAL ---
        const showModal = document.getElementById('showModal');
        document.querySelectorAll('.show-button').forEach(btn => {
            btn.addEventListener('click', () => {
                const p = JSON.parse(btn.getAttribute('data-product'));
                showModal.querySelector('#showName').textContent = p.name;
                showModal.querySelector('#showPrice').textContent = p.price;
                showModal.querySelector('#showDescription').textContent = p.description;
                showModal.classList.remove('hidden');
            });
        });
        document.getElementById('closeShowModal').onclick = () => showModal.classList.add('hidden');
        document.getElementById('cancelShowModal').onclick = () => showModal.classList.add('hidden');
    });

    if (document.querySelector('.alert-success')) {
        setTimeout(() => {
            document.querySelector('.alert-success').remove();
        }, 5000);
    }
</script>
<script>
    // Base URL para los productos del seller
    const sellerProductsBaseUrl = "{{ url('seller/products') }}";
</script>
@endsection