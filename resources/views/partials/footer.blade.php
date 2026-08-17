{{-- ========================================================
     PREMIUM B&W FOOTER (TAILWIND CSS) - APP EDITION
     ======================================================== --}}
<footer class="bg-[#050505] text-zinc-400 pt-20 pb-28 md:pb-10 border-t-2 border-zinc-800 relative overflow-hidden font-sans">

    {{-- Subtle Blue Glow Background (Opsional, agar tidak terlalu gelap) --}}
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/5 rounded-full filter blur-[100px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">

        {{-- MAIN CONTENT GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 mb-20">

            {{-- ================= KOLOM KIRI: LOGOS (5 Cols) ================= --}}
            <div class="lg:col-span-5 flex flex-col gap-6 items-center sm:items-start text-center sm:text-left">
                <img src="{{ asset('assets/logos/Logo Helix.png') }}" alt="Helix Logo" class="h-14 w-auto drop-shadow-lg">
                <p class="text-sm leading-relaxed text-zinc-400 max-w-sm">
                    Platform Pusat Penjualan UMKM dan Pemberdayaan Desa Sadawarna.
                </p>

                <div class="mt-4">
                    <h5 class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-3">Didukung Oleh:</h5>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 bg-white/5 p-4 rounded-2xl border border-zinc-800">
                        <img src="{{ asset('assets/logos/BIMA.png') }}" alt="BIMA" class="h-10 object-contain">
                        <img src="{{ asset('assets/logos/DIKTISAITEK.png') }}" alt="DIKTISAITEK" class="h-10 object-contain">
                        <img src="{{ asset('assets/logos/polsub.png') }}" alt="Polsub" class="h-10 object-contain">
                        <img src="{{ asset('assets/logos/TUT WURI.png') }}" alt="Tut Wuri" class="h-10 object-contain">
                    </div>
                </div>
            </div>

            {{-- ================= KOLOM KANAN: LINKS (7 Cols) ================= --}}
            <div class="lg:col-span-7 grid grid-cols-2 md:grid-cols-3 gap-8 lg:pl-10">

                {{-- Link Grup 1 --}}
                <div>
                    <h4 class="text-white font-black mb-6 uppercase tracking-widest text-xs">Jelajahi</h4>
                    <ul class="space-y-4 text-sm font-medium text-zinc-400">
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Beranda</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Kategori Material</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Daftar Mitra Toko</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Promo Proyek</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Helix Blog</a></li>
                    </ul>
                </div>

                {{-- Link Grup 2 --}}
                <div>
                    <h4 class="text-white font-black mb-6 uppercase tracking-widest text-xs">Layanan</h4>
                    <ul class="space-y-4 text-sm font-medium text-zinc-400">
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Cara Pembayaran</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Lacak Pengiriman</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Kebijakan Retur 7 Hari</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Helix B2B</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">FAQ</a></li>
                    </ul>
                </div>

                {{-- Link Grup 3 (Hubungi Kami) --}}
                <div class="col-span-2 md:col-span-1">
                    <h4 class="text-white font-black mb-6 uppercase tracking-widest text-xs">Hubungi Kami</h4>
                    <ul class="space-y-4 text-sm font-medium text-zinc-400">
                        <li>
                            <a href="mailto:cs@helix.com" class="hover:text-blue-400 transition-colors flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-white"><i class="fas fa-envelope text-xs"></i></div>
                                cs@helix.com
                            </a>
                        </li>
                        <li>
                            <a href="tel:0211500768" class="hover:text-blue-400 transition-colors flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-white"><i class="fas fa-phone-alt text-xs"></i></div>
                                (021) 1500-POTA
                            </a>
                        </li>
                        <li class="flex items-start gap-3 mt-4 text-xs leading-relaxed text-zinc-500">
                            <i class="fas fa-building mt-1 text-zinc-600"></i>
                            Helix<br>
                            Bendungan Sadawarna<br>
                            Desa Cibogo, Kab. Subang
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        {{-- ================= MIDDLE SECTION: SOCIAL & PAYMENTS ================= --}}
        <div class="border-t border-zinc-800/80 pt-8 pb-8 flex flex-col md:flex-row items-center justify-between gap-8">

            {{-- Social Media (Minimalist B&W) --}}
            <div class="flex flex-wrap justify-center gap-3">
                @foreach(['facebook-f', 'instagram', 'twitter', 'youtube', 'tiktok'] as $icon)
                    <a href="#" class="w-10 h-10 rounded-full bg-white text-black hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all duration-300 shadow-md">
                        <i class="fab fa-{{ $icon }} text-lg"></i>
                    </a>
                @endforeach
            </div>

            {{-- Language Toggle Removed --}}
            <div class="flex items-center gap-6 hidden"></div>

            {{-- Payment Icons --}}
            <div class="flex flex-wrap justify-center gap-3">
                <div class="h-8 px-3 bg-zinc-900 border border-zinc-800 rounded flex items-center justify-center text-zinc-500 hover:text-white transition-colors font-bold text-[10px] tracking-wider">QRIS</div>
            </div>

        </div>

        {{-- ================= BOTTOM SECTION: COPYRIGHT ================= --}}
        <div class="border-t border-zinc-800/80 pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-zinc-600">
            <p>&copy; {{ date('Y') }} Helix. Hak Cipta Dilindungi.</p>
            <div class="flex gap-4">
                <a href="#" class="hover:text-zinc-300 transition-colors">Kebijakan Privasi</a>
                <a href="#" class="hover:text-zinc-300 transition-colors">Syarat & Ketentuan</a>
            </div>
        </div>

    </div>

</footer>
