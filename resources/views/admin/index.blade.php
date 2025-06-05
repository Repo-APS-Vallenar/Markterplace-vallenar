@extends('layouts.admin')

@section('admin-content')
<div class="bg-white shadow-xl rounded-2xl p-10 w-full max-w-2xl">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-4 text-center">
        Panel de Administrador
    </h1>
    <div class="text-center mb-6">
        <p class="text-xl font-medium text-gray-600">
            Bienvenido, {{ auth()->user()->name }}. Eres un administrador.
        </p>
    </div>
</div>
@endsection