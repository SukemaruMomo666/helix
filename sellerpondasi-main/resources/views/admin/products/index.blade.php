@extends('layouts.admin')

@section('title', 'Pusat Moderasi Material')

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM PRODUCT MODERATION CSS     == */
    /* ========================================= */

    /* Animasi Mengambang Halus */
    .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease, border-color 0.3s ease; }
    .hover-lift:hover { transform: translateY(-6px); }

    /* Custom Scrollbar untuk Filter */
    .filter-wrapper::-webkit-scrollbar { height: 4px; }
    .filter-wrapper::-webkit-scrollbar-track { background: transparent; }
    .filter-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .filter-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .dark .filter-wrapper::-webkit-scrollbar-thumb { background: #475569; }
    .dark .filter-wrapper::-webkit-scrollbar-thumb:hover { background: #64748b; }

    /* Line Clamp Fallback */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */

    /* Base Colors & Opacities */
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-800\/80 { background-color: rgba(30, 41, 59, 0.8) !important; }
    .dark .dark\:bg-slate-800\/60 { background-color: rgba(30, 41, 59, 0.6) !important; }
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:bg-slate-800\/40 { background-color: rgba(30, 41, 59, 0.4) !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }
    .dark .dark\:bg-transparent { background-color: transparent !important; }

    .dark .dark\:border-slate-800 { border-color: #1e293b !important; }
    .dark .dark\:border-slate-800\/80 { border-color: rgba(30, 41, 59, 0.8) !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:border-slate-700\/50 { border-color: rgba(51, 65, 85, 0.5) !important; }
    .dark .dark\:border-slate-600 { border-color: #475569 !important; }

    /* Form Inputs Overrides */
    .dark .form-control { background-color: #1e293b !important; border-color: #334155 !important; color: #f8fafc !important; }
    .dark .form-control:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important; }
    .dark .form-control::placeholder { color: #64748b !important; }

    /* Pagination Overrides */
    .dark .pagination .page-link { background-color: #1e293b !important; border-color: #334155 !important; color: #cbd5e1 !important; }
    .dark .pagination .page-item.active .page-link { background-color: #3b82f6 !important; border-color: #3b82f6 !important; color: white !important; }
    .dark .pagination .page-item.disabled .page-link { background-color: #0f172a !important; color: #475569 !important; border-color: #1e293b !important; }

    /* Status Badges (Amber, Emerald, Rose) */
    .dark .dark\:bg-amber-500\/10 { background-color: rgba(245, 158, 11, 0.1) !important; }
    .dark .dark\:bg-amber-500\/90 { background-color: rgba(245, 158, 11, 0.9) !important; }

    .dark .dark\:bg-emerald-500\/90 { background-color: rgba(16, 185, 129, 0.9) !important; }

    .dark .dark\:bg-rose-500\/90 { background-color: rgba(244, 63, 94, 0.9) !important; }

    .dark .dark\:bg-blue-500\/10 { background-color: rgba(59, 130, 246, 0.1) !important; }

    /* Text Specifics */
    .dark .dark\:text-white { color: #ffffff !important; }

    /* Hover States & Custom Shadows */
    .dark .dark\:hover\:bg-slate-700:hover { background-color: #334155 !important; }
    .dark .dark\:hover\:bg-blue-600:hover { background-color: #2563eb !important; }
    .dark .dark\:hover\:border-blue-500:hover { border-color: #3b82f6 !important; }
    .dark .dark\:hover\:border-blue-500\/50:hover { border-color: rgba(59, 130, 246, 0.5) !important; }
    .dark .dark\:hover\:shadow-\[0_10px_30px_-10px_rgba\(59\,130\,246\,0\.15\)\]:hover { box-shadow: 0 10px 30px -10px rgba(59, 130, 246, 0.15) !important; }
</style>
@endpush

@section('content')

{{-- HEADER HALAMAN & STATISTIK CEPAT --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-1 transition-colors duration-300 flex items-center gap-3">
            Pusat Moderasi
        </h2>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 transition-colors duration-300">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors text-decoration-none">Dashboard</a>
            <i class="mdi mdi-chevron-right text-sm"></i>
            <span class="text-blue-600 dark:text-blue-400">Moderasi Material</span>
        </div>
        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-2 m-0 max-w-xl leading-relaxed">
            Tinjau kelayakan material dan produk yang diunggah oleh mitra toko sebelum ditayangkan ke publik untuk memastikan standar kualitas platform.
        </p>
    </div>

    {{-- KPI Card Mini --}}
    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/50 p-4 rounded-2xl flex items-center gap-4 shadow-sm transition-colors duration-300 w-full md:w-auto">
        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-500 flex items-center justify-center text-2xl flex-shrink-0 relative overflow-hidden">
            <div class="absolute inset-0 bg-amber-400 opacity-20 animate-pulse"></div>
            <i class="mdi mdi-timer-sand relative z-10"></i>
        </div>
        <div class="pr-4">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Antrean Tinjauan</div>
            <div class="text-2xl font-black text-slate-800 dark:text-white leading-none">{{ number_format($stats['pending'] ?? 0) }} <span class="text-sm font-bold text-slate-500 dark:text-slate-400">Produk</span></div>
        </div>
    </div>
</div>

{{-- AREA FILTER & PENCARIAN --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden mb-8 transition-colors duration-300">
    <div class="p-5 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">

        {{-- Tabs Filter Status --}}
        <div class="flex p-1 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700/50 w-full xl:w-auto overflow-x-auto filter-wrapper shadow-inner dark:shadow-none">
            @php
                $statusTabs = [
                    'semua' => ['icon' => 'mdi-layers-outline', 'label' => 'Semua'],
                    'pending' => ['icon' => 'mdi-clock-outline', 'label' => 'Perlu Tinjauan'],
                    'approved' => ['icon' => 'mdi-check-decagram', 'label' => 'Disetujui'],
                    'rejected' => ['icon' => 'mdi-cancel', 'label' => 'Ditolak']
                ];
            @endphp

            @foreach($statusTabs as $key => $data)
                <a href="{{ route('admin.products.index', ['status' => $key, 'search' => $search]) }}"
                   class="flex items-center gap-2 px-4 py-2 text-xs font-black capitalize rounded-lg transition-all text-decoration-none outline-none whitespace-nowrap {{ $status_filter == $key ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-white shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <i class="mdi {{ $data['icon'] }} text-base leading-none"></i> {{ $data['label'] }}
                </a>
            @endforeach
        </div>

        {{-- Form Pencarian --}}
        <form action="{{ route('admin.products.index') }}" method="GET" class="relative w-full xl:w-96 m-0">
            <input type="hidden" name="status" value="{{ $status_filter }}">
            <i class="mdi mdi-magnify absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-lg"></i>
            <input type="text" name="search" value="{{ $search }}"
                   class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-inner dark:shadow-none"
                   placeholder="Cari nama material, merek, atau toko...">
        </form>
    </div>
</div>

{{-- PRODUCT GRID --}}
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6 mb-8">
    @forelse($products as $prod)
        <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 rounded-2xl flex flex-col overflow-hidden shadow-sm hover-lift group transition-all duration-300 hover:border-blue-300 dark:hover:border-blue-500/50 dark:hover:shadow-[0_10px_30px_-10px_rgba(59,130,246,0.15)] relative">

            {{-- Image Container --}}
            <div class="w-full aspect-[4/3] bg-slate-100 dark:bg-slate-900 relative overflow-hidden flex-shrink-0">
                <img src="{{ asset('assets/uploads/products/' . ($prod->gambar_utama ?? 'default.jpg')) }}" alt="{{ $prod->nama_barang }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                {{-- Status Badge (Absolute) --}}
                @php
                    $badgeClass = '';
                    $iconClass = '';
                    $st = strtolower($prod->status_moderasi);
                    if($st == 'pending') { $badgeClass = 'bg-amber-100 text-amber-700 dark:bg-amber-500/90 dark:text-white shadow-lg'; $iconClass = 'mdi-clock-outline'; }
                    elseif($st == 'approved') { $badgeClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/90 dark:text-white shadow-lg'; $iconClass = 'mdi-check-decagram'; }
                    elseif($st == 'rejected') { $badgeClass = 'bg-rose-100 text-rose-700 dark:bg-rose-500/90 dark:text-white shadow-lg'; $iconClass = 'mdi-cancel'; }
                    else { $badgeClass = 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-white'; $iconClass = 'mdi-help-circle'; }
                @endphp
                <div class="absolute top-3 left-3 flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider backdrop-blur-sm {{ $badgeClass }}">
                    <i class="mdi {{ $iconClass }} text-sm"></i> {{ $prod->status_moderasi }}
                </div>
            </div>

            {{-- Content Container --}}
            <div class="p-4 flex flex-col flex-1">
                {{-- Category & Price --}}
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest bg-blue-50 dark:bg-blue-500/10 px-2 py-0.5 rounded">{{ $prod->nama_kategori ?? 'UMUM' }}</span>
                </div>

                {{-- Name --}}
                <h5 class="text-sm font-black text-slate-800 dark:text-white leading-snug mb-3 line-clamp-2 min-h-[2.8rem] transition-colors duration-300" title="{{ $prod->nama_barang }}">
                    {{ $prod->nama_barang }}
                </h5>

                <div class="flex justify-between items-center mb-4 mt-auto">
                    <div class="text-base font-black text-orange-500 dark:text-orange-400">Rp {{ number_format($prod->harga, 0, ',', '.') }}</div>
                    <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1"><i class="mdi mdi-layers-outline"></i> Stok: {{ $prod->stok }}</div>
                </div>

                {{-- Action Button --}}
                <a href="{{ route('admin.products.show', $prod->id) }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800/80 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 text-slate-700 dark:text-slate-300 text-xs font-black rounded-xl transition-all text-decoration-none outline-none group/btn mb-4 border border-slate-200 dark:border-slate-700 hover:border-blue-600 dark:hover:border-blue-500">
                    <i class="mdi mdi-file-search-outline text-lg group-hover/btn:scale-110 transition-transform"></i> Tinjau Detail
                </a>

                {{-- Footer Store Info --}}
                <div class="pt-3 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between mt-auto">
                    <div class="flex items-center gap-2 overflow-hidden pr-2">
                        <div class="w-6 h-6 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center flex-shrink-0 text-xs">
                            <i class="mdi mdi-storefront-outline"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400 truncate">{{ $prod->nama_toko }}</span>
                    </div>
                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 flex-shrink-0">
                        {{ date('d/m/y', strtotime($prod->created_at)) }}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-1 sm:col-span-2 md:col-span-3 xl:col-span-4 2xl:col-span-5 flex flex-col items-center justify-center py-24 bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 rounded-3xl shadow-sm transition-colors duration-300">
            <div class="w-24 h-24 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center mb-5 shadow-inner">
                <i class="mdi mdi-cube-off-outline text-5xl text-slate-300 dark:text-slate-600"></i>
            </div>
            <h5 class="text-lg font-black text-slate-700 dark:text-slate-200 mb-1">Tidak ada produk ditemukan.</h5>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 m-0">Coba ubah status filter atau kata kunci pencarian Anda.</p>
        </div>
    @endforelse
</div>

{{-- PAGINATION --}}
<div class="px-6 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col md:flex-row justify-between items-center gap-4 transition-colors duration-300 shadow-sm">
    <div class="text-xs font-bold text-slate-500 dark:text-slate-400">
        Menampilkan <span class="text-blue-600 dark:text-blue-400 font-black">{{ $products->firstItem() ?? 0 }}</span> - <span class="text-blue-600 dark:text-blue-400 font-black">{{ $products->lastItem() ?? 0 }}</span> dari <span class="text-slate-800 dark:text-white font-black">{{ $products->total() }}</span> material
    </div>
    <div class="pagination-wrapper">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
