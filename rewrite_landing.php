<?php
$blade = <<<'BLADE'
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logohelix.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $settings['app_name'] ?? 'Helix' }} - Marketplace UMKM Sadawarna</title>
    <meta name="description" content="{{ $settings['seo_description'] ?? 'Platform Jual Beli Produk Lokal Terlengkap se-Indonesia' }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        primary: '#0f766e', // Dark teal
                        secondary: '#f97316', // Orange
                        accent: '#10b981', // Green (Emerald)
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
        
        .hero-bg {
            background-color: #f9fbfc; /* Very light neutral/blueish tint */
        }
    </style>
</head>
<body class="text-slate-800 antialiased selection:bg-emerald-500 selection:text-white overflow-x-hidden hero-bg"
      x-data="landingPageData()"
      x-init="initPage()">

    {{-- POPUP PROMO --}}
    @if(($settings['enable_welcome_popup'] ?? '0') == '1' && !empty($settings['popup_image']))
    <div x-show="showPopup" x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
         x-transition.opacity.duration.300ms>
        <div class="relative w-full max-w-sm mx-auto" @click.outside="closePopup()">
            <button @click="closePopup()" class="absolute -top-10 right-0 w-8 h-8 bg-white text-slate-800 rounded-full flex items-center justify-center text-lg hover:bg-slate-200 transition-colors shadow">
                <i class="fas fa-times"></i>
            </button>
            <a href="{{ $settings['popup_link'] ?? '#' }}" class="block w-full rounded-2xl overflow-hidden shadow-2xl hover:scale-[1.02] transition-transform">
                <img src="{{ asset('storage/' . $settings['popup_image']) }}" class="w-full h-auto object-cover" alt="Promo">
            </a>
        </div>
    </div>
    @endif

    @include('partials.navbar')

    {{-- HERO SECTION: SPLIT LAYOUT --}}
    <section class="relative pt-32 pb-16 overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                {{-- Left: Content --}}
                <div class="max-w-xl">
                    <h1 class="text-4xl md:text-5xl lg:text-[3.5rem] font-black text-slate-900 tracking-tight leading-none mb-2">
                        Temukan Produk
                    </h1>
                    <h2 class="text-4xl md:text-5xl lg:text-[3.5rem] font-black text-teal-600 tracking-tight leading-none mb-2">
                        UMKM Terbaik
                    </h2>
                    <h3 class="text-4xl md:text-5xl lg:text-[3.5rem] font-black text-orange-500 tracking-tight leading-none mb-6">
                        Indonesia
                    </h3>
                    <p class="text-slate-500 text-sm md:text-base font-medium mb-10 leading-relaxed max-w-md">
                        {{ $settings['hero_subtitle'] ?? 'Mendukung pengrajin lokal dan usaha kecil dengan koleksi kurasi premium dari seluruh nusantara.' }}
                    </p>

                    {{-- Search Bar (Pill Shape) --}}
                    <form action="{{ route('search') ?? url('/search') }}" method="GET" class="w-full flex items-center bg-white border border-slate-200 rounded-full p-1.5 shadow-sm hover:shadow-md focus-within:border-teal-500 focus-within:ring-2 focus-within:ring-teal-100 transition-all max-w-md">
                        <div class="pl-4 pr-2 text-slate-400">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" name="query" placeholder="Cari batik, kopi, kerajinan..." class="w-full py-2.5 px-2 text-sm md:text-base font-medium text-slate-800 outline-none bg-transparent placeholder:text-slate-400">
                        <button type="submit" class="bg-teal-700 hover:bg-teal-800 text-white font-bold py-2.5 px-8 rounded-full transition-colors shrink-0">
                            Cari
                        </button>
                    </form>
                </div>

                {{-- Right: Image/Slider inside Soft Green Container --}}
                <div class="relative w-full h-[300px] sm:h-[400px] lg:h-[500px]">
                    <div class="absolute inset-0 bg-emerald-50 rounded-[2.5rem] sm:rounded-[3rem] p-4 sm:p-8 flex items-center justify-center overflow-hidden">
                        @php
                            $validBanners = [];
                            if(!empty($settings['hero_image']) && file_exists(public_path('storage/' . $settings['hero_image']))) {
                                $validBanners[] = ['img' => asset('storage/' . $settings['hero_image'])];
                            }
                            for($i = 1; $i <= 4; $i++) {
                                $imgK = 'hero_image_' . $i;
                                if(!empty($settings[$imgK]) && file_exists(public_path('storage/' . $settings[$imgK]))) {
                                    if(count($validBanners) < 4) $validBanners[] = ['img' => asset('storage/' . $settings[$imgK])];
                                }
                            }
                        @endphp
                        
                        <div id="hero-slider" class="w-full h-full flex transition-transform duration-700 ease-[cubic-bezier(0.25, 1, 0.5, 1)]">
                            @forelse($validBanners as $banner)
                                <div class="min-w-full h-full relative flex-shrink-0 flex items-center justify-center">
                                    <img src="{{ $banner['img'] }}" class="w-full h-auto max-h-full object-contain rounded-xl shadow-sm" alt="Banner" onerror="this.style.display='none'">
                                </div>
                            @empty
                                <div class="min-w-full h-full flex flex-col items-center justify-center text-emerald-300">
                                    <i class="fas fa-image text-6xl mb-4 opacity-50"></i>
                                    <span class="font-medium text-sm opacity-50">Gambar Promo (Kosong)</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <main class="container mx-auto px-4 sm:px-6 py-8 lg:py-12 space-y-16 lg:space-y-24 relative z-20">

        {{-- 2. KATEGORI (4 LARGE CARDS) --}}
        <section class="w-full">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 max-w-5xl mx-auto mb-8">
                @php
                    // Limit categories to 4 for this specific design layout
                    $displayCategories = array_slice($categories->toArray() ?? [], 0, 4);
                @endphp
                @forelse($displayCategories as $index => $catObj)
                    @php $cat = (object)$catObj; @endphp
                    <a href="{{ url('pages/produk?kategori=' . $cat->id) }}" class="bg-white py-8 px-4 rounded-3xl shadow-sm border border-slate-100 flex flex-col items-center justify-center gap-4 hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-xl">
                            <i class="{{ $cat->icon_class ?? 'fas fa-box' }}"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-800">{{ $cat->nama_kategori }}</span>
                    </a>
                @empty
                    <!-- Fallback hardcoded if no DB cats -->
                    <a href="#" class="bg-white py-8 px-4 rounded-3xl shadow-sm border border-slate-100 flex flex-col items-center justify-center gap-4 hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-xl"><i class="fas fa-tshirt"></i></div>
                        <span class="text-sm font-bold text-slate-800">Pakaian</span>
                    </a>
                    <a href="#" class="bg-white py-8 px-4 rounded-3xl shadow-sm border border-slate-100 flex flex-col items-center justify-center gap-4 hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center text-xl"><i class="fas fa-utensils"></i></div>
                        <span class="text-sm font-bold text-slate-800">Kuliner</span>
                    </a>
                    <a href="#" class="bg-white py-8 px-4 rounded-3xl shadow-sm border border-slate-100 flex flex-col items-center justify-center gap-4 hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xl"><i class="fas fa-toolbox"></i></div>
                        <span class="text-sm font-bold text-slate-800">Kerajinan</span>
                    </a>
                    <a href="#" class="bg-white py-8 px-4 rounded-3xl shadow-sm border border-slate-100 flex flex-col items-center justify-center gap-4 hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl"><i class="fas fa-leaf"></i></div>
                        <span class="text-sm font-bold text-slate-800">Kecantikan</span>
                    </a>
                @endforelse
            </div>

            {{-- Feature Badges --}}
            <div class="flex flex-wrap items-center justify-center gap-6 md:gap-12 text-sm font-medium text-slate-600">
                <div class="flex items-center gap-2"><i class="fas fa-leaf text-teal-600"></i> Organic</div>
                <div class="flex items-center gap-2"><i class="fas fa-shield-alt text-teal-600"></i> Good Quality</div>
                <div class="flex items-center gap-2"><i class="fas fa-truck text-teal-600"></i> Fast Delivery</div>
            </div>
        </section>


        {{-- 3. MITRA UNGGULAN (3 HORIZONTAL CARDS) --}}
        @if(($settings['show_top_stores'] ?? '1') == '1' && isset($listToko) && count($listToko) > 0)
        <section class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg md:text-xl font-black text-slate-900 tracking-tight">Mitra Unggulan</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                @foreach(array_slice($listToko->toArray(), 0, 3) as $index => $tokoObj)
                    @php $toko = (object)$tokoObj; @endphp
                    <a href="{{ route('toko.detail', $toko->slug ?? '') }}" class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 p-1 shrink-0 flex items-center justify-center">
                            @if(!empty($toko->logo_toko))
                                <img src="{{ asset('assets/uploads/logos/' . $toko->logo_toko) }}" class="w-full h-full rounded-full object-cover" onerror="this.src='{{ asset('logohelix.png') }}'">
                            @else
                                <i class="fas fa-store text-slate-400"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-sm text-slate-900 truncate group-hover:text-teal-700 transition-colors">
                                {{ $toko->nama_toko }}
                            </h4>
                            <p class="text-xs text-slate-500 truncate font-medium mt-0.5">
                                <i class="fas fa-map-marker-alt text-slate-400 mr-1"></i> {{ $toko->kota ?? 'Indonesia' }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- 4. CUSTOMER SERVICE BANNER --}}
        <section class="w-full max-w-6xl mx-auto">
            <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 md:p-10 flex flex-col md:flex-row items-center justify-between shadow-xl gap-6 md:gap-10">
                <div class="flex items-center gap-4 sm:gap-6">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-slate-800 flex items-center justify-center text-white text-xl sm:text-2xl shrink-0 border border-slate-700">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl font-bold text-white mb-1 md:mb-2">Butuh Bantuan Order?</h3>
                        <p class="text-slate-400 text-sm md:text-base">Tim CS kami siap membantu Anda 24/7.</p>
                    </div>
                </div>
                <a href="#" class="shrink-0 w-full md:w-auto bg-white text-slate-900 font-bold text-sm px-6 py-3.5 rounded-xl text-center hover:bg-slate-100 transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fab fa-whatsapp text-emerald-500 text-lg"></i> Hubungi Kami di WA
                </a>
            </div>
        </section>

        {{-- 5. REKOMENDASI UNTUK ANDA --}}
        @if(isset($listProdukLokal) && count($listProdukLokal) > 0)
        <section class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg md:text-xl font-black text-slate-900 tracking-tight">Rekomendasi Untuk Anda</h3>
                <a href="{{ url('pages/produk') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700 transition-colors">Lihat Semua</a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                @foreach(array_slice($listProdukLokal->toArray(), 0, 4) as $index => $pObj)
                    @php 
                        $p = (object)$pObj; 
                        // Simulate random tags based on image reference for demo aesthetic
                        $tags = ['Terlaris', 'UMKM Asli', 'Promo', 'Baru'];
                        $tag = $tags[$index % count($tags)];
                    @endphp
                    <a href="{{ route('produk.detail', $p->slug ?? '') }}" class="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-teal-100 transition-all block group relative">
                        {{-- Tag Badge --}}
                        <div class="absolute top-5 right-5 z-10 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">
                            {{ $tag }}
                        </div>
                        
                        <div class="aspect-square bg-slate-50 rounded-xl mb-4 overflow-hidden relative">
                            <img src="{{ asset('assets/uploads/products/'.($p->gambar_utama ?? 'default.jpg')) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        
                        <div class="px-1">
                            <div class="flex items-center gap-1.5 mb-1.5 text-[10px] text-slate-500 font-medium">
                                <i class="fas fa-map-marker-alt text-slate-400"></i>
                                <span class="truncate">{{ $p->nama_kota_toko ?? 'Indonesia' }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 line-clamp-1 mb-2 group-hover:text-teal-700 transition-colors">{{ $p->nama_barang }}</h4>
                            <div class="font-black text-emerald-600 text-sm">Rp {{ number_format($p->harga ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
        @endif

    </main>

    @include('partials.footer')
    <script src="{{ asset('assets/js/navbar.js') }}"></script>
    
    <script>
        // Alpine Data
        function landingPageData() {
            return {
                showPopup: false,
                initPage() {
                    const popupEnabled = "{{ $settings['enable_welcome_popup'] ?? '0' }}" === "1";
                    if(popupEnabled) {
                        const lastSeen = localStorage.getItem('popup_last_seen');
                        const today = new Date().toDateString();
                        if (lastSeen !== today) {
                            this.showPopup = true;
                            localStorage.setItem('popup_last_seen', today);
                        }
                    }
                    setTimeout(() => initSlider(), 100);
                },
                closePopup() { this.showPopup = false; }
            }
        }

        // Simple Slider Logic
        const slider = document.getElementById('hero-slider');
        let currentSlide = 0;
        let totalSlides = slider ? slider.children.length : 0;
        let slideInterval;

        function initSlider() {
            if(totalSlides <= 1) return;
            startSlideShow();
        }

        function goToSlide(index) {
            if(!slider) return;
            currentSlide = index;
            slider.style.transform = `translateX(-${currentSlide * 100}%)`;
            resetSlideShow();
        }

        function moveSlider(direction) {
            currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
            goToSlide(currentSlide);
        }

        function startSlideShow() { slideInterval = setInterval(() => moveSlider(1), 5000); }
        function resetSlideShow() { clearInterval(slideInterval); startSlideShow(); }
    </script>
</body>
</html>
BLADE;

file_put_contents('c:\laragon\www\helix\resources\views\landing_new.blade.php', $blade);
copy('c:\laragon\www\helix\resources\views\landing_new.blade.php', 'c:\laragon\www\helix\resources\views\landing.blade.php');
echo "landing page successfully rewritten!";
