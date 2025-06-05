<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'seller')
            ->with(['products' => function($q) {
                $q->with('orders');
            }]);

        // Filtro de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro de estado
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filtro de fechas
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->paginate(10);
        $users->getCollection()->transform(function($u) {
            $u->products_count = $u->products->count();
            $u->orders_count = $u->products->reduce(function($carry, $product) {
                return $carry + $product->orders->count();
            }, 0);
            return $u;
        });
        return view('admin.users.index', compact('users'));
    }

    public function buyers(Request $request)
    {
        $query = User::where('role', 'buyer')
            ->with('orders');

        // Filtro de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro de estado
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filtro de fechas
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $buyers = $query->paginate(10);
        $buyersForJson = $buyers->getCollection()->map(function($b) {
            return [
                'id' => $b->id,
                'name' => $b->name,
                'email' => $b->email,
                'status' => $b->is_active ? 'Activo' : 'Suspendido',
                'orders_count' => $b->orders->count(),
                'created_at' => $b->created_at->format('d/m/Y'),
                'last_login' => $b->last_login_at ? $b->last_login_at->format('d/m/Y H:i') : 'Nunca',
            ];
        });
        return view('admin.buyers.index', compact('buyers', 'buyersForJson'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'seller';

        $user = User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Vendedor creado.');
    }

    public function edit(User $user)
    {
        abort_unless($user->role === 'seller', 404);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'seller', 404);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|confirmed|min:6',
        ]);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return back()->with('success', 'Vendedor actualizado.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->role === 'seller', 404);
        $user->delete();
        return back()->with('success', 'Vendedor eliminado.');
    }
}
