<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class SellerController extends Controller
{
    public function __construct()
    {
        // Verifica que el usuario tenga el rol de 'seller' antes de continuar
        $this->middleware(function ($request, $next) {
            if (Auth::check() && !Auth::user()->hasRole('seller')) {
                return abort(403, 'Unauthorized');
            }

            return $next($request);
        });
    }

    // Mostrar todos los productos del vendedor
    public function index()
    {
        // Lógica del panel de vendedor
        return view('seller.index');
    }

    // Mostrar el formulario para crear un nuevo producto
    public function create()
    {
        return view('seller.products.create');
    }

    // Guardar un nuevo producto creado por el vendedor
    public function store(Request $request)
    {
        // Validación de los datos del producto
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        // Crear un nuevo producto
        $product = new Product();
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->description = $request->input('description');
        $product->user_id = Auth::id(); // Asignar al vendedor autenticado
        $product->save();

        return redirect()->route('seller.products.index')->with('success', 'Producto creado exitosamente.');
    }

    // Mostrar el formulario para editar un producto existente
    public function edit(Product $product)
    {
        // Verificar si el vendedor es el dueño del producto
        if ($product->user_id != Auth::id()) {
            return redirect()->route('seller.products.index')->with('error', 'No tienes permiso para editar este producto.');
        }

        return view('seller.products.edit', compact('product'));
    }

    // Actualizar los detalles de un producto
    public function update(Request $request, Product $product)
    {
        // Validación de los datos del producto
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        // Verificar si el vendedor es el dueño del producto
        if ($product->user_id != Auth::id()) {
            return redirect()->route('seller.products.index')->with('error', 'No tienes permiso para actualizar este producto.');
        }

        // Actualizar el producto
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->description = $request->input('description');
        $product->save();

        return redirect()->route('seller.products.index')->with('success', 'Producto actualizado exitosamente.');
    }

    // Eliminar un producto
    public function destroy(Product $product)
    {
        // Verificar si el vendedor es el dueño del producto
        if ($product->user_id != Auth::id()) {
            return redirect()->route('seller.products.index')->with('error', 'No tienes permiso para eliminar este producto.');
        }

        // Eliminar el producto
        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Producto eliminado exitosamente.');
    }
}
