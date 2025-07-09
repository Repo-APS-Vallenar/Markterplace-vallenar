<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; // Si usas Gates
use App\Models\User;
use App\Models\OrderItem; // Importa OrderItem

class OrderController extends Controller
{
    // Constructor con middleware de autenticación y rol (si aplica globalmente)
    public function __construct()
    {
        $this->middleware('auth');
        // Si usas middleware de rol, podrías tenerlo aquí o en las rutas
        // $this->middleware('role:admin|seller|buyer');
    }

    /**
     * Muestra el listado de pedidos según el rol del usuario.
     * Este método es para la vista general de "Mis Pedidos" (buyer) o "Todos los Pedidos" (admin).
     * Los vendedores tendrán su propia vista/método para "Pedidos Recibidos".
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin ve todos los pedidos, cargando sus items y los productos de esos items, y el comprador.
            $orders = Order::with(['user', 'orderItems.product'])->latest()->get();
        } elseif ($user->isBuyer()) {
            // Comprador ve solo sus pedidos, cargando sus items y los productos de esos items.
            $orders = Order::where('user_id', $user->id)->with(['user', 'orderItems.product'])->latest()->get();
        } else {
            // Si un seller intenta acceder a esta ruta general, redirigir o mostrar un mensaje.
            // O simplemente no mostrar nada (como el whereRaw('0=1')).
            $orders = collect(); // Colección vacía si no es admin ni buyer
        }

        // Si necesitas $ordersForJson para alguna API o JS específico, ajusta la lógica aquí
        // para que itere sobre orderItems si es necesario.
        $ordersForJson = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'user_name' => optional($order->user)->name ?? '',
                'total' => $order->total,
                'status' => $order->status,
                'delivery_method' => $order->delivery_method,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'product_name' => optional($item->product)->name ?? '',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'subtotal' => $item->subtotal,
                        'seller_name' => optional($item->seller)->name ?? '',
                    ];
                })
            ];
        });

        return view('orders.index', compact('orders', 'ordersForJson'));
    }

    /**
     * Scope para filtrar pedidos (si lo usas en otros lugares).
     * Asegúrate de que las relaciones sean correctas aquí también.
     */
    public function scopeVisibleTo($query, User $user)
    {
        if ($user->role === 'admin') {
            return $query;
        }

        if ($user->role === 'seller') {
            // Un seller ve pedidos que contienen sus order items
            return $query->whereHas('orderItems', fn($q) => $q->where('seller_id', $user->id));
        }

        if ($user->role === 'buyer') {
            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('0 = 1'); // No retorna nada para otros
    }


    /**
     * Crea un pedido (desde el carrito, no desde un solo producto).
     * Este método debería ser llamado desde el proceso de checkout del carrito.
     */
    public function store(Request $request)
    {
        // Asumo que la validación y la autorización se manejan en el CartController@checkout
        // o en un servicio de checkout. Si este store es para un solo producto, necesitas adaptarlo.

        // Ejemplo de cómo sería si se procesa un carrito:
        // $this->authorize('create', Order::class); // Si tienes una política para Order

        // Validar datos de envío/pago
        $request->validate([
            'payment_method' => 'required|in:efectivo,tarjeta', // Ajusta según tus métodos
            'notes' => 'nullable|string|max:500',
            // ... otros campos de envío/pago
        ]);

        $user = Auth::user();
        $cartItems = $user->cartItems()->with('product.user')->get(); // Cargar productos y sus vendedores

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Tu carrito está vacío.');
        }

        // Calcular el total del pedido
        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        // Crear el pedido principal
        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        // Crear los OrderItems para cada producto en el carrito
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'seller_id' => $cartItem->product->user_id, // ¡Importante! El user_id del producto es el seller_id
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price,
                'subtotal' => $cartItem->quantity * $cartItem->product->price,
            ]);
            // Opcional: Eliminar el ítem del carrito después de agregarlo al pedido
            // $cartItem->delete();
        }

        // Redirigir al comprador a su lista de pedidos o una página de éxito
        return redirect()->route('buyer.orders.index')->with('success', '¡Pedido realizado correctamente!');
    }

    /**
     * Muestra los detalles de un pedido específico.
     */
    public function show(Order $order)
    {
        // $this->authorize('view', $order); // Si usas políticas

        // Cargar orderItems y sus productos, y el usuario (comprador)
        $order->load(['orderItems.product.user', 'user']);

        return view('orders.show', compact('order'));
    }

    /**
     * Muestra el formulario de edición de un pedido (para admin/seller).
     */
    public function edit(Order $order)
    {
        // $this->authorize('update', $order); // Si usas políticas
        $order->load(['orderItems.product.user', 'user']); // Cargar relaciones para edición
        return view('orders.edit', compact('order'));
    }

    /**
     * Actualiza un pedido.
     */
    public function update(Request $request, Order $order)
    {
        // $this->authorize('update', $order); // Si usas políticas

        $data = $request->validate([
            'status'          => 'required|in:pending,processing,completed,cancelled',
            // 'delivery_method' => 'required|in:domicilio,acordar', // Si se puede cambiar en la edición
            // 'quantity'        => 'required|integer|min:1', // Si se puede cambiar la cantidad del pedido completo (raro)
        ]);

        $order->update($data);

        return redirect()->route('orders.index')->with('success', 'Pedido actualizado correctamente.');
    }

    /**
     * Elimina un pedido.
     */
    public function destroy(Order $order)
    {
        // $this->authorize('delete', $order); // Si usas políticas
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Pedido eliminado correctamente.');
    }

    /**
     * Cancela un pedido (normalmente para compradores).
     */
    public function cancel(Order $order)
    {
        if (Auth::user()->id !== $order->user_id && Auth::user()->role !== 'admin') {
            abort(403); // No autorizado si no es el comprador o admin
        }
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Solo puedes cancelar pedidos pendientes.');
        }
        $order->status = 'cancelled';
        $order->save();
        return redirect()->route('buyer.orders.index')->with('success', '¡Pedido cancelado correctamente!');
    }

    /**
     * Muestra los pedidos recibidos por el vendedor.
     * ESTE ES EL MÉTODO CLAVE PARA LA VISTA DEL VENDEDOR.
     */
    public function receivedOrders()
    {
        $sellerId = Auth::id();

        // Obtener los order_items que le pertenecen a este vendedor
        // Y cargar eager load las relaciones Order y Product para mostrar la información en la vista
        $sellerOrderItems = OrderItem::where('seller_id', $sellerId)
                                     ->with(['order.user', 'product']) // Carga Order, el User del Order y el Product del OrderItem
                                     ->orderBy('created_at', 'desc')
                                     ->get();

        // Asegúrate de que la vista 'seller.orders.index' reciba esta variable
        return view('seller.orders.index', compact('sellerOrderItems'));
    }

    // Si tienes un método para marcar OrderItem como procesado
    public function markOrderItemProcessed(OrderItem $orderItem)
    {
        if ($orderItem->seller_id !== Auth::id()) {
            abort(403, 'No autorizado para procesar este ítem de pedido.');
        }

        // Aquí podrías cambiar el estado del OrderItem
        // Considera si también quieres cambiar el estado del Order principal si todos sus OrderItems se procesan.
        // Por ejemplo, si todos los order items de un pedido están procesados, el pedido principal pasa a "completed".
        $orderItem->status = 'processed'; // Asumiendo que OrderItem tiene una columna 'status'
        $orderItem->save();

        // Lógica para actualizar el estado del pedido principal si todos los items del vendedor están procesados
        $order = $orderItem->order;
        $allSellerItemsProcessed = $order->orderItems()->where('seller_id', Auth::id())->where('status', '!=', 'processed')->doesntExist();

        if ($allSellerItemsProcessed) {
            // Esto es solo si quieres que el pedido principal cambie de estado cuando TODOS los items
            // de ESE vendedor en ESE pedido están procesados.
            // Si el pedido tiene items de otros vendedores, el estado del pedido principal es más complejo.
            // Podrías considerar un estado intermedio para el pedido principal, como "partially_processed".
        }

        return redirect()->back()->with('success', 'Ítem de pedido marcado como procesado.');
    }
}