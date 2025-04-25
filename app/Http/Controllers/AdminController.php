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

        return view("/admin/index", compact("user"));
    }
    // Constructor para asegurar que solo los admin puedan acceder
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Verificar si el usuario tiene el rol 'admin'
            if (Auth::check() && !Auth::user()->hasRole('admin')) {
                // Si no tiene el rol, regresar un error 403
                return abort(403, 'Unauthorized');
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
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    // Crear nuevo usuario
    public function createUser()
    {
        return view('admin.users.create');
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
    public function showUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }
}
