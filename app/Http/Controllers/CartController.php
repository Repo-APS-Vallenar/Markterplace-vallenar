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
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'seller_id' => $product->user_id // <-- ¡ESTA LÍNEA DEBE ESTAR AHÍ Y CORRECTA!
            ];
        }

        session()->put('cart', $cart);
        // Opcional: para depurar, puedes descomentar la siguiente línea
        // dd(session()->get('cart'));
        return redirect()->route('cart.index')->with('success', 'Producto añadido al carrito');
    }

    // El método checkout en este controlador ya no será necesario si lo manejas con Livewire
    // Si tienes una ruta que apunta a este checkout, deberías redirigir a la vista Livewire
    // o eliminar esta ruta si el checkout se hace exclusivamente vía Livewire.
    public function checkout(Request $request)
    {
        // Esta función ya no debería manejar la lógica de creación de pedidos
        // si estás usando el componente Livewire\Cart para eso.
        // Podrías simplemente redirigir a la página del carrito si es necesario,
        // o a la página de pedidos del comprador.
        // session()->forget('cart'); // Esto ya lo hace el componente Livewire
        // return redirect()->route('cart.index')->with('success', '¡Compra realizada con éxito!');
        return redirect()->route('buyer.orders.index')->with('success', '¡Compra realizada con éxito!');
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
        Session::save();
        // Vuelve a obtener el carrito actualizado de la sesión
        $cart = session()->get('cart', []);
        return view('buyer.cart.partials.table', ['cart' => $cart])->render();
    }

    public function remove($productId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);
        Session::save();
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
