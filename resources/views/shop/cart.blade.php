@extends('layouts.shop')

@section('title', 'Keranjang - Shop')

@section('content')
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Keranjang</h1>
            <p class="mt-1 text-sm text-gray-600">Perbarui jumlah, lalu checkout untuk mengirim pesanan ke panel admin.</p>
        </div>

        <div class="flex gap-2">
            <a
                href="{{ route('shop.index') }}"
                class="btn btn-secondary"
            >
                + Tambah produk
            </a>
            @if (!empty($cart))
                <form method="POST" action="{{ route('shop.cart.clear') }}">
                    @csrf
                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        Kosongkan
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if (empty($cart))
        <div class="surface border-dashed p-10 text-center">
            <p class="text-sm font-semibold text-gray-900">Keranjang masih kosong.</p>
            <p class="mt-1 text-sm text-gray-600">Silakan pilih produk dulu.</p>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-12">
            <form method="POST" action="{{ route('shop.cart.update') }}" class="lg:col-span-8">
                @csrf

                <div class="surface overflow-hidden">
                    <div class="divide-y divide-gray-200">
                        @foreach ($cart as $item)
                            <div class="flex gap-4 p-4">
                                <div class="h-20 w-20 overflow-hidden rounded-2xl bg-gray-100">
                                    @if (!empty($item['image']))
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::url($item['image']) }}"
                                            alt="{{ $item['name'] }}"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                        />
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-gray-900">{{ $item['name'] }}</p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                @if (!empty($item['sku']))
                                                    SKU: {{ $item['sku'] }}
                                                @endif
                                            </p>
                                        </div>
                                        <p class="shrink-0 text-sm font-semibold">
                                            Rp {{ number_format((int) $item['price'], 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="mt-3 flex items-end justify-between gap-3">
                                        <div class="w-28">
                                            <label class="text-xs font-semibold text-gray-700" for="qty-{{ $item['id'] }}">Qty</label>
                                            <input
                                                id="qty-{{ $item['id'] }}"
                                                type="number"
                                                min="0"
                                                max="99"
                                                name="items[{{ $item['id'] }}][qty]"
                                                value="{{ (int) $item['qty'] }}"
                                                class="field mt-1 px-3 py-2"
                                            />
                                            <p class="mt-1 text-[11px] text-gray-500">Isi 0 untuk hapus</p>
                                        </div>

                                        <p class="text-sm text-gray-700">
                                            Subtotal:
                                            <span class="font-semibold text-gray-900">
                                                Rp {{ number_format(((int) $item['qty']) * ((int) $item['price']), 0, ',', '.') }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button
                        type="submit"
                        class="btn btn-primary px-5 py-3"
                    >
                        Update keranjang
                    </button>
                    <a
                        href="{{ route('shop.index') }}"
                        class="btn btn-secondary px-5 py-3"
                    >
                        Lanjut belanja
                    </a>
                </div>
            </form>

            <div class="lg:col-span-4">
                <div class="surface p-6">
                    <h2 class="text-lg font-semibold">Ringkasan</h2>
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span class="text-gray-600">Total</span>
                        <span class="font-semibold">Rp {{ number_format((int) $total, 0, ',', '.') }}</span>
                    </div>

                    <form method="POST" action="{{ route('shop.cart.checkout') }}" class="mt-5">
                        @csrf
                        <button
                            type="submit"
                            class="btn btn-primary w-full px-5 py-3"
                        >
                            Checkout
                        </button>
                    </form>

                    <p class="mt-3 text-xs text-gray-500">
                        Pesanan akan masuk ke <strong>Orderan Masuk</strong> di panel admin untuk diproses.
                    </p>
                </div>
            </div>
        </div>
    @endif
@endsection
