@extends('layouts.admin')
@section('admin-content')
    <div x-data="userModal()">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-bold mb-4">Gestión de usuarios</h1>

            {{-- Buscador y Filtros --}}
            <div class="mb-6 bg-white p-4 rounded-lg shadow">
                <form action="{{ route('admin.users.index') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                            placeholder="Nombre o email...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                        <select name="role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <option value="">Todos</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            <option value="seller" {{ request('role') == 'seller' ? 'selected' : '' }}>Vendedor</option>
                            <option value="buyer" {{ request('role') == 'buyer' ? 'selected' : '' }}>Comprador</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha hasta</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    </div>
                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 font-semibold transition">Filtrar</button>
                        <a href="{{ route('admin.users.index') }}"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 font-semibold transition">Limpiar</a>
                    </div>
                </form>
            </div>

            {{-- Tabla de Usuarios --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 font-semibold text-gray-700">Nombre</th>
                            <th class="px-4 py-2 font-semibold text-gray-700">Email</th>
                            <th class="px-4 py-2 font-semibold text-gray-700">Rol</th>
                            <th class="px-4 py-2 font-semibold text-gray-700">Productos</th>
                            <th class="px-4 py-2 font-semibold text-gray-700">Pedidos recibidos</th>
                            <th class="px-4 py-2 font-semibold text-gray-700">Registro</th>
                            <th class="px-4 py-2 font-semibold text-gray-700 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $i => $user)
                            @php
                                $userJson = [
                                    'id' => $user->id,
                                    'name' => $user->name,
                                    'email' => $user->email,
                                    'role' => $user->role,
                                    'products_count' => $user->products_count ?? 0,
                                    'orders_count' => $user->orders_count ?? 0,
                                    'created_at' => $user->created_at->format('d/m/Y'),
                                ];
                            @endphp
                            <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition">
                                <td class="px-4 py-2">{{ $user->name }}</td>
                                <td class="px-4 py-2">{{ $user->email }}</td>
                                <td class="px-4 py-2 text-center capitalize">{{ $user->role }}</td>
                                <td class="px-4 py-2 text-center">{{ $user->products_count ?? '—' }}</td>
                                <td class="px-4 py-2 text-center">{{ $user->orders_count ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 text-center">
                                    <div class="inline-flex gap-1">
                                        <button type="button"
                                            class="show-user-button flex items-center justify-center w-12 h-12 bg-teal-500 text-white rounded hover:bg-teal-600 transition"
                                            data-user='@json($userJson)'>
                                            <i class="fas fa-eye text-lg"></i>
                                        </button>
                                        <button type="button"
                                            class="flex items-center justify-center w-12 h-12 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition"
                                            onclick="window.openModal('{{ route('admin.users.edit-modal', $user) }}')">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="flex items-center justify-center w-12 h-12 bg-red-500 text-white rounded hover:bg-red-600 transition"
                                                onclick="return confirm('¿Eliminar este usuario?')">
                                                <i class="fas fa-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-4 text-center text-gray-600">No se encontraron usuarios.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>

        {{-- Modal de Detalle de Usuario --}}
        <div id="userDetailModal"
            class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 hidden z-50">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-lg mx-4">
                <div class="px-4 py-2 bg-gray-100 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Detalle del Usuario</h3>
                    <button id="closeUserDetailModal" type="button"
                        class="text-gray-600 hover:text-gray-800">&times;</button>
                </div>
                <div class="px-4 py-4 space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-700">ID:</h4>
                        <p id="userDetailId" class="mt-1 text-gray-900"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700">Nombre:</h4>
                        <p id="userDetailName" class="mt-1 text-gray-900"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700">Email:</h4>
                        <p id="userDetailEmail" class="mt-1 text-gray-900"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700">Rol:</h4>
                        <p id="userDetailRole" class="mt-1 text-gray-900"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700">Total de Productos:</h4>
                        <p id="userDetailProducts" class="mt-1 text-gray-900"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700">Pedidos Recibidos:</h4>
                        <p id="userDetailOrders" class="mt-1 text-gray-900"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700">Fecha de Registro:</h4>
                        <p id="userDetailCreated" class="mt-1 text-gray-900"></p>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-100 text-right">
                    <button type="button" id="cancelUserDetailModal"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function userModal() {
            return {
                show: false,
                modalContent: '',
                openModal(type, id = null) {
                    this.show = true;
                    let url = '';
                    if (type === 'create') url = '{{ route('admin.users.create') }}';
                    if (type === 'edit') url = '{{ url('admin/users') }}/' + id + '/edit';
                    if (type === 'delete') url = '{{ url('admin/users') }}/' + id + '/delete-confirm';

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error en la respuesta del servidor');
                            }
                            return response.text();
                        })
                        .then(html => {
                            this.modalContent = html;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al cargar el modal');
                        });
                },
                closeModal() {
                    this.show = false;
                    this.modalContent = '';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Modal de detalle de usuario
            const userDetailModal = document.getElementById('userDetailModal');
            document.querySelectorAll('.show-user-button').forEach(btn => {
                btn.addEventListener('click', () => {
                    const u = JSON.parse(btn.getAttribute('data-user'));
                    document.getElementById('userDetailId').textContent = u.id;
                    document.getElementById('userDetailName').textContent = u.name;
                    document.getElementById('userDetailEmail').textContent = u.email;
                    document.getElementById('userDetailRole').textContent = u.role;
                    document.getElementById('userDetailProducts').textContent = u.products_count;
                    document.getElementById('userDetailOrders').textContent = u.orders_count;
                    document.getElementById('userDetailCreated').textContent = u.created_at;
                    userDetailModal.classList.remove('hidden');
                });
            });
            document.getElementById('closeUserDetailModal').addEventListener('click', () => {
                userDetailModal.classList.add('hidden');
            });
            document.getElementById('cancelUserDetailModal').addEventListener('click', () => {
                userDetailModal.classList.add('hidden');
            });
        });
    </script>
@endsection
