@extends('layouts.shop')

@section('title', $product->name . ' - Shop')

@section('content')
    <div class="mb-6">
        <a href="{{ route('shop.index') }}" class="btn btn-ghost px-0 py-0 text-sm">
            ← Kembali ke produk
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-7">
            <div class="surface overflow-hidden">
                <div class="aspect-4/3 bg-gray-100">
                    @if ($product->image)
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-cover"
                        />
                    @else
                        <div class="flex h-full w-full items-center justify-center text-sm text-gray-500">
                            No image
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="surface p-6">
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $product->name }}</h1>
                <p class="mt-2 text-sm text-gray-600">
                    <span class="badge">{{ $product->category?->name ?? 'Tanpa kategori' }}</span>
                    @if ($product->sku)
                        <span class="mx-2 text-gray-200">|</span>
                        <span class="text-xs text-gray-500">SKU: {{ $product->sku }}</span>
                    @endif
                </p>

                <div class="mt-5 flex items-end justify-between gap-3">
                    <p class="text-2xl font-semibold text-gray-900">
                        Rp {{ number_format((int) $product->price, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-600">Stok: {{ (int) $product->stock }}</p>
                </div>

                @if ($product->description)
                    <div class="mt-6">
                        <h2 class="text-sm font-semibold text-gray-900">Deskripsi</h2>
                        <div class="prose prose-sm mt-2 max-w-none text-gray-700">
                            {{ $product->description }}
                        </div>
                    </div>
                @endif

                <div class="mt-6">
                    <form method="POST" action="{{ route('shop.cart.add', $product) }}" class="flex items-end gap-3">
                        @csrf
                        <div class="w-28">
                            <label for="qty" class="text-xs font-semibold text-gray-700">Qty</label>
                            <input
                                id="qty"
                                name="qty"
                                type="number"
                                min="1"
                                max="99"
                                value="1"
                                class="field mt-1 px-3 py-2"
                            />
                        </div>

                        <button
                            type="submit"
                            @disabled((int) $product->stock < 1)
                            class="btn btn-primary flex-1 px-4 py-3"
                        >
                            Beli (Tambah ke keranjang)
                        </button>
                    </form>

                    <div class="mt-3 flex gap-2">
                        <a
                            href="{{ route('shop.cart') }}"
                            class="btn btn-secondary flex-1 px-4 py-3"
                        >
                            Lihat keranjang
                        </a>
                        <a
                            href="{{ route('shop.index') }}"
                            class="btn btn-ghost px-4 py-3"
                        >
                            Lanjut belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

