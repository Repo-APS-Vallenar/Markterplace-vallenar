@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <!-- Mensaje de éxito -->
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show"
                class="alert alert-success bg-green-500 text-white p-4 rounded-md mb-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-white font-bold text-lg">&times;</button>
            </div>
        @endif

        <!-- Botón para crear producto -->
        <button id="createProductModalButton" onclick="openModal()" class="px-4 py-2 bg-green-500 text-white rounded-md">
            Nuevo Producto
        </button>

        <!-- Modal de creación de producto -->
        @include('seller.products.modals.create-modal')

        <!-- Tabla de productos -->
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="table-auto w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-sm font-medium text-gray-700">Nombre</th>
                        <th class="px-4 py-2 text-sm font-medium text-gray-700">Precio</th>
                        <th class="px-4 py-2 text-sm font-medium text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="border-b">
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $product->name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">${{ number_format($product->price) }}</td>
                            <td class="px-4 py-2 text-sm">
                                <!-- Botón de edición -->
                                <button id="editProductModalButton" onclick="openModalEdit()"
                                    class="bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600"
                                    data-bs-toggle="editProductModal" data-bs-target="#editProductModal">
                                    Editar
                                </button>

                                <!-- Botón de ver detalles -->
                                <button id="showProductModalButton" onclick="openModalShow()"
                                    class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600"
                                    data-bs-toggle="showProductModal" data-bs-target="#showProductModal">
                                    Ver
                                </button>

                                <!-- Modal de edición -->
                                @include('seller.products.modals.edit-modal', ['product' => $product])

                                <!-- Modal de visualización -->
                                @include('seller.products.modals.show-modal', ['product' => $product])

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center px-4 py-2 text-sm text-gray-700">No hay productos disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        // Función para abrir el modal de creación
        function openModal() {
            var modal = document.getElementById('createProductModal');
            if (modal) {
                modal.classList.remove('hidden');
            } else {
                console.error("No se encontró el modal con el ID 'createProductModal'.");
            }
        }
        function openModalEdit() {
            var modal = document.getElementById('editProductModal');
            if (modal) {
                modal.classList.remove('hidden');
            } else {
                console.error("No se encontró el modal con el ID 'editProductModal'.");
            }
        }
        function openModalShow() {
            var modal = document.getElementById('showProductModal');
            if (modal) {
                modal.classList.remove('hidden');
            } else {
                console.error("No se encontró el modal con el ID 'showProductModal'.");
            }
        }

        // Función para cerrar modal createProductModal
        function closeModal(productId = null) {
            var modal;
            modal = document.getElementById('createProductModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
        // Función para cerrar modal editProductModal
        function closeModalEdit(productId = null) {
            var modal;
            modal = document.getElementById('editProductModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
        // Función para cerrar modal showProductModal
        function closeModalShow(productId = null) {
            var modal;
            modal = document.getElementById('showProductModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // Inicializar modales de Bootstrap
        document.addEventListener('DOMContentLoaded', function () {
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function (modal) {
                new bootstrap.Modal(modal);  // Esto inicializa el modal si no se ha hecho
            });
        });

    </script>

@endsection