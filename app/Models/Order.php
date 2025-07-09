<?php

// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Comprador
        'status',
        'payment_method',
        'total',
        'notes',
    ];

    public function user() // Comprador del pedido
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems() // Los productos específicos dentro de este pedido
    {
        return $this->hasMany(OrderItem::class);
    }
}