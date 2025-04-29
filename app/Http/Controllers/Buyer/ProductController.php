<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::all(); // o solo activos/visibles
        return view('buyer.products.index', compact('products'));
    }

    public function showProducts($userId)
    {
        $user = User::findOrFail($userId); // Usamos findOrFail para garantizar que siempre se obtenga un usuario válido
        $products = $user->products; // Esto debería funcionar si la relación está configurada correctamente

        return view('buyer.products.by_user', compact('products', 'user'));
    }
}
