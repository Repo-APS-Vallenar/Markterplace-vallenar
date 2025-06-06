<?php

// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Campos que se pueden asignar de forma masiva
    protected $fillable = [
        'user_id', // Comprador
        'product_id', // Producto
        'seller_id',
        'status', // Estado del pedido (pendiente, procesado, completado, etc.)
        'payment_method',
        'total',
        'notes',
        'quantity',
    ];

    // Relación con el modelo User (comprador)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el modelo Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
