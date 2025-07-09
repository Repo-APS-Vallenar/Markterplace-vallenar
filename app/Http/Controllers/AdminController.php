<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{


    public function index()
    {
        $user = Auth::user();
        return view('admin.index', compact('user'));  // <-- usar notación con punto
    }
    // Constructor para asegurar que solo los admin puedan acceder
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role !== 'admin') {
                abort(403, 'No autorizado');
            }
            return $next($request);
        });
    }
    // Vista de dashboard de administrador
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Listar todos los usuarios
    public function listUsers()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Crear nuevo usuario
    public function createUser(Request $request)
    {
        if ($request->ajax()) {
            return view('admin.users.create');
        }
        return view('admin.users.index');
    }

    // Guardar nuevo usuario
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,seller,buyer', // Aquí validamos el rol
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente');
    }

    // Ver un usuario específico
    public function showUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function deleteConfirm(Request $request, User $user)
    {
        if ($request->ajax()) {
            return view('admin.users.delete-confirm', compact('user'));
        }
        return view('admin.users.index');
    }

    // Actualizar usuario
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,seller,buyer',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente');
    }

    // Eliminar usuario
    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente');
    }

    public function editModal(User $user)
    {
        return view('admin.users.partials.edit-modal', compact('user'));
    }
}
