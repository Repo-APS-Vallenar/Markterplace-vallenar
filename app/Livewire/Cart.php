<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order; // Asegúrate de importar el modelo Order
use App\Models\OrderItem; // Asegúrate de importar el modelo OrderItem
use Illuminate\Support\Facades\DB; // Para transacciones
use Illuminate\Support\Facades\Auth; // Para obtener el usuario autenticado
use Illuminate\Support\Facades\Session; // Para manejar sesiones
use Illuminate\Support\Facades\Log; // Para loguear errores

class Cart extends Component
{
    public $cart = [];
    public $payment_method = 'cash';
    public $notes = '';
    public $showCheckoutModal = false;

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function increase($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            session()->put('cart', $this->cart);
            $this->dispatch('cart-updated');
        }
    }

    public function decrease($productId)
    {
        if (isset($this->cart[$productId]) && $this->cart[$productId]['quantity'] > 1) {
            $this->cart[$productId]['quantity']--;
            session()->put('cart', $this->cart);
            $this->dispatch('cart-updated');
        }
    }

    public function remove($productId)
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            session()->put('cart', $this->cart);
            $this->dispatch('cart-updated');
        }
    }

    public function checkout()
    {
        // 1. Validar que el carrito no esté vacío
        if (empty($this->cart)) {
            session()->flash('error', 'Tu carrito está vacío. Agrega productos antes de confirmar el pedido.');
            $this->closeCheckoutModal();
            return;
        }

        // Validar los datos del formulario del modal
        $this->validate([
            'payment_method' => 'required|string|in:cash,transfer', // Asegura que solo sean estos valores
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        if (!$user) {
            // Manejar caso donde no hay usuario autenticado (redirigir a login, etc.)
            session()->flash('error', 'Debes iniciar sesión para realizar un pedido.');
            return redirect()->route('login'); // O la ruta de tu login
        }

        // Usaremos una transacción para asegurar que todo se guarda o nada
        DB::beginTransaction();

        try {
            // 2. Calcular el total general del pedido
            $totalOrderAmount = 0;
            foreach ($this->cart as $item) {
                // Asegúrate de que $item['price'] y $item['quantity'] existan y sean numéricos.
                // Podrías necesitar cargar el precio actual del producto desde la BD aquí
                // para evitar manipulaciones en el frontend, pero por ahora usamos lo del carrito.
                $totalOrderAmount += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
            }

            // 3. Crear el PEDIDO principal en la tabla `orders`
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending', // Estado inicial del pedido
                'payment_method' => $this->payment_method,
                'total' => $totalOrderAmount, // El total de todo el carrito
                'notes' => $this->notes,
                // 'created_at' y 'updated_at' se manejan automáticamente por Laravel
            ]);

            // 4. Recorrer los productos del carrito para crear los ITEMS del PEDIDO
            foreach ($this->cart as $productId => $item) {
                OrderItem::create([
                    'order_id' => $order->id, // <-- Asegúrate que $order->id esté disponible aquí
                    'product_id' => $productId,
                    'seller_id' => $item['seller_id'], // <-- Asegúrate que seller_id esté disponible aquí
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => ($item['price'] ?? 0) * ($item['quantity'] ?? 0),
                ]);
            }

            DB::commit(); // Si todo sale bien, guarda los cambios

            // 5. Limpiar el carrito después de un pedido exitoso
            session()->forget('cart');
            $this->cart = [];
            $this->showCheckoutModal = false;

            // 6. Mensaje de éxito y redirección
            session()->flash('success', '¡Pedido realizado con éxito! Pronto te contactará el vendedor.');
            return redirect()->route('buyer.orders.index'); // O la ruta que sea más adecuada
            // Aquí puedes disparar eventos para notificaciones por correo/WhatsApp, etc.

        } catch (\Exception $e) {
            DB::rollBack(); // Si algo falla, revierte todos los cambios
            session()->flash('error', 'Ocurrió un error al procesar tu pedido: ' . $e->getMessage());
            // Logear el error para depuración
            Log::error('Error al procesar pedido: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->closeCheckoutModal(); // Cierra el modal en caso de error
            // Puedes redirigir a alguna página de error o simplemente dejarlo en la misma página
        }
    }

    public function openCheckoutModal()
    {
        $this->showCheckoutModal = true;
    }

    public function closeCheckoutModal()
    {
        $this->showCheckoutModal = false;
    }

    public function render()
    {
        return view('livewire.cart', [
            'cart' => $this->cart
        ]);
    }
}
