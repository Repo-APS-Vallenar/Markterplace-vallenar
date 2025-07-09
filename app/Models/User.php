<?php

// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isBuyer()
    {
        return $this->role === 'buyer';
    }

    public function isSeller()
    {
        return $this->role === 'seller';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function products() // Productos creados por este usuario (vendedor)
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function orders() // Pedidos realizados por este usuario (comprador)
    {
        return $this->hasMany(Order::class, 'user_id'); // Explícitamente user_id para claridad
    }

    // Un usuario (vendedor) recibe order items.
    public function receivedOrderItems()
    {
        return $this->hasMany(OrderItem::class, 'seller_id');
    }
}