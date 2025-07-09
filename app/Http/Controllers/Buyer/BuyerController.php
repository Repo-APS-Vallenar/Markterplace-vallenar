<?php

namespace App\Http\Controllers\Buyer;

use App\Models\Product; // Posiblemente no necesites este aquí
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Posiblemente no necesites este aquí si no lo usas más allá de sellersWithProducts

class BuyerController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role !== 'buyer' && Auth::user()->role !== 'admin') {
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

    // Ver los detalles de un producto (mantener si se usa en otro lugar)
    public function show(Product $product)
    {
        return view('buyer.products.show', compact('product'));
    }

    // Ver los pedidos del comprador
    public function orders()
    {
        if (Auth::user()->role === 'admin') {
            // El admin puede ver todos los pedidos, cargando sus items y productos
            $orders = Order::with(['orderItems.product'])->orderByDesc('created_at')->get();
        } else {
            // Un comprador solo ve sus propios pedidos, cargando sus items y productos
            $orders = Order::where('user_id', Auth::id())
                            ->with(['orderItems.product']) // <-- ¡¡ESTO ES CRUCIAL!!
                            ->orderByDesc('created_at')
                            ->get();
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
        // Cargar las relaciones para este pedido específico si se accede directamente
        // Aunque la vista index ahora maneja el detalle en modal,
        // esto sería para una vista individual si existiera.
        $order->load('orderItems.product'); // Cargar la relación para el pedido individual

        if (Auth::user()->role !== 'admin' && $order->user_id != Auth::id()) {
            return redirect()->route('buyer.orders.index')->with('error', 'Acción no permitida.');
        }

        return view('buyer.orders.show', compact('order'));
    }
}