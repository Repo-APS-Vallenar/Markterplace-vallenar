<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category; // Importar el modelo Category
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Para manejar la subida y eliminación de imágenes

class SellerController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Verifica si el usuario está autenticado y si su rol es 'seller' o 'admin'
            if (Auth::check() && (Auth::user()->role === 'seller' || Auth::user()->role === 'admin')) {
                return $next($request);
            }
            abort(403, 'No autorizado para acceder a esta sección.');
        });
    }

    /**
     * Muestra la vista del panel de vendedor (dashboard general).
     */
    public function index()
    {
        // Puedes agregar lógica aquí para el panel de vendedor,
        // por ejemplo, contar productos, pedidos pendientes, etc.
        return view('seller.index');
    }

    /**
     * Muestra todos los productos que pertenecen al vendedor autenticado.
     * Esta es la vista donde estará el modal de creación de productos.
     */
    public function productsIndex()
    {
        $products = Product::where('user_id', Auth::id())->get();
        $categories = Category::all(); // Obtener todas las categorías para rellenar el select del modal
        return view('seller.products.index', compact('products', 'categories'));
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     * Este método podría no ser necesario si usas un modal en productsIndex.
     */
    public function create()
    {
        $categories = Category::all(); // Pasar las categorías al formulario
        return view('seller.products.create', compact('categories'));
    }

    /**
     * Guarda un nuevo producto creado por el vendedor.
     */
    public function store(Request $request)
    {
        // Validación de los datos del producto
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0', // Precio no negativo
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id', // Debe existir en la tabla categories
            'image' => 'nullable|image|max:2048', // Opcional, pero si se sube debe ser imagen y max 2MB
        ]);

        // Crear una nueva instancia de Producto
        $product = new Product();
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->description = $request->input('description');
        $product->user_id = Auth::id(); // Asignar el ID del vendedor autenticado como user_id
        $product->category_id = $request->input('category_id'); // Asignar la categoría seleccionada

        // Lógica para subir la imagen si se proporciona una
        if ($request->hasFile('image')) {
            // Guarda la imagen en el disco 'public' dentro de una carpeta 'products'
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath; // Guarda la ruta relativa en la base de datos
        }

        // Guardar el producto en la base de datos
        $product->save();

        // Redirigir de vuelta a la lista de productos con un mensaje de éxito
        return redirect()->route('seller.products.index')->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un producto existente.
     */
    public function edit(Product $product)
    {
        // Verificar si el vendedor autenticado es el dueño del producto
        if ($product->user_id !== Auth::id()) {
            return redirect()->route('seller.products.index')->with('error', 'No tienes permiso para editar este producto.');
        }
        $categories = Category::all(); // Pasar las categorías para el formulario de edición
        return view('seller.products.edit', compact('product', 'categories'));
    }

    /**
     * Actualiza los detalles de un producto existente.
     */
    public function update(Request $request, Product $product)
    {
        // Verificar si el vendedor autenticado es el dueño del producto
        if ($product->user_id !== Auth::id()) {
            return redirect()->route('seller.products.index')->with('error', 'No tienes permiso para actualizar este producto.');
        }

        // Validación de los datos
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048', // Opcional para actualización
        ]);

        // Actualizar los campos del producto
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->description = $request->input('description');
        $product->category_id = $request->input('category_id');

        // Lógica para actualizar la imagen si se proporciona una nueva
        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior si existe
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            // Guardar la nueva imagen
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        return redirect()->route('seller.products.index')->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Elimina un producto.
     */
    public function destroy(Product $product)
    {
        // Verificar si el vendedor autenticado es el dueño del producto
        if ($product->user_id !== Auth::id()) {
            return redirect()->route('seller.products.index')->with('error', 'No tienes permiso para eliminar este producto.');
        }

        // Eliminar la imagen asociada si existe
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Eliminar el producto de la base de datos
        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Producto eliminado exitosamente.');
    }

    /**
     * Muestra los pedidos recibidos por el vendedor.
     */
    public function orders()
    {
        $sellerId = Auth::id();

        $orders = Order::whereHas('orderItems', function ($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        })
            ->with(['orderItems.product', 'user']) // Carga ansiosa de orderItems y sus productos, y del usuario (comprador)
            ->orderByDesc('created_at')
            ->get();

        return view('seller.orders.index', compact('orders'));
    }

    /**
     * Muestra los detalles de un pedido específico en un modal (usado para AJAX/Livewire).
     */
    public function showOrderModal($id)
    {
        $order = Order::with(['orderItems.product', 'user'])->findOrFail($id);

        $sellerId = Auth::id();
        $hasSellerItems = $order->orderItems->contains('seller_id', $sellerId);

        if (!$hasSellerItems && Auth::user()->role !== 'admin') {
            abort(403, 'No autorizado para ver los detalles de este pedido.');
        }

        return view('seller.orders.modals.show-modal', compact('order'));
    }

    // Método para cambiar el estado del pedido (ejemplo)
    public function updateOrderStatus(Request $request, Order $order)
    {
        // Validación básica
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        // Verificar que el vendedor actual esté autorizado a modificar este pedido
        $sellerId = Auth::id();
        $hasSellerItems = $order->orderItems->contains('seller_id', $sellerId);

        if (!$hasSellerItems && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'No autorizado para actualizar este pedido.');
        }

        $order->status = $request->input('status');
        $order->save();

        return redirect()->back()->with('success', 'Estado del pedido actualizado correctamente.');
    }
}
