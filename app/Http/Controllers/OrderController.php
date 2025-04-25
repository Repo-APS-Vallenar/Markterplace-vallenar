<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Mostrar pedidos según el rol
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $orders = Order::latest()->get(); // Todos los pedidos
        } elseif ($user->hasRole('seller')) {
            // Pedidos que contienen productos del vendedor
            $orders = Order::whereHas('products', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->latest()->get();
        } elseif ($user->hasRole('buyer')) {
            // Pedidos del comprador
            $orders = Order::where('user_id', $user->id)->latest()->get();
        } else {
            abort(403);
        }

        return view('orders.index', compact('orders'));
    }

    // Mostrar formulario de creación de pedidos para buyers
    public function create()
    {
        if (!Auth::user()->hasRole('buyer')) {
            abort(403);
        }

        $products = Product::all();
        return view('orders.create', compact('products'));
    }

    // Almacenar pedido
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('buyer')) {
            abort(403);
        }

        $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
        ]);

        $order = Order::create([
            'user_id' => Auth::id(),
        ]);

        $order->products()->attach($request->products);

        return redirect()->route('orders.index')->with('success', 'Pedido creado correctamente.');
    }

    // Mostrar un pedido
    public function show(Order $order)
    {
        $user = Auth::user();

        if (
            $user->hasRole('admin') ||
            ($user->hasRole('buyer') && $order->user_id === $user->id) ||
            ($user->hasRole('seller') && $order->products()->where('user_id', $user->id)->exists())
        ) {
            return view('orders.show', compact('order'));
        }

        abort(403);
    }
}
