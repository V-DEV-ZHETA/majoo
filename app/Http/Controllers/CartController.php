<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $total = collect($cart)->sum(fn ($item) => ((int) $item['qty']) * ((int) $item['price']));

        return view('shop.cart', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    public function add(Request $request, Product $product)
    {
        abort_unless((bool) $product->is_available, 404);

        $qty = (int) $request->input('qty', 1);
        $qty = max(1, min(99, $qty));

        $cart = session()->get('cart', []);
        $key = (string) $product->getKey();

        $existingQty = isset($cart[$key]) ? (int) $cart[$key]['qty'] : 0;
        $newQty = min((int) $product->stock, $existingQty + $qty);
        $newQty = max(1, $newQty);

        $cart[$key] = [
            'id' => (int) $product->getKey(),
            'name' => (string) $product->name,
            'price' => (int) $product->price,
            'qty' => $newQty,
            'image' => $product->image,
            'sku' => $product->sku,
        ];

        session()->put('cart', $cart);

        return redirect()
            ->route('shop.cart')
            ->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request)
    {
        $items = (array) $request->input('items', []);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.cart');
        }

        $productIds = array_map('intval', array_keys($cart));
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get(['id', 'stock', 'is_available'])
            ->keyBy('id');

        foreach ($items as $id => $payload) {
            $id = (int) $id;
            if (!isset($cart[(string) $id])) {
                continue;
            }

            $qty = (int) ($payload['qty'] ?? 1);
            $qty = max(0, min(99, $qty));

            $product = $products->get($id);
            if (!$product || !(bool) $product->is_available) {
                unset($cart[(string) $id]);
                continue;
            }

            if ($qty === 0) {
                unset($cart[(string) $id]);
                continue;
            }

            $cart[(string) $id]['qty'] = min((int) $product->stock, $qty);
            if ((int) $cart[(string) $id]['qty'] < 1) {
                unset($cart[(string) $id]);
            }
        }

        session()->put('cart', $cart);

        return redirect()
            ->route('shop.cart')
            ->with('success', 'Keranjang diperbarui.');
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);
        unset($cart[(string) $product->getKey()]);
        session()->put('cart', $cart);

        return redirect()
            ->route('shop.cart')
            ->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()
            ->route('shop.cart')
            ->with('success', 'Keranjang dikosongkan.');
    }
}

