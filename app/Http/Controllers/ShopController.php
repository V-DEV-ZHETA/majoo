<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category');
        $sort = (string) $request->query('sort', 'newest');

        $productsQuery = Product::query()
            ->with('category')
            ->where('is_available', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%");
                });
            })
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId));

        $productsQuery = match ($sort) {
            'price_asc' => $productsQuery->orderBy('price', 'asc'),
            'price_desc' => $productsQuery->orderBy('price', 'desc'),
            'stock_desc' => $productsQuery->orderBy('stock', 'desc'),
            default => $productsQuery->latest('id'),
        };

        $products = $productsQuery
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('shop.index', [
            'products' => $products,
            'categories' => $categories,
            'q' => $q,
            'categoryId' => $categoryId,
            'sort' => $sort,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless((bool) $product->is_available, 404);

        $product->loadMissing('category');

        return view('shop.show', [
            'product' => $product,
        ]);
    }
}

