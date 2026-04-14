@extends('layouts.shop')

@section('title', 'Shop - Produk')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Produk</h1>
            <p class="mt-1 text-sm text-gray-600">Cari produk dan klik “Beli” untuk masuk ke keranjang.</p>
        </div>

        <form method="GET" action="{{ route('shop.index') }}" class="surface grid w-full gap-3 p-3 sm:max-w-3xl sm:grid-cols-12">
            <div class="sm:col-span-6">
                <label class="sr-only" for="q">Cari</label>
                <input
                    id="q"
                    name="q"
                    value="{{ $q }}"
                    placeholder="Cari nama / SKU…"
                    class="field"
                />
            </div>

            <div class="sm:col-span-3">
                <label class="sr-only" for="category">Kategori</label>
                <select
                    id="category"
                    name="category"
                    class="field"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected((string) $categoryId === (string) $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="sr-only" for="sort">Urutkan</label>
                <select
                    id="sort"
                    name="sort"
                    class="field"
                >
                    <option value="newest" @selected($sort === 'newest')>Terbaru</option>
                    <option value="price_asc" @selected($sort === 'price_asc')>Harga: Murah → Mahal</option>
                    <option value="price_desc" @selected($sort === 'price_desc')>Harga: Mahal → Murah</option>
                    <option value="stock_desc" @selected($sort === 'stock_desc')>Stok Terbanyak</option>
                </select>
            </div>

            <div class="sm:col-span-12 flex gap-2">
                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Terapkan
                </button>
                <a
                    href="{{ route('shop.index') }}"
                    class="btn btn-secondary"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if ($products->count() === 0)
        <div class="surface border-dashed p-10 text-center">
            <p class="text-sm font-semibold text-gray-900">Produk tidak ditemukan.</p>
            <p class="mt-1 text-sm text-gray-600">Coba ubah kata kunci atau filter.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($products as $product)
                <article class="group overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <a href="{{ route('shop.products.show', $product) }}" class="block">
                        <div class="aspect-4/3 bg-gray-100">
                            @if ($product->image)
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]"
                                    loading="lazy"
                                />
                            @else
                                <div class="flex h-full w-full items-center justify-center text-sm text-gray-500">
                                    No image
                                </div>
                            @endif
                        </div>
                    </a>

                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('shop.products.show', $product) }}" class="block truncate font-semibold text-gray-900">
                                    {{ $product->name }}
                                </a>
                                <p class="mt-1 text-xs text-gray-500">
                                    <span class="badge">{{ $product->category?->name ?? 'Tanpa kategori' }}</span>
                                    @if ($product->sku)
                                        <span class="mx-2 text-gray-200">|</span>
                                        <span class="text-xs text-gray-500">SKU: {{ $product->sku }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-semibold text-gray-900">
                                    Rp {{ number_format((int) $product->price, 0, ',', '.') }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">Stok: {{ (int) $product->stock }}</p>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-3">
                            <a
                                href="{{ route('shop.products.show', $product) }}"
                                class="btn btn-ghost px-3 py-2"
                            >
                                Detail
                            </a>

                            <form method="POST" action="{{ route('shop.cart.add', $product) }}">
                                @csrf
                                <input type="hidden" name="qty" value="1" />
                                <button
                                    type="submit"
                                    @disabled((int) $product->stock < 1)
                                    class="btn btn-primary px-4 py-2"
                                >
                                    Beli
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
@endsection

