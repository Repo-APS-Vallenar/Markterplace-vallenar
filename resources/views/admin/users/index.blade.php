@extends('layouts.admin')
@section('admin-content')
    <div x-data="userModal()">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <button type="button" @click="openModal('create')" class="mb-4 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Nuevo Usuario</button>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($user->role) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button type="button" @click="openModal('edit', {{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                        <button type="button" @click="openModal('delete', {{ $user->id }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="show" style="display: none;" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
                <template x-if="modalContent">
                    <div x-html="modalContent"></div>
                </template>
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
    </script>
@endsection