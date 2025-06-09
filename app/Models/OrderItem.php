<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'quantity',
        'price',
        'subtotal'
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
