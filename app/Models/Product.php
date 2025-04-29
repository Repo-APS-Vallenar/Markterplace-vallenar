<?php

// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Product extends Model
{
    use HasFactory;

    // Campos que se pueden asignar de forma masiva
    protected $fillable = [
        'name',
        'price',
        'description',
        'user_id', // Relacionado con el vendedor (usuario)
    ];

    // Relación inversa con el modelo User (vendedor)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el modelo Order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
