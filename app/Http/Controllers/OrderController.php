<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class OrderController extends Controller
{
    // 1. Listado de pedidos (filtrado según rol)
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            // Admin ve todos los pedidos
            $orders = Order::with(['product', 'user'])->latest()->get();
        } elseif ($user->isSeller()) {
            // Vendedor ve solo pedidos de sus productos
            $orders = Order::whereHas('product', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->with(['product', 'user'])->latest()->get();
        } else {
            // Comprador ve solo sus pedidos
            $orders = Order::where('user_id', $user->id)->with(['product', 'user'])->latest()->get();
        }

        $ordersForJson = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'product' => optional($order->product)->name ?? '',
                'user' => optional($order->user)->name ?? '',
                'quantity' => $order->quantity ?? '',
                'delivery_method' => $order->delivery_method ?? '',
                'status' => $order->status ?? '',
            ];
        });

        return view('orders.index', ['orders' => $orders, 'ordersForJson' => $ordersForJson]);
    }

    // En Order.php
    public function scopeVisibleTo($query, User $user)
    {
        if ($user->role === 'admin') {
            return $query;
        }

        if ($user->role === 'seller') {
            return $query->whereHas('product', fn($q) => $q->where('seller_id', $user->id));
        }

        if ($user->role === 'buyer') {
            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('0 = 1'); // No retorna nada para otros
    }


    // 2. Formulario para crear pedido (solo buyers)
    public function create($productId)
    {
        $product = Product::findOrFail($productId);
        dd($product);
        $this->authorize('create', Order::class);

        return view('orders.create', compact('product'));
    }


    // 3. Almacenar nuevo pedido
    public function store(Request $request)
    {
        $this->authorize('create', Order::class);

        $data = $request->validate([
            'product_id'      => 'required|exists:products,id',
            'quantity'        => 'required|integer|min:1',
            'delivery_method' => 'required|in:domicilio,acordar',
        ]);

        Order::create([
            'user_id'         => Auth::id(),
            'product_id'      => $data['product_id'],
            'quantity'        => $data['quantity'],
            'delivery_method' => $data['delivery_method'],
            'status'          => 'pending',
        ]);

        return redirect()
            ->route('buyer.index')
            ->with('success', 'Pedido realizado correctamente.');
    }

    // 4. Ver detalle de un pedido
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['product', 'user']);
        return view('orders.show', compact('order'));
    }

    // 5. Formulario de edición (seller/admin)
    public function edit(Order $order)
    {
        $this->authorize('update', $order);
        return view('orders.edit', compact('order'));
    }

    // 6. Actualizar pedido
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $data = $request->validate([
            'status'          => 'required|in:pending,processing,completed,cancelled',
            'delivery_method' => 'required|in:domicilio,acordar',
            'quantity'        => 'required|integer|min:1',
        ]);

        $order->update($data);

        return redirect()->route('orders.index')
            ->with('success', 'Pedido actualizado correctamente.');
    }

    // 7. Eliminar pedido
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Pedido eliminado correctamente.');
    }

    public function cancel(Order $order)
    {
        if (auth()->user()->id !== $order->user_id && auth()->user()->role !== 'admin') {
            abort(403);
        }
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Solo puedes cancelar pedidos pendientes.');
        }
        $order->status = 'cancelled';
        $order->save();
        return redirect()->route('buyer.orders.index')->with('success', '¡Pedido cancelado correctamente!');
    }
}
