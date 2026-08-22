<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vendor->name }} - Profil Vendor | {{ config('app.name', 'Grafika Printing') }}</title>
    <meta name="description" content="Profil vendor {{ $vendor->name }} - {{ $vendor->address ?? 'Grafika Printing' }}">
    @if($vendor->logo)
    <meta property="og:title" content="{{ $vendor->name }} - Grafika Printing">
    <meta property="og:image" content="{{ asset('vendors_logo/' . $vendor->logo) }}">
    @endif
    <meta property="og:type" content="profile">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .rating-star {
            color: #fbbf24;
        }
    </style>
</head>
<body class="py-8 px-4">
    <div class="max-w-lg mx-auto">
        {{-- Profile Card --}}
        <div class="glass-card rounded-2xl shadow-2xl overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-center">
                @if($vendor->logo)
                    <img src="{{ asset('vendors_logo/' . $vendor->logo) }}"
                         alt="{{ $vendor->name }}"
                         class="w-24 h-24 rounded-full mx-auto border-4 border-white shadow-lg object-cover">
                @else
                    <div class="w-24 h-24 rounded-full mx-auto border-4 border-white shadow-lg bg-white/20 flex items-center justify-center">
                        <span class="text-3xl font-bold text-white">{{ strtoupper(substr($vendor->name, 0, 2)) }}</span>
                    </div>
                @endif
                <h1 class="text-2xl font-bold text-white mt-4">{{ $vendor->name }}</h1>
                @if($vendor->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span> Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-2">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span> Tidak Aktif
                    </span>
                @endif
            </div>

            {{-- Rating --}}
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-center gap-2">
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($vendor->average_rating))
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @endif
                        @endfor
                    </div>
                    <span class="text-sm text-gray-600 font-medium">
                        {{ number_format($vendor->average_rating, 1) }}
                        <span class="text-gray-400">({{ $vendor->rating_count }} ulasan)</span>
                    </span>
                </div>
            </div>

            {{-- Info --}}
            <div class="px-6 py-4 space-y-3">
                @if($vendor->address)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Alamat</p>
                        <p class="text-sm text-gray-700">{{ $vendor->address }}</p>
                    </div>
                </div>
                @endif

                @if($vendor->email)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Email</p>
                        <a href="mailto:{{ $vendor->email }}" class="text-sm text-indigo-600 hover:underline">{{ $vendor->email }}</a>
                    </div>
                </div>
                @endif

                @if($vendor->phone)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Telepon</p>
                        <a href="tel:{{ $vendor->phone }}" class="text-sm text-indigo-600 hover:underline">{{ $vendor->phone }}</a>
                    </div>
                </div>
                @endif

                @if($vendor->website)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Website</p>
                        <a href="{{ $vendor->website }}" target="_blank" rel="noopener noreferrer" class="text-sm text-indigo-600 hover:underline">{{ $vendor->website }}</a>
                    </div>
                </div>
                @endif
            </div>

            {{-- Products Preview --}}
            @php
                $produks = $vendor->produk()->limit(6)->get();
            @endphp
            @if($produks->count())
            <div class="px-6 py-4 border-t border-gray-100">
                <h2 class="text-sm font-semibold text-gray-800 mb-3 uppercase tracking-wide">Produk</h2>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($produks as $produk)
                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                        @if($produk->gambar)
                            <img src="{{ asset('produk_gambar/' . $produk->gambar) }}"
                                 alt="{{ $produk->nama_produk }}"
                                 class="w-full h-20 object-cover rounded-md mb-1">
                        @else
                            <div class="w-full h-20 bg-gray-200 rounded-md mb-1 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <p class="text-xs text-gray-700 font-medium truncate">{{ $produk->nama_produk }}</p>
                        <p class="text-xs text-indigo-600 font-semibold">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Actions --}}
            <div class="px-6 py-4 border-t border-gray-100 space-y-3">
                @if($vendor->getActiveLinktreeCached())
                <a href="{{ url('/l/' . $vendor->getActiveLinktreeCached()->custom_url) }}"
                   class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Kunjungi Linktree
                </a>
                @endif

                @if($vendor->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $vendor->phone) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition-all shadow-md">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Hubungi via WhatsApp
                </a>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-6 py-3 bg-gray-50 text-center">
                <p class="text-xs text-gray-400">
                    Powered by <a href="{{ url('/') }}" class="text-indigo-500 hover:underline">Grafika Printing</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
