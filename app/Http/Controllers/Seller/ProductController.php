<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class ProductController extends Controller
{

    public function index()
    {
        if (Auth::user()->role === 'admin' || Auth::user()->role === 'seller') {
            $products = Product::all();
        } else {
            $products = Product::where('seller_id', Auth::id())->get();
        }
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
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Crear el producto y asociarlo con el vendedor
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->seller_id = Auth::id(); // Usar el ID del vendedor autenticado
        $product->category_id = $request->category_id;
        $product->save();

        return redirect()->route('buyer.products.by_user', ['userId' => Auth::id()]);
    }

    public function edit(Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json($product);
    }

    // Actualizar un producto
    public function update(Request $request, Product $product)
    {
        //$this->authorize('update', $product); // opcional según roles

        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Producto actualizado correctamente.');
    }


    // Eliminar un producto
    public function destroy(Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            return redirect()->route('seller.products.index');
        }

        $product->delete();
        return redirect()->route('seller.products.index');
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }
}
