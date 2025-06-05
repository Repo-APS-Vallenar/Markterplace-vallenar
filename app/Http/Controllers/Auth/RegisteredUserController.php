<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Obtener roles 'buyer' y 'seller'
        $roles = Role::whereIn('name', ['buyer', 'seller'])->get();
        return view('auth.register', compact('roles'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:buyer,seller,admin', // Validación para el rol
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        // Asignar el rol al usuario
        Log::debug('Role asignado:', [$request->role]);
        $user->role === ($request->role);

        // Disparar el evento de registro
        event(new Registered($user));

        // Loguear al usuario
        Auth::login($user);

        // Redirigir al dashboard o página correspondiente
        return redirect(route('dashboard', absolute: false));
    }
}
