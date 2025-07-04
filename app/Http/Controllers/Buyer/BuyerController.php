<?php

namespace App\Http\Controllers\Buyer;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BuyerController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role !== 'buyer' && auth()->user()->role !== 'admin') {
                abort(403, 'No autorizado');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Lógica del panel de comprador
        return view('buyer.index');
    }
    // Ver los detalles de un producto
    public function show(Product $product)
    {
        return view('buyer.products.show', compact('product'));
    }

    // Crear un pedido
    public function createOrder(Request $request, Product $product)
    {
        // Validación del pedido
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Crear el pedido para el comprador autenticado
        $order = new Order();
        $order->product_id = $product->id;
        $order->user_id = Auth::id();
        $order->quantity = $request->input('quantity');
        $order->status = 'pendiente'; // El pedido comienza como pendiente
        $order->save();

        return redirect()->route('buyer.orders.index')->with('success', 'Pedido creado exitosamente.');
    }

    // Ver los pedidos del comprador
    public function orders()
    {
        if (Auth::user()->role === 'admin') {
            // El admin puede ver todos los pedidos
            $orders = Order::all();
        } else {
        $orders = Order::where('user_id', Auth::id())->get();
        }
        return view('buyer.orders.index', compact('orders'));
    }

    public function sellersWithProducts()
    {
        $sellers = User::where('role', 'seller')
            ->with(['products' => function ($query) {
                $query->where('is_active', true);
            }])->get();

        return view('buyer.sellers', compact('sellers'));
    }

    // Ver detalles de un pedido específico
    public function showOrder(Order $order)
    {
        // Solo permitir que el comprador vea su propio pedido, pero el admin puede ver cualquiera
        if (Auth::user()->role !== 'admin' && $order->user_id != Auth::id()) {
            return redirect()->route('buyer.orders.index')->with('error', 'Acción no permitida.');
        }

        return view('buyer.orders.show', compact('order'));
    }
}
