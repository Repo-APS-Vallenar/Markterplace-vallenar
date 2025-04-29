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
        return $user->hasRole('admin') ||
               ($user->hasRole('seller') && $order->product->seller_id == $user->id) ||
               ($user->hasRole('buyer') && $order->user_id == $user->id);
    }

    /**
     * Determinar si el usuario puede crear pedidos.
     */
    public function create(User $user)
    {
        return $user->hasRole('buyer');
    }

    /**
     * Determinar si el usuario puede actualizar un pedido.
     */
    public function update(User $user, Order $order)
    {
        return $user->hasRole('admin') ||
               ($user->hasRole('seller') && $order->product->seller_id == $user->id);
    }

    /**
     * Determinar si el usuario puede eliminar un pedido.
     */
    public function delete(User $user, Order $order)
    {
        return $user->hasRole('admin') ||
               ($user->hasRole('buyer') && $order->user_id == $user->id);
    }
}

