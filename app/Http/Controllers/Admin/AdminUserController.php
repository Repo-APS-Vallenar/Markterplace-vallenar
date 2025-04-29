<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::role('seller')->get();
        return view('admin.users.index', compact('users'));
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
        $user = User::create($data);
        $user->assignRole('seller');

        return redirect()->route('admin.users.index')->with('success', 'Vendedor creado.');
    }

    public function edit(User $user)
    {
        abort_unless($user->hasRole('seller'), 404);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->hasRole('seller'), 404);

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
        abort_unless($user->hasRole('seller'), 404);
        $user->delete();
        return back()->with('success', 'Vendedor eliminado.');
    }
}
