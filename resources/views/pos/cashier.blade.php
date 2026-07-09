@php
    $categories = [
        ['name' => 'Semua Menu', 'count' => 110, 'icon' => 'restaurant_menu', 'slug' => 'all'],
        ['name' => 'All Discount', 'count' => 3, 'icon' => 'local_offer', 'slug' => 'discount'],
        ['name' => 'Food', 'count' => 5, 'icon' => 'restaurant', 'slug' => 'food'],
        ['name' => 'Drink', 'count' => 2, 'icon' => 'local_cafe', 'slug' => 'drink'],
        ['name' => 'Roti', 'count' => 20, 'icon' => 'bakery_dining', 'slug' => 'bread'],
        ['name' => 'Kue', 'count' => 20, 'icon' => 'cake', 'slug' => 'cake'],
        ['name' => 'Donat', 'count' => 20, 'icon' => 'donut_small', 'slug' => 'donut'],
        ['name' => 'Pastry', 'count' => 20, 'icon' => 'breakfast_dining', 'slug' => 'pastry'],
        ['name' => 'Sandwich', 'count' => 20, 'icon' => 'lunch_dining', 'slug' => 'sandwich'],
    ];

    $products = [
        [
            'id' => 'beef-crowich',
            'name' => 'Beef Crowich',
            'category' => 'Sandwich',
            'slug' => 'sandwich',
            'type' => 'food',
            'discount' => true,
            'price' => 55000,
            'image' => asset('images/products/beef-crowich.webp'),
        ],
        [
            'id' => 'buttermelt-croissant',
            'name' => 'Buttermelt Croissant',
            'category' => 'Pastry',
            'slug' => 'pastry',
            'type' => 'food',
            'discount' => false,
            'price' => 40000,
            'image' => asset('images/products/buttermelt-croissant.webp'),
        ],
        [
            'id' => 'cereal-cream-donut',
            'name' => 'Cereal Cream Donut',
            'category' => 'Donut',
            'slug' => 'donut',
            'type' => 'food',
            'discount' => true,
            'price' => 24500,
            'image' => asset('images/products/cereal-cream-donut.webp'),
        ],
        [
            'id' => 'cheesy-cheesecake',
            'name' => 'Cheesy Cheesecake',
            'category' => 'Cake',
            'slug' => 'cake',
            'type' => 'food',
            'discount' => false,
            'price' => 37500,
            'image' => asset('images/products/cheesy-cheesecake.webp'),
        ],
        [
            'id' => 'cheezy-sourdough',
            'name' => 'Cheezy Sourdough',
            'category' => 'Bread',
            'slug' => 'bread',
            'type' => 'food',
            'discount' => false,
            'price' => 45000,
            'image' => asset('images/products/cheezy-sourdough.webp'),
        ],
        [
            'id' => 'iced-latte',
            'name' => 'Iced Latte',
            'category' => 'Drink',
            'slug' => 'drink',
            'type' => 'drink',
            'discount' => true,
            'price' => 32000,
            'image' => asset('images/products/iced-latte.webp'),
        ],
        [
            'id' => 'lemon-tea',
            'name' => 'Lemon Tea',
            'category' => 'Drink',
            'slug' => 'drink',
            'type' => 'drink',
            'discount' => false,
            'price' => 22000,
            'image' => asset('images/products/lemon-tea.webp'),
        ],
    ];

    $initialCart = [
        ['id' => 'beef-crowich', 'qty' => 1],
        ['id' => 'cheesy-cheesecake', 'qty' => 2, 'name' => 'Sliced Black Forest', 'price' => 50000, 'category' => 'Cake', 'slug' => 'cake', 'image' => asset('images/products/sliced-black-forest.webp')],
        ['id' => 'cheezy-sourdough', 'qty' => 1, 'name' => 'Solo Floss Bread', 'price' => 45000, 'category' => 'Bread', 'slug' => 'bread', 'image' => asset('images/products/solo-floss-bread.webp')],
    ];

    $todaySummary = [
        'transactions' => 24,
        'revenue' => 2450000,
        'average' => 102000,
        'openOrders' => 3,
        'items' => [
            ['code' => 'TRX-1024', 'time' => '13:42', 'cashier' => 'Yopi', 'method' => 'QRIS', 'total' => 185000],
            ['code' => 'TRX-1023', 'time' => '13:18', 'cashier' => 'Yopi', 'method' => 'Tunai', 'total' => 94000],
            ['code' => 'TRX-1022', 'time' => '12:54', 'cashier' => 'Yopi', 'method' => 'Kartu', 'total' => 132000],
            ['code' => 'TRX-1021', 'time' => '12:27', 'cashier' => 'Yopi', 'method' => 'QRIS', 'total' => 76000],
            ['code' => 'TRX-1020', 'time' => '11:59', 'cashier' => 'Yopi', 'method' => 'QRIS', 'total' => 211000],
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lumina POS - Kasir</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preload" href="{{ asset('fonts/material-symbols/material-symbols-outlined-400.ttf') }}" as="font" type="font/ttf" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div
            class="lumina-shell"
            data-pos-root
            data-products='@json($products)'
            data-initial-cart='@json($initialCart)'
            data-discount="10000"
            data-tax-rate="0.1"
        >
            <section class="lumina-main" aria-label="Terminal kasir">
                <header class="lumina-topbar">
                    <div class="lumina-brand">
                        <button class="lumina-icon-button" type="button" aria-label="Buka menu" data-menu-open>
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                        <div class="lumina-logo">Lumina POS</div>
                    </div>

                    <div class="lumina-toolbar">
                        <div class="lumina-date-pill" aria-label="Tanggal dan waktu kasir">
                            <span class="material-symbols-outlined">calendar_today</span>
                            <span>Rab, 29 Mei 2024</span>
                            <span class="divider" aria-hidden="true"></span>
                            <span class="material-symbols-outlined">schedule</span>
                            <span>07:59 WIB</span>
                        </div>

                        <div class="lumina-status-pill">
                            <span class="lumina-status-dot" aria-hidden="true"></span>
                            <span>Pesanan Terbuka</span>
                        </div>

                        <button class="lumina-icon-button" type="button" aria-label="Profil kasir">
                            <span class="material-symbols-outlined">account_circle</span>
                        </button>
                        <button class="lumina-icon-button is-danger" type="button" aria-label="Keluar">
                            <span class="material-symbols-outlined">power_settings_new</span>
                        </button>
                    </div>
                </header>

                <main class="lumina-content">
                    <nav class="lumina-categories" aria-label="Kategori menu">
                        @foreach ($categories as $category)
                            <button
                                class="lumina-category {{ $loop->first ? 'is-active' : '' }}"
                                type="button"
                                data-category-filter="{{ $category['slug'] }}"
                            >
                                <span class="material-symbols-outlined">{{ $category['icon'] }}</span>
                                <strong>{{ $category['name'] }}</strong>
                                <span>{{ $category['count'] }} Items</span>
                            </button>
                        @endforeach
                    </nav>

                    <div class="lumina-search">
                        <input data-product-search type="search" placeholder="Cari sesuatu yang manis..." aria-label="Cari menu">
                        <span class="material-symbols-outlined" aria-hidden="true">search</span>
                    </div>

                    <div class="lumina-grid" data-product-grid>
                        @foreach ($products as $product)
                            <article
                                class="lumina-product"
                                tabindex="0"
                                role="button"
                                data-product-card
                                data-id="{{ $product['id'] }}"
                                data-name="{{ $product['name'] }}"
                                data-category="{{ $product['category'] }}"
                                data-slug="{{ $product['slug'] }}"
                                data-type="{{ $product['type'] }}"
                                data-discount="{{ $product['discount'] ? 'true' : 'false' }}"
                                data-price="{{ $product['price'] }}"
                                data-image="{{ $product['image'] }}"
                                aria-label="Tambahkan {{ $product['name'] }} ke keranjang"
                            >
                                <div class="lumina-product-image">
                                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
                                </div>
                                <h3>{{ $product['name'] }}</h3>
                                <span class="lumina-badge {{ $product['slug'] }}">{{ $product['category'] }}</span>
                                <div class="lumina-product-price">Rp {{ number_format($product['price'], 0, ',', '.') }}</div>
                            </article>
                        @endforeach
                    </div>
                </main>

            </section>

            <button class="lumina-cart-overlay" type="button" data-cart-close aria-label="Tutup keranjang"></button>
            <button class="lumina-menu-overlay" type="button" data-menu-close aria-label="Tutup menu kasir"></button>
            <button class="lumina-history-overlay" type="button" data-history-close aria-label="Tutup riwayat hari ini"></button>

            <aside class="lumina-menu-panel" aria-label="Menu operasional kasir">
                <div class="lumina-menu-header">
                    <div>
                        <strong>Menu Kasir</strong>
                        <span>Shift Pagi - Yopi</span>
                    </div>
                    <button class="lumina-icon-button" type="button" data-menu-close aria-label="Tutup menu">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="lumina-menu-list">
                    <button class="is-active" type="button" data-menu-close>
                        <span class="material-symbols-outlined">point_of_sale</span>
                        <span>Kasir</span>
                    </button>
                    <button type="button" data-history-open>
                        <span class="material-symbols-outlined">receipt_long</span>
                        <span>Riwayat Hari Ini</span>
                    </button>
                    <button type="button">
                        <span class="material-symbols-outlined">schedule</span>
                        <span>Shift</span>
                    </button>
                </div>
            </aside>

            <aside class="lumina-history-panel" aria-label="Riwayat transaksi hari ini">
                <div class="lumina-history-header">
                    <div>
                        <strong>Riwayat Hari Ini</strong>
                        <span>Rab, 29 Mei 2024</span>
                    </div>
                    <button class="lumina-icon-button" type="button" data-history-close aria-label="Tutup riwayat">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="lumina-history-stats">
                    <div>
                        <span>Transaksi</span>
                        <strong>{{ $todaySummary['transactions'] }}</strong>
                    </div>
                    <div>
                        <span>Omzet</span>
                        <strong>Rp {{ number_format($todaySummary['revenue'], 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <span>Rata-rata</span>
                        <strong>Rp {{ number_format($todaySummary['average'], 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <span>Order Terbuka</span>
                        <strong>{{ $todaySummary['openOrders'] }}</strong>
                    </div>
                </div>

                <div class="lumina-history-list">
                    @foreach ($todaySummary['items'] as $transaction)
                        <article class="lumina-history-item">
                            <div>
                                <strong>{{ $transaction['code'] }}</strong>
                                <span>{{ $transaction['time'] }} WIB - {{ $transaction['cashier'] }}</span>
                            </div>
                            <div>
                                <span>{{ $transaction['method'] }}</span>
                                <strong>Rp {{ number_format($transaction['total'], 0, ',', '.') }}</strong>
                            </div>
                        </article>
                    @endforeach
                </div>
            </aside>

            <aside class="lumina-sidebar" aria-label="Ringkasan pesanan">
                <div class="lumina-cart-drawer-title">
                    <div>
                        <strong>Keranjang</strong>
                        <span data-mobile-cart-count>0 item</span>
                    </div>
                    <button class="lumina-icon-button" type="button" data-cart-close aria-label="Tutup keranjang">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="lumina-sidebar-header">
                    <div class="lumina-segment" role="group" aria-label="Jenis pesanan">
                        <button class="is-active" type="button" data-order-type="dine-in">Dine In</button>
                        <button type="button" data-order-type="take-away">Take Away</button>
                    </div>
                    <label class="lumina-table-select" data-table-field>
                        <select data-table-select aria-label="Pilih meja">
                            @for ($table = 1; $table <= 5; $table++)
                                <option value="{{ $table }}" @selected($table === 5)>Meja {{ str_pad((string) $table, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                    </label>
                </div>

                <div class="lumina-cart" data-cart-list></div>

                <div class="lumina-summary">
                    <div class="lumina-summary-row">
                        <span>Subtotal</span>
                        <span data-subtotal>Rp 0</span>
                    </div>
                    <div class="lumina-summary-row">
                        <span>Pajak (10%)</span>
                        <span data-tax>Rp 0</span>
                    </div>
                    <div class="lumina-summary-row is-discount">
                        <span>Diskon</span>
                        <span data-discount-label>-Rp 0</span>
                    </div>
                    <div class="lumina-total">
                        <span>TOTAL</span>
                        <span data-total>Rp 0</span>
                    </div>
                    <div class="lumina-payment-row">
                        <button class="lumina-soft-button" type="button">
                            <span>Promo Applied</span>
                            <span class="material-symbols-outlined">check_circle</span>
                        </button>
                        <button class="lumina-outline-button" type="button">QRIS</button>
                    </div>
                    <button class="lumina-primary-button" type="button">Buat Pesanan</button>
                </div>
            </aside>

            <nav class="lumina-mobile-nav" aria-label="Navigasi kasir mobile">
                <button class="is-active" type="button" data-product-tab>
                    <span class="material-symbols-outlined">storefront</span>
                    <span>Produk</span>
                </button>
                <button type="button" data-cart-open>
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span>Cart</span>
                    <strong data-cart-badge>0</strong>
                </button>
            </nav>
        </div>
    </body>
</html>
