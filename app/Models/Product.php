<?php

// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'category_id',
        'user_id', // El vendedor que creó este producto
    ];

    public function user() // Relación con el User que es el vendedor
    {
        return $this->belongsTo(User::class, 'user_id'); // Explícitamente user_id para claridad
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems() // Los ítems de pedidos que contienen este producto
    {
        return $this->hasMany(OrderItem::class);
    }
}