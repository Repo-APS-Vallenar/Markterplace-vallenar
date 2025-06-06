<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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

    public function checkout(Request $request)
    {
        // Aquí va la lógica para crear los pedidos a partir del carrito y limpiar el carrito
        // Por ahora, solo redirige con un mensaje de éxito
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', '¡Compra realizada con éxito!');
    }

    public function update(Request $request, $productId)
    {
        $cart = session()->get('cart', []);
        if (!isset($cart[$productId])) {
            return view('buyer.cart.partials.table', ['cart' => $cart])->render();
        }

        if ($request->input('action') === 'increase') {
            $cart[$productId]['quantity'] += 1;
        } elseif ($request->input('action') === 'decrease' && $cart[$productId]['quantity'] > 1) {
            $cart[$productId]['quantity'] -= 1;
        }
        session()->put('cart', $cart);
        \Session::save();
        // Vuelve a obtener el carrito actualizado de la sesión
        $cart = session()->get('cart', []);
        return view('buyer.cart.partials.table', ['cart' => $cart])->render();
    }

    public function remove($productId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);
        \Session::save();
        // Vuelve a obtener el carrito actualizado de la sesión
        $cart = session()->get('cart', []);
        return view('buyer.cart.partials.table', ['cart' => $cart])->render();
    }

    private function cartPartial()
    {
        $cart = session()->get('cart', []);
        return view('buyer.cart.partials.table', compact('cart'))->render();
    }
}
