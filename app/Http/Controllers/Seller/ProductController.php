<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role !== 'seller' && Auth::user()->role !== 'admin') {
                abort(403, 'No autorizado');
            }
            return $next($request);
        });
    }

    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $products = Product::all();
        } else {
            $products = Product::where('user_id', Auth::id())->get();
        }
        return view('seller.products.index', compact('products'));
    }

    // Mostrar el formulario para crear un nuevo producto
    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('seller.products.modals.create-modal');
        }
        return redirect()->route('seller.products.index');
    }

    // Guardar un nuevo producto
    public function store(Request $request)
    {

        // dd($request->all()); // ¡Elimina o comenta esta línea!

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10048',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->user_id = Auth::id(); // Asegúrate que Auth::id() devuelve el ID del seller logueado
        $product->category_id = $request->category_id;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save(); // Esta línea es clave para guardar el producto en la BD

        // La redirección está correcta ahora:
        return redirect()->route('seller.products.index')->with('success', 'Producto creado correctamente.');
    }
    public function edit(Product $product, Request $request)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        if ($request->ajax()) {
            return view('seller.products.modals.edit-modal', compact('product'));
        }

        return redirect()->route('seller.products.index');
    }

    // Actualizar un producto
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category_id' => $request->category_id,
        ];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

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

    public function show(Product $product, Request $request)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        if ($request->ajax()) {
            return view('seller.products.modals.show-modal', compact('product'));
        }

        return redirect()->route('seller.products.index');
    }

    public function deleteModal(Product $product, Request $request)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }
        if ($request->ajax()) {
            return view('seller.products.modals.delete-modal', compact('product'));
        }
        return redirect()->route('seller.products.index');
    }
}
