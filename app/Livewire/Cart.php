<?php

namespace App\Livewire;

use Livewire\Component;

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
        $user = auth()->user();
        foreach ($this->cart as $productId => $item) {
            $sellerId = $item['seller_id'] ?? null;
            \App\Models\Order::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'seller_id' => $sellerId,
                'status' => 'pending',
                'payment_method' => $this->payment_method,
                'total' => $item['price'] * $item['quantity'],
                'notes' => $this->notes,
                'quantity' => $item['quantity'],
            ]);
        }
        // Limpia el carrito
        session()->forget('cart');
        $this->cart = [];
        $this->showCheckoutModal = false;
        session()->flash('success', '¡Pedido realizado con éxito! Pronto te contactará el vendedor.');
        return redirect()->route('buyer.orders.index');
        // Aquí puedes disparar eventos para notificaciones por correo/WhatsApp
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
