<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()
                ->route('shop.cart')
                ->with('error', 'Keranjang kosong. Tambahkan produk terlebih dahulu.');
        }

        try {
            $order = DB::transaction(function () use ($cart) {
                $productIds = collect($cart)->pluck('id')->map(fn ($id) => (int) $id)->all();
                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->where('is_available', true)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $lines = [];
                $total = 0;

                foreach ($cart as $row) {
                    $id = (int) $row['id'];
                    $qty = (int) $row['qty'];
                    $product = $products->get($id);

                    if (!$product || $qty < 1) {
                        throw new \RuntimeException('Beberapa produk tidak lagi tersedia. Perbarui keranjang.');
                    }

                    if ($qty > (int) $product->stock) {
                        throw new \RuntimeException(
                            "Stok tidak cukup untuk \"{$product->name}\". Stok tersedia: {$product->stock}."
                        );
                    }

                    $unitPrice = (int) $product->price;
                    $subtotal = $unitPrice * $qty;
                    $total += $subtotal;

                    $lines[] = [
                        'product' => $product,
                        'qty' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ];
                }

                $order = Order::query()->create([
                    'order_number' => null,
                    'total' => $total,
                    'status' => 'pending',
                ]);

                $order->order_number = 'ORD-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
                $order->save();

                foreach ($lines as $line) {
                    $product = $line['product'];
                    OrderItem::query()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'unit_price' => $line['unit_price'],
                        'qty' => $line['qty'],
                        'subtotal' => $line['subtotal'],
                    ]);

                    $product->decrement('stock', $line['qty']);
                }

                return $order->fresh(['items']);
            });
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('shop.cart')
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('shop.cart')
                ->with('error', 'Checkout gagal. Silakan coba lagi atau hubungi admin.');
        }

        session()->forget('cart');

        return redirect()
            ->route('shop.cart')
            ->with('success', 'Pesanan berhasil dibuat. Nomor order: '.$order->order_number.'. Tim admin akan memproses pesanan Anda.');
    }
}

