<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',    // <-- Asegúrate de que este esté
        'product_id',  // <-- Asegúrate de que este esté
        'seller_id',   // <-- Asegúrate de que este esté
        'quantity',    // <-- Asegúrate de que este esté
        'price',       // <-- Asegúrate de que este esté
        'subtotal',    // <-- Asegúrate de que este esté
    ];

    // Relación con el pedido
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relación con el producto
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relación con el vendedor
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
