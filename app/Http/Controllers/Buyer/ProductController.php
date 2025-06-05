<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $query = Product::with('user');
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('seller_ids')) {
            $query->whereIn('user_id', $request->seller_ids);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('category_ids')) {
            $query->whereIn('category_id', $request->category_ids);
        }
        $products = $query->get();
        return view('buyer.products.index', compact('products'));
    }

    public function showProducts($userId)
    {
        $user = User::findOrFail($userId);
        $products = Product::where('seller_id', $user->id)->get();
        return view('buyer.products.by_user', compact('products', 'user'));
    }
}
