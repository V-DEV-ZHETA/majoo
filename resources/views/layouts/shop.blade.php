<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/90 backdrop-blur">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="flex h-16 items-center justify-between gap-4">
                <a href="{{ route('shop.index') }}" class="flex items-center gap-2 font-semibold">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gray-900 text-white">M</span>
                    <span class="hidden sm:inline">{{ config('app.name', 'Majoo') }}</span>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-700">Shop</span>
                </a>

                <nav class="flex items-center gap-2">
                    <a
                        href="{{ route('shop.index') }}"
                        class="rounded-xl px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                    >
                        Produk
                    </a>
                    <a
                        href="{{ route('shop.cart') }}"
                        class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                    >
                        Keranjang
                        @php($cartCount = collect(session('cart', []))->sum(fn($i) => (int) $i['qty']))
                        @if ($cartCount > 0)
                            <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-gray-900 px-2 text-xs font-semibold text-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6">
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-10 text-sm text-gray-500 sm:px-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Majoo') }}.</p>
                <p class="text-gray-400">Halaman shop sederhana (Blade + Tailwind).</p>
            </div>
        </div>
    </footer>
</body>
</html>

