<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $linktree->meta_title ?: $linktree->title . ' - Linktree' }}</title>
    <meta name="description" content="{{ $linktree->meta_description ?: $linktree->bio }}">
    <meta property="og:title" content="{{ $linktree->title }}">
    <meta property="og:description" content="{{ $linktree->bio }}">
    @if($linktree->avatar)
    <meta property="og:image" content="{{ asset('linktree/avatars/' . $linktree->avatar) }}">
    @endif
    <meta property="og:url" content="{{ url('/l/' . $linktree->custom_url) }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta name="twitter:title" content="{{ $linktree->title }}">
    <meta name="twitter:description" content="{{ $linktree->bio }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css'])

    {{-- Schema.org JSON-LD Structured Data --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "ProfilePage",
        "name": @json($linktree->title),
        "description": @json($linktree->meta_description ?: $linktree->bio),
        "url": @json(url('/l/' . $linktree->custom_url)),
        @if($linktree->avatar)
        "image": @json(asset('linktree/avatars/' . $linktree->avatar)),
        @endif
        "mainEntity": {
            "@@type": "Person",
            "name": @json($vendor->name ?? $vendor->nama_vendor ?? $linktree->title),
            "description": @json($linktree->bio),
            @if($linktree->show_qris && $linktree->qris_image)
            "paymentAccepted": "QRIS, Cash",
            @endif
            "sameAs": [
                @foreach($linktree->activeSocials as $social)
                @json($social->url){{ $loop->last ? '' : ',' }}
                @endforeach
            ]
        },
        "publisher": {
            "@@type": "Organization",
            "name": "Grafika Printing",
            "url": @json(url('/'))
        }
    }
    </script>
    <style>
        [x-cloak] { display: none !important; }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            background-color: {{ $linktree->bg_color }};
            color: {{ $linktree->text_color }};
            display: flex;
            justify-content: center;
            padding: 0;
            margin: 0;
        }

        .container {
            max-width: 420px;
            width: 100%;
            padding: 24px 16px 40px;
        }

        /* Banner */
        .banner {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .banner-placeholder {
            width: 100%;
            height: 60px;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        /* Profile */
        .profile {
            text-align: center;
            margin-bottom: 24px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid {{ $linktree->primary_color }}20;
            margin-bottom: 12px;
        }

        .avatar-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: {{ $linktree->primary_color }};
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            margin: 0 auto 12px;
            border: 3px solid {{ $linktree->primary_color }}20;
        }

        .name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .bio {
            font-size: 14px;
            opacity: 0.7;
            line-height: 1.4;
        }

        /* Links */
        .links {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .link-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 20px;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }

        .link-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .link-item:active {
            transform: translateY(0);
        }

        /* Button Styles */
        .btn-rounded {
            border-radius: 8px;
        }

        .btn-square {
            border-radius: 4px;
        }

        .btn-pill {
            border-radius: 50px;
        }

        /* Product Catalog */
        .products-section {
            margin-bottom: 20px;
        }

        .products-title {
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 12px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 16px;
        }

        .product-card {
            background: {{ $linktree->primary_color }}08;
            border: 1px solid {{ $linktree->primary_color }}15;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: {{ $linktree->primary_color }}30;
        }

        .product-image {
            width: 100%;
            height: 100px;
            object-fit: cover;
            background: {{ $linktree->primary_color }}05;
        }

        .product-image-placeholder {
            width: 100%;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: {{ $linktree->primary_color }}05;
            color: {{ $linktree->primary_color }}60;
            font-size: 24px;
        }

        .product-info {
            padding: 8px 10px;
        }

        .product-name {
            font-size: 12px;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 2px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-size: 11px;
            font-weight: 700;
            color: {{ $linktree->primary_color }};
        }

        @media (max-width: 360px) {
            .product-grid {
                grid-template-columns: 1fr;
            }
        }

        /* QRIS */
        .qris-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .qris-image {
            max-width: 180px;
            border-radius: 8px;
            border: 2px solid {{ $linktree->primary_color }}20;
        }

        .qris-label {
            font-size: 12px;
            opacity: 0.6;
            margin-top: 6px;
        }

        /* Social Links */
        .socials {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.2s ease;
            background: {{ $linktree->primary_color }}10;
        }

        .social-link:hover {
            transform: scale(1.1);
            background: {{ $linktree->primary_color }}25;
        }

        .social-link svg {
            width: 20px;
            height: 20px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 32px;
            font-size: 11px;
            opacity: 0.4;
        }

        .footer a {
            color: inherit;
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .container {
                padding: 16px 12px 32px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Banner -->
        @if($linktree->banner)
        <img src="{{ asset('linktree/banners/' . $linktree->banner) }}" alt="Banner" class="banner">
        @else
        <div class="banner-placeholder" style="background: linear-gradient(135deg, {{ $linktree->primary_color }}30, {{ $linktree->secondary_color }}30);"></div>
        @endif

        <!-- Profile -->
        <div class="profile">
            @if($linktree->avatar)
            <img src="{{ asset('linktree/avatars/' . $linktree->avatar) }}" alt="{{ $linktree->title }}" class="avatar">
            @else
            <div class="avatar-placeholder">{{ strtoupper(substr($linktree->title, 0, 1)) }}</div>
            @endif
            <div class="name">{{ $linktree->title }}</div>
            @if($linktree->bio)
            <div class="bio">{{ $linktree->bio }}</div>
            @endif
        </div>

        <!-- Links -->
        <div class="links">
            @foreach($linktree->activeLinks as $link)
            <a href="{{ route('linktree.click', [$linktree->custom_url, $link->id]) }}"
               class="link-item btn-{{ $linktree->button_style }}"
               style="background: {{ $linktree->primary_color }}; color: white;">
                {!! $link->icon_html !!}
                {{ $link->title }}
            </a>
            @endforeach
        </div>

        <!-- Product Catalog -->
        @if($hasProducts && $linktree->activeLinktreeProducts->count() > 0)
        <div class="products-section" x-data="productModal()">
            <div class="products-title" style="color: {{ $linktree->text_color }};"><i class="fas fa-shopping-bag" style="margin-right: 4px;"></i> Produk</div>
            <div class="product-grid">
                @foreach($linktree->activeLinktreeProducts as $lp)
                <div class="product-card" @click="openProduct('{{ $lp->id }}'); trackProductClick('{{ $lp->id }}')" style="cursor: pointer;">
                    @if($lp->display_image)
                    <img src="{{ asset('produk_gambar/' . $lp->display_image) }}" alt="{{ $lp->display_name }}" class="product-image" loading="lazy">
                    @else
                    <div class="product-image-placeholder"><i class="fas fa-box" style="font-size: 24px;"></i></div>
                    @endif
                    <div class="product-info">
                        @if($lp->kategori_name && $lp->kategori_name !== '-')
                        <div style="display: inline-block; font-size: 9px; padding: 1px 6px; border-radius: 4px; background: {{ $linktree->primary_color }}15; color: {{ $linktree->primary_color }}; margin-bottom: 3px; font-weight: 500;">{{ $lp->kategori_name }}</div>
                        @endif
                        <div class="product-name">{{ $lp->display_name }}</div>
                        @if($lp->spesifikasi_summary && $lp->spesifikasi_summary !== '-')
                        <div style="font-size: 9px; color: {{ $linktree->text_color }}; opacity: 0.5; margin-bottom: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $lp->spesifikasi_summary }}">{{ $lp->spesifikasi_summary }}</div>
                        @endif
                        @if($lp->display_price)
                        <div class="product-price">{{ $lp->display_price }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Payment Section -->
        @if($linktree->show_qris)
        <div class="qris-section" style="background: {{ $linktree->primary_color }}08; border: 1px solid {{ $linktree->primary_color }}20; border-radius: 12px; padding: 16px; margin-top: 16px; text-align: center;">

            {{-- Xendit QRIS Payment (active) --}}
            @if($xenditActive && $qrisAvailable)
                @if($linktree->qris_image)
                    <img src="{{ asset('linktree/qris/' . $linktree->qris_image) }}" alt="QRIS" class="qris-image" style="max-width: 200px; border-radius: 8px; margin-bottom: 8px;">
                    <div class="qris-label" style="font-size: 12px; opacity: 0.7;">Scan untuk pembayaran QRIS</div>
                @endif

                {{-- Dynamic QRIS Payment via Xendit --}}
                <div id="qris-dynamic-section" style="margin-top: 12px;">
                    <div id="qris-payment-form">
                        <div style="margin-bottom: 8px;">
                            <input type="number" id="qris-amount" placeholder="Jumlah pembayaran (Rp)" min="1000" max="10000000"
                                style="width: 100%; padding: 10px; border: 1px solid {{ $linktree->primary_color }}30; border-radius: 8px; font-size: 14px; text-align: center;">
                        </div>
                        <button onclick="generateDynamicQris()" id="btn-generate-qris"
                            style="width: 100%; padding: 10px; background: {{ $linktree->primary_color }}; color: white; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; font-weight: 500;">
                            <i class="fas fa-credit-card" style="margin-right: 4px;"></i> Bayar via QRIS
                        </button>
                    </div>

                    <!-- Loading State -->
                    <div id="qris-loading" style="display: none; padding: 20px;">
                        <div style="font-size: 14px; opacity: 0.7;">Memproses pembayaran...</div>
                    </div>

                    <!-- QR Code Result -->
                    <div id="qris-result" style="display: none; padding: 12px;">
                        <div id="qris-qr-container" style="margin-bottom: 12px;"></div>
                        <div id="qris-amount-display" style="font-weight: 600; font-size: 16px; margin-bottom: 8px;"></div>
                        <div id="qris-status" style="font-size: 12px; opacity: 0.7; margin-bottom: 12px;">Menunggu pembayaran...</div>
                        <button onclick="resetQrisForm()" style="padding: 8px 16px; background: transparent; color: {{ $linktree->primary_color }}; border: 1px solid {{ $linktree->primary_color }}40; border-radius: 8px; font-size: 12px; cursor: pointer;">
                            Bayar Lagi
                        </button>
                    </div>

                    <!-- Error State -->
                    <div id="qris-error" style="display: none; padding: 12px; color: #dc3545; font-size: 13px;"></div>
                </div>

            {{-- Xendit active but no QRIS image - show static QRIS only --}}
            @elseif($xenditActive && $linktree->qris_image && !$qrisAvailable)
                <img src="{{ asset('linktree/qris/' . $linktree->qris_image) }}" alt="QRIS" class="qris-image" style="max-width: 200px; border-radius: 8px; margin-bottom: 8px;">
                <div class="qris-label" style="font-size: 12px; opacity: 0.7;">Scan untuk pembayaran QRIS</div>

            {{-- Xendit NOT active - Manual Transfer fallback --}}
            @elseif(!$xenditActive)
                <div style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: {{ $linktree->text_color }};">
                    <i class="fas fa-credit-card" style="margin-right: 4px;"></i> Pembayaran Transfer Manual
                </div>

                @if($bankAccount)
                    <div style="background: {{ $linktree->primary_color }}05; border: 1px solid {{ $linktree->primary_color }}15; border-radius: 8px; padding: 12px; margin-bottom: 12px; text-align: left;">
                        @if($bankAccount['bank_name'])
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                <span style="font-size: 12px; opacity: 0.6;">Bank</span>
                                <span style="font-size: 13px; font-weight: 600;">{{ $bankAccount['bank_name'] }}</span>
                            </div>
                        @endif
                        @if($bankAccount['account_number'])
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                <span style="font-size: 12px; opacity: 0.6;">No. Rekening</span>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span style="font-size: 13px; font-weight: 600; font-family: monospace;">{{ $bankAccount['account_number'] }}</span>
                                    <button onclick="copyBankNumber('{{ $bankAccount['account_number'] }}')" style="font-size: 11px; padding: 2px 6px; border: 1px solid {{ $linktree->primary_color }}30; border-radius: 4px; background: transparent; cursor: pointer;">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                        @if($bankAccount['account_name'])
                            <div style="display: flex; justify-content: space-between;">
                                <span style="font-size: 12px; opacity: 0.6;">Atas Nama</span>
                                <span style="font-size: 13px; font-weight: 600;">{{ $bankAccount['account_name'] }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <div style="font-size: 11px; opacity: 0.5; margin-top: 8px;">
                    Lakukan transfer ke rekening di atas, lalu hubungi penjual untuk konfirmasi.
                </div>
            @endif
        </div>
        @endif

        <!-- Social Links -->
        @if($linktree->activeSocials->count() > 0)
        <div class="socials">
            @foreach($linktree->activeSocials as $social)
            <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" class="social-link" title="{{ ucfirst($social->platform) }}">
                {!! $social->icon_html !!}
            </a>
            @endforeach
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <a href="{{ url('/') }}">Grafika Printing</a>
        </div>
    </div>

    <script>
        let qrisInvoiceId = null;
        let qrisCheckInterval = null;

        function formatRupiah(num) {
            return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function generateDynamicQris() {
            const amountInput = document.getElementById('qris-amount');
            const amount = parseInt(amountInput.value);

            if (!amount || amount < 1000) {
                amountInput.style.borderColor = '#dc3545';
                return;
            }

            amountInput.style.borderColor = '{{ $linktree->primary_color }}30';

            // Show loading
            document.getElementById('qris-payment-form').style.display = 'none';
            document.getElementById('qris-loading').style.display = 'block';
            document.getElementById('qris-error').style.display = 'none';
            document.getElementById('qris-result').style.display = 'none';

            fetch('{{ route("linktree.pay.qris", $linktree->custom_url) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: amount,
                    description: 'Pembayaran ke {{ addslashes($vendor->name ?? $vendor->nama_vendor ?? $linktree->title) }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    qrisInvoiceId = data.invoice_id;

                    document.getElementById('qris-loading').style.display = 'none';
                    document.getElementById('qris-result').style.display = 'block';
                    document.getElementById('qris-amount-display').textContent = formatRupiah(data.amount);

                    // Show QR code or redirect URL
                    const qrContainer = document.getElementById('qris-qr-container');
                    if (data.qr_code) {
                        qrContainer.innerHTML = '<img src="' + data.qr_code + '" alt="QRIS Code" style="max-width: 200px; border-radius: 8px;">';
                    } else if (data.invoice_url) {
                        qrContainer.innerHTML = '<a href="' + data.invoice_url + '" target="_blank" style="display: inline-block; padding: 12px 24px; background: {{ $linktree->primary_color }}; color: white; border-radius: 8px; text-decoration: none; font-weight: 500;">Buka Halaman Pembayaran</a>';
                    }

                    // Start checking payment status
                    startPaymentCheck();
                } else {
                    showError(data.error || 'Gagal membuat pembayaran');
                }
            })
            .catch(error => {
                showError('Terjadi kesalahan. Silakan coba lagi.');
            });
        }

        function startPaymentCheck() {
            if (qrisCheckInterval) clearInterval(qrisCheckInterval);

            qrisCheckInterval = setInterval(() => {
                if (!qrisInvoiceId) return;

                fetch('{{ route("linktree.pay.status", [$linktree->custom_url, "__INVOICE__"]) }}'.replace('__INVOICE__', qrisInvoiceId))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.status === 'PAID') {
                            clearInterval(qrisCheckInterval);
                            document.getElementById('qris-status').innerHTML = '<i class="fas fa-check-circle"></i> Pembayaran berhasil! Terima kasih.';
                            document.getElementById('qris-status').style.color = '#28a745';
                        }
                    })
                    .catch(() => {});
            }, 5000);
        }

        function resetQrisForm() {
            if (qrisCheckInterval) clearInterval(qrisCheckInterval);
            qrisInvoiceId = null;
            document.getElementById('qris-payment-form').style.display = 'block';
            document.getElementById('qris-loading').style.display = 'none';
            document.getElementById('qris-result').style.display = 'none';
            document.getElementById('qris-error').style.display = 'none';
            document.getElementById('qris-amount').value = '';
        }

        function trackProductClick(productId) {
            // Track product click (optional analytics)
            fetch('{{ url("/l/" . $linktree->custom_url . "/product-click") }}/' + productId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).catch(() => {});
        }

        function showError(msg) {
            document.getElementById('qris-loading').style.display = 'none';
            document.getElementById('qris-result').style.display = 'none';
            document.getElementById('qris-error').style.display = 'block';
            document.getElementById('qris-error').textContent = msg;
            setTimeout(() => {
                document.getElementById('qris-error').style.display = 'none';
                document.getElementById('qris-payment-form').style.display = 'block';
            }, 3000);
        }

        function copyBankNumber(number) {
            navigator.clipboard.writeText(number).then(() => {
                alert('Nomor rekening berhasil disalin: ' + number);
            }).catch(() => {
                // Fallback
                const textArea = document.createElement('textarea');
                textArea.value = number;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Nomor rekening berhasil disalin: ' + number);
            });
        }
    </script>

    <!-- ==================== Product Detail Modal ==================== -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         x-cloak
         style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); padding: 16px;">
        <div @click.outside="open = false"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
             style="background: white; border-radius: 16px; max-width: 480px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
            <div style="padding: 24px;">
                <!-- Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <h3 style="font-size: 18px; font-weight: 700; color: #111827; flex: 1; margin-right: 12px;" x-text="product?.name"></h3>
                    <button @click="open = false" style="color: #9ca3af; font-size: 20px; cursor: pointer; background: none; border: none; padding: 4px; line-height: 1;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Product Image -->
                <div style="margin-bottom: 16px;" x-show="product?.image">
                    <img :src="product?.image" style="width: 100%; height: 192px; object-fit: cover; border-radius: 12px;">
                </div>

                <!-- Price -->
                <div style="margin-bottom: 16px;" x-show="product?.price">
                    <span style="font-size: 22px; font-weight: 700; color: #16a34a;" x-text="product?.price"></span>
                </div>

                <!-- Description -->
                <p style="color: #6b7280; font-size: 14px; margin-bottom: 16px;" x-text="product?.description" x-show="product?.description"></p>

                <!-- Spesifikasi -->
                <template x-if="specs && specs.length > 0">
                    <div style="margin-bottom: 16px;">
                        <h4 style="font-weight: 600; color: #1f2937; font-size: 14px; margin-bottom: 12px;">Spesifikasi:</h4>
                        <template x-for="(spec, index) in specs" :key="index">
                            <div style="margin-bottom: 12px;">
                                <label style="display: block; font-size: 12px; font-weight: 500; color: #6b7280; margin-bottom: 4px;">
                                    <span x-text="spec.nama"></span>
                                    <span x-show="spec.satuan" style="color: #9ca3af;" x-text="'(' + spec.satuan + ')'"></span>
                                </label>
                                <!-- Select type -->
                                <template x-if="spec.tipe_input === 'select'">
                                    <select x-model="selectedSpecs[index]"
                                            style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: white;">
                                        <option value="">Pilih...</option>
                                        <template x-for="pilihan in spec.pilihan" :key="pilihan">
                                            <option :value="pilihan" x-text="pilihan"></option>
                                        </template>
                                        <template x-for="bahan in spec.bahans" :key="bahan.id">
                                            <option :value="bahan.nama" x-text="bahan.nama + ' (Rp ' + formatNumber(bahan.hpp) + ')'"></option>
                                        </template>
                                    </select>
                                </template>
                                <!-- Number type -->
                                <template x-if="spec.tipe_input === 'number'">
                                    <input type="number" x-model="selectedSpecs[index]"
                                           style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;"
                                           :placeholder="spec.satuan || 'Masukkan angka'">
                                </template>
                                <!-- Text type -->
                                <template x-if="spec.tipe_input === 'text'">
                                    <input type="text" x-model="selectedSpecs[index]"
                                           style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;"
                                           :placeholder="spec.satuan || 'Masukkan teks'">
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Quantity -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 500; color: #6b7280; margin-bottom: 4px;">Jumlah</label>
                    <input type="number" x-model="quantity" min="1" value="1"
                           style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;">
                </div>

                <!-- Order Form -->
                <form :action="'{{ url('/l/' . $linktree->custom_url . '/order') }}/' + productId" method="POST">
                    @csrf
                    <input type="hidden" name="customer_name" :value="customerName">
                    <input type="hidden" name="customer_phone" :value="customerPhone">
                    <input type="hidden" name="customer_email" :value="customerEmail">
                    <input type="hidden" name="quantity" :value="quantity">
                    <input type="hidden" name="notes" :value="notes">
                    <!-- Dynamic selected_specs fields -->
                    <template x-for="(item, index) in specsArray" :key="index">
                        <div>
                            <input type="hidden" :name="'selected_specs[' + index + '][name]'" :value="item.name">
                            <input type="hidden" :name="'selected_specs[' + index + '][value]'" :value="item.value">
                        </div>
                    </template>

                    <!-- Customer Info -->
                    <div style="margin-bottom: 16px;">
                        <h4 style="font-weight: 600; color: #1f2937; font-size: 14px; margin-bottom: 12px;">Informasi Anda:</h4>
                        <input type="text" x-model="customerName" placeholder="Nama Lengkap *" required
                               style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px; margin-bottom: 8px;">
                        <input type="tel" x-model="customerPhone" placeholder="No. WhatsApp *" required
                               style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px; margin-bottom: 8px;">
                        <input type="email" x-model="customerEmail" placeholder="Email (opsional)"
                               style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px; margin-bottom: 8px;">
                        <textarea x-model="notes" placeholder="Catatan (opsional)" rows="2"
                                  style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px; resize: vertical;"></textarea>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            :disabled="!customerName || !customerPhone"
                            :style="(!customerName || !customerPhone) ? 'width:100%;padding:12px;background:#9ca3af;color:white;font-weight:600;border-radius:12px;border:none;font-size:15px;cursor:not-allowed;' : 'width:100%;padding:12px;background:#16a34a;color:white;font-weight:600;border-radius:12px;border:none;cursor:pointer;font-size:15px;transition:background 0.2s;'">
                        <i class="fas fa-shopping-cart" style="margin-right: 8px;"></i> Pesan Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function productModal() {
        return {
            open: false,
            product: null,
            specs: [],
            selectedSpecs: {},
            quantity: 1,
            customerName: @json(auth()->user()->name ?? ''),
            customerPhone: @json(auth()->user()->phone ?? ''),
            customerEmail: @json(auth()->user()->email ?? ''),
            notes: '',
            productId: null,

            get specsArray() {
                return Object.entries(this.selectedSpecs).map(([index, value]) => {
                    const spec = this.specs[parseInt(index)];
                    return { name: spec?.nama || '', value: value };
                }).filter(s => s.value);
            },

            async openProduct(linktreeProductId) {
                try {
                    const response = await fetch(`/l/{{ $linktree->custom_url }}/product/${linktreeProductId}`);
                    const data = await response.json();
                    this.product = {
                        name: data.product.display_name,
                        image: data.product.display_image ? `/produk_gambar/${data.product.display_image}` : null,
                        price: data.product.display_price || '',
                        description: data.product.display_description || ''
                    };
                    this.specs = data.specs || [];
                    this.productId = linktreeProductId;
                    this.selectedSpecs = {};
                    this.quantity = 1;
                    this.notes = '';
                    this.open = true;
                } catch (e) {
                    console.error('Error loading product:', e);
                }
            },

            formatNumber(num) {
                return Number(num).toLocaleString('id-ID');
            }
        }
    }
    </script>
</body>
</html>
