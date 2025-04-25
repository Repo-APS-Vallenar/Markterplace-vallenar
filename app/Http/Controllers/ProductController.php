<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // Mostrar todos los productos del vendedor autenticado
    public function index()
    {

        $products = Product::all();
        return view('seller.products.index', compact('products'));
    }

    // Mostrar el formulario para crear un nuevo producto
    public function create()
    {
        return view('seller.products.modals.create-modal');
    }

    // Guardar un nuevo producto
    public function store(Request $request)
    {
        // Verificar si el usuario está autenticado
        $user = Auth::user();
        if (!$user) {
            // El usuario no está autenticado, redirigir o mostrar un mensaje de error
            return redirect()->route('login')->with('error', 'Necesitas estar logueado.');
        }

        // El usuario está autenticado, ahora puedes proceder
        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->user_id = Auth::user()->id; // Accede al id del usuario autenticado
        $product->save();

        return redirect()->route('seller.products.index')->with('success', 'Producto creado exitosamente.');
    }



    // Mostrar un producto específico
    public function show(Product $product)
    {
        $products = Product::all();
        return view('seller.products.modals.show-modal', compact('product'));
    }

    // Mostrar el formulario para editar un producto
    public function edit(Product $product)
    {
        $products = Product::all();
        if ($product->user_id !== Auth::id()) {
            return redirect()->route('seller.products.index');
        }

        return view('seller.products.modals.edit-modal', compact('product'));
    }

    // Actualizar un producto
    public function update(Request $request, Product $product)
    {
        //$this->authorize('update', $product); // opcional según roles

        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Producto actualizado correctamente.');
    }


    // Eliminar un producto
    public function destroy(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            return redirect()->route('seller.products.index');
        }

        $product->delete();
        return redirect()->route('seller.products.index');
    }
}
