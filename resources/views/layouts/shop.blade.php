<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Base ─────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Instrument Sans', system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ── Smooth page entrance ─────────────── */
        @keyframes pageIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        main { animation: pageIn .45s ease-out both; }

        /* ── Header shadow on scroll ──────────── */
        .site-header {
            transition: box-shadow .3s ease, background-color .3s ease;
        }
        .site-header.scrolled {
            box-shadow: 0 1px 12px rgba(0,0,0,.06);
            background-color: rgba(255,255,255,.97);
        }

        /* ── Nav links ────────────────────────── */
        .nav-link {
            position: relative;
            transition: color .2s ease, background-color .2s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 4px; left: 50%;
            width: 0; height: 2px;
            background: #111;
            border-radius: 1px;
            transition: width .25s ease, left .25s ease;
        }
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 16px; left: calc(50% - 8px);
        }
        .nav-link.active { color: #111 !important; font-weight: 600; }

        /* ── Cart badge bounce ────────────────── */
        @keyframes badgePop {
            0%   { transform: scale(.6); }
            60%  { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        .cart-badge { animation: badgePop .35s ease-out; }

        /* ── Flash messages ───────────────────── */
        .flash {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.5;
            animation: flashIn .4s ease-out both;
            position: relative;
            overflow: hidden;
        }
        .flash::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity .3s ease;
        }
        .flash.removing {
            animation: flashOut .35s ease-in both;
        }

        @keyframes flashIn {
            from { opacity: 0; transform: translateY(-8px) scale(.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes flashOut {
            to { opacity: 0; transform: translateY(-8px) scale(.97); height: 0; padding: 0 18px; margin: 0; border-width: 0; }
        }

        .flash-success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        .flash-success .flash-icon {
            flex-shrink: 0;
            width: 22px; height: 22px;
            background: #10b981;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .flash-success .flash-icon svg { width: 13px; height: 13px; stroke: #fff; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

        .flash-error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .flash-error .flash-icon {
            flex-shrink: 0;
            width: 22px; height: 22px;
            background: #ef4444;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .flash-error .flash-icon svg { width: 13px; height: 13px; stroke: #fff; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

        /* auto-dismiss progress bar */
        .flash .flash-progress {
            position: absolute;
            bottom: 0; left: 0;
            height: 3px;
            border-radius: 0 0 14px 14px;
            animation: flashProgress 3.5s linear both;
        }
        .flash-success .flash-progress { background: #34d399; }
        .flash-error .flash-progress   { background: #f87171; }

        @keyframes flashProgress {
            from { width: 100%; }
            to   { width: 0%; }
        }

        /* ── Footer subtle hover ──────────────── */
        .footer-link {
            transition: color .2s ease;
        }
        .footer-link:hover {
            color: #111;
        }

        /* ── Focus visible for accessibility ──── */
        a:focus-visible, button:focus-visible {
            outline: 2px solid #111;
            outline-offset: 2px;
            border-radius: 8px;
        }

        /* ── Selection color ──────────────────── */
        ::selection {
            background: #111;
            color: #fff;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">

    <!-- ════════ HEADER ════════ -->
    <header class="site-header sticky top-0 z-40 border-b border-gray-100 bg-white/90 backdrop-blur-md">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="flex h-16 items-center justify-between gap-4">

                <!-- Logo -->
                <a href="{{ route('shop.index') }}" class="flex items-center gap-2.5 group">
                    <span class="inline-flex items-center gap-2 rounded-xl px-2.5 py-2 transition hover:bg-gray-50">
                        <span class="text-[15px] font-extrabold tracking-tight text-gray-900">MAJOO</span>
                        <span class="text-gray-300 text-xs">/</span>
                        <span class="text-sm font-semibold tracking-tight text-gray-500">SHOP</span>
                    </span>
                </a>

                <!-- Nav -->
                <nav class="flex items-center gap-1">
                    <a
                        href="{{ route('shop.index') }}"
                        class="nav-link {{ request()->routeIs('shop.index') ? 'active' : '' }} rounded-xl px-3.5 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50"
                    >
                        Produk
                    </a>
                    <a
                        href="{{ route('shop.cart') }}"
                        class="nav-link {{ request()->routeIs('shop.cart') ? 'active' : '' }} inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                        Keranjang
                        @php($cartCount = collect(session('cart', []))->sum(fn($i) => (int) $i['qty']))
                        @if ($cartCount > 0)
                            <span class="cart-badge inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-gray-900 px-1.5 text-[11px] font-bold text-white leading-none">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- ════════ MAIN ════════ -->
    <main class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 sm:py-10">
        {{-- Flash: Success --}}
        @if (session('success'))
            <div class="flash flash-success mb-6" data-auto-dismiss>
                <span class="flash-icon">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.classList.add('removing'); setTimeout(() => this.parentElement.remove(), 350)" class="ml-auto shrink-0 p-1 rounded-lg hover:bg-emerald-200/50 transition-colors" aria-label="Tutup">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="flash-progress"></div>
            </div>
        @endif

        {{-- Flash: Error --}}
        @if (session('error'))
            <div class="flash flash-error mb-6" data-auto-dismiss>
                <span class="flash-icon">
                    <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </span>
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.classList.add('removing'); setTimeout(() => this.parentElement.remove(), 350)" class="ml-auto shrink-0 p-1 rounded-lg hover:bg-red-200/50 transition-colors" aria-label="Tutup">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="flash-progress"></div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- ════════ FOOTER ════════ -->
    {{-- <footer class="border-t border-gray-100 bg-white mt-auto">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-gray-100 text-gray-500 text-xs font-bold">M</span>
                    <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ config('app.name', 'Majoo') }}.</p>
                </div>
                <p class="text-xs text-gray-400 leading-relaxed">Halaman shop sederhana &mdash; Blade + Tailwind CSS.</p>
            </div>
        </div>
    </footer> --}}

    <!-- ════════ SCRIPTS ════════ -->
    <script>
        // Header shadow on scroll
        const header = document.querySelector('.site-header');
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    header.classList.toggle('scrolled', window.scrollY > 8);
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        // Auto-dismiss flash messages
        document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
            setTimeout(() => {
                if (el.parentElement) {
                    el.classList.add('removing');
                    setTimeout(() => el.remove(), 350);
                }
            }, 3800);
        });
    </script>
</body>
</html>f