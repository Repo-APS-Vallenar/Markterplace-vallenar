@extends('layouts.admin')
@section('admin-content')
<div class="container mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Supervisión de Compradores</h1>

  {{-- Buscador y Filtros --}}
  <div class="mb-6 bg-white p-4 rounded-lg shadow">
    <form action="{{ route('admin.buyers.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}" 
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
          placeholder="Nombre o email...">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
          <option value="">Todos</option>
          <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
          <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendido</option>
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
      <div class="md:col-span-4 flex justify-end">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Filtrar
        </button>
        <a href="{{ route('admin.buyers.index') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
          Limpiar
        </a>
      </div>
    </form>
  </div>

  {{-- Tabla de Compradores --}}
  <div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 font-semibold text-gray-700">Nombre</th>
          <th class="px-4 py-2 font-semibold text-gray-700">Email</th>
          <th class="px-4 py-2 font-semibold text-gray-700">Estado</th>
          <th class="px-4 py-2 font-semibold text-gray-700">Pedidos</th>
          <th class="px-4 py-2 font-semibold text-gray-700">Registro</th>
          <th class="px-4 py-2 font-semibold text-gray-700 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($buyers as $i => $b)
        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition">
          <td class="px-4 py-2">{{ $b->name }}</td>
          <td class="px-4 py-2">{{ $b->email }}</td>
          <td class="px-4 py-2">
            <span class="inline-block px-2 py-1 rounded text-xs font-semibold
              {{ $b->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
              {{ $b->is_active ? 'Activo' : 'Suspendido' }}
            </span>
          </td>
          <td class="px-4 py-2 text-center">{{ $b->orders->count() }}</td>
          <td class="px-4 py-2">{{ $b->created_at->format('d/m/Y') }}</td>
          <td class="px-4 py-2 text-center space-x-1">
            <button type="button"
              class="show-buyer-button inline-flex items-center gap-1 px-3 py-1 bg-teal-500 text-white rounded hover:bg-teal-600 transition"
              data-buyer='@json($buyersForJson[$i])'>
              <i class="fas fa-eye"></i> Ver
            </button>
            <a href="{{ route('admin.users.edit', $b) }}"
              class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition">
              <i class="fas fa-edit"></i> Editar
            </a>
            <form action="{{ route('admin.users.destroy', $b) }}" method="POST" class="inline">
              @csrf @method('DELETE')
              <button type="submit"
                class="inline-flex items-center gap-1 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition"
                onclick="return confirm('¿Eliminar este comprador?')">
                <i class="fas fa-trash"></i> Borrar
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="px-4 py-4 text-center text-gray-600">No se encontraron compradores.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginación --}}
  <div class="mt-4">
    {{ $buyers->links() }}
  </div>
</div>

{{-- Modal de Detalle de Comprador --}}
<div id="buyerDetailModal" class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 hidden z-50">
  <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-lg mx-4">
    <div class="px-4 py-2 bg-gray-100 border-b flex justify-between items-center">
      <h3 class="text-lg font-semibold">Detalle del Comprador</h3>
      <button id="closeBuyerDetailModal" type="button" class="text-gray-600 hover:text-gray-800">&times;</button>
    </div>
    <div class="px-4 py-4 space-y-4">
      <div>
        <h4 class="font-medium text-gray-700">ID:</h4>
        <p id="buyerDetailId" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Nombre:</h4>
        <p id="buyerDetailName" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Email:</h4>
        <p id="buyerDetailEmail" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Estado:</h4>
        <p id="buyerDetailStatus" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Total de Pedidos:</h4>
        <p id="buyerDetailOrders" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Fecha de Registro:</h4>
        <p id="buyerDetailCreated" class="mt-1 text-gray-900"></p>
      </div>
      <div>
        <h4 class="font-medium text-gray-700">Último Acceso:</h4>
        <p id="buyerDetailLastLogin" class="mt-1 text-gray-900"></p>
      </div>
    </div>
    <div class="px-4 py-3 bg-gray-100 text-right">
      <button type="button" id="cancelBuyerDetailModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cerrar</button>
    </div>
  </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Modal de detalle de comprador
        const buyerDetailModal = document.getElementById('buyerDetailModal');
        document.querySelectorAll('.show-buyer-button').forEach(btn => {
            btn.addEventListener('click', () => {
                const b = JSON.parse(btn.getAttribute('data-buyer'));
                document.getElementById('buyerDetailId').textContent = b.id;
                document.getElementById('buyerDetailName').textContent = b.name;
                document.getElementById('buyerDetailEmail').textContent = b.email;
                document.getElementById('buyerDetailStatus').textContent = b.status;
                document.getElementById('buyerDetailOrders').textContent = b.orders_count;
                document.getElementById('buyerDetailCreated').textContent = b.created_at;
                document.getElementById('buyerDetailLastLogin').textContent = b.last_login;
                buyerDetailModal.classList.remove('hidden');
            });
        });
        document.getElementById('closeBuyerDetailModal').onclick = () => buyerDetailModal.classList.add('hidden');
        document.getElementById('cancelBuyerDetailModal').onclick = () => buyerDetailModal.classList.add('hidden');
    });
</script>
@endsection
@endsection 