<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        // Aquí mostramos los productos en el carrito
        $cart = session()->get('cart', []);
        return view('buyer.cart.index', compact('cart'));
    }

    public function add(Product $product)
    {
        // Agregar el producto al carrito
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Producto añadido al carrito');
    }
}
