<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determinar si el usuario puede ver un pedido.
     */
    public function view(User $user, Order $order)
    {
        return $user->role === 'admin' ||
               ($user->role === 'seller' && $order->product->seller_id == $user->id) ||
               ($user->role === 'buyer' && $order->user_id == $user->id);
    }

    /**
     * Determinar si el usuario puede crear pedidos.
     */
    public function create(User $user)
    {
        return $user->role === 'buyer';
    }

    /**
     * Determinar si el usuario puede actualizar un pedido.
     */
    public function update(User $user, Order $order)
    {
        return $user->role==='admin' ||
               ($user->role==='seller' && $order->product->seller_id == $user->id);
    }

    /**
     * Determinar si el usuario puede eliminar un pedido.
     */
    public function delete(User $user, Order $order)
    {
        return $user->role==='admin' ||
               ($user->role==='buyer' && $order->user_id == $user->id);
    }
}

