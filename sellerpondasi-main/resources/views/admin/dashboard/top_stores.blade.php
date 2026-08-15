@extends('layouts.admin')

@section('title', 'Peringkat Performa Toko')

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM TABLE CSS (TRUE BLACK)     == */
    /* ========================================= */
    .table-container::-webkit-scrollbar { height: 6px; }
    .table-container::-webkit-scrollbar-track { background: transparent; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    .dark .table-container::-webkit-scrollbar-thumb { background: #475569; }
    .dark .table-container::-webkit-scrollbar-thumb:hover { background: #64748b; }

    .dark .text-glow-emerald { text-shadow: 0 0 20px rgba(16, 185, 129, 0.6) !important; }

    /* Form Customizations */
    .form-control-custom { 
        width: 100%; 
        border-radius: 0.75rem; 
        font-size: 0.875rem; 
        font-weight: 700; 
        transition: all 0.2s; 
        outline: none; 
        border: 1px solid #e2e8f0; 
    }
    
    /* Fokus input ditarik ke depan agar border biru full mengelilingi */
    .form-control-custom:focus { 
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); 
        border-color: #3b82f6; 
        z-index: 10; 
        position: relative; 
    }

    /* PERBAIKAN BUG: Search Input Group */
    .input-group-text { 
        display: flex;
        align-items: center;
        padding: 0.625rem 1rem; 
        border-radius: 0.75rem 0 0 0.75rem; 
        border: 1px solid #e2e8f0; 
        border-right: none; /* Hilangkan border kanan icon */
    }
    
    /* Menghilangkan lengkungan (radius) kiri pada input agar menyatu dengan icon */
    .input-group-text + .form-control-custom,
    .input-group .form-control-custom { 
        border-top-left-radius: 0 !important; 
        border-bottom-left-radius: 0 !important; 
    }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */
    
    /* Layout & Containers */
    .dark .dark\:bg-slate-950 { background-color: #020617 !important; }
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-900\/80 { background-color: rgba(15, 23, 42, 0.8) !important; }
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:bg-slate-800\/30 { background-color: rgba(30, 41, 59, 0.3) !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }
    .dark .dark\:bg-transparent { background-color: transparent !important; }

    /* Borders */
    .dark .dark\:border-slate-800 { border-color: #1e293b !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:border-slate-600 { border-color: #475569 !important; }

    /* Form Overrides Dark Mode */
    .dark .form-control-custom { background-color: #0f172a !important; border-color: #334155 !important; color: #f8fafc !important; }
    .dark .form-control-custom:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important; }
    
    /* Samakan warna background icon pencarian dengan background input di dark mode */
    .dark .input-group-text { background-color: #0f172a !important; border-color: #334155 !important; color: #94a3b8 !important; border-right: none !important; }
    .dark .bg-slate-50.input-group-text { background-color: rgba(30, 41, 59, 0.5) !important; } /* Fallback jika form-nya pakai /50 opacity */

    /* Text Colors */
    .dark .dark\:text-white { color: #ffffff !important; }
    .dark .dark\:text-slate-200 { color: #e2e8f0 !important; }
    .dark .dark\:text-slate-300 { color: #cbd5e1 !important; }
    .dark .dark\:text-slate-400 { color: #94a3b8 !important; }
    .dark .dark\:text-slate-500 { color: #64748b !important; }
    .dark .dark\:text-slate-600 { color: #475569 !important; }

    /* Badges & Accents (Emerald, Purple, Amber) */
    .dark .dark\:bg-emerald-500\/10 { background-color: rgba(16, 185, 129, 0.1) !important; }
    .dark .dark\:text-emerald-400 { color: #34d399 !important; }
    .dark .dark\:border-emerald-500\/30 { border-color: rgba(16, 185, 129, 0.3) !important; }

    .dark .dark\:bg-purple-500\/10 { background-color: rgba(168, 85, 247, 0.1) !important; }
    .dark .dark\:text-purple-400 { color: #c084fc !important; }
    .dark .dark\:border-purple-500\/30 { border-color: rgba(168, 85, 247, 0.3) !important; }

    .dark .dark\:bg-amber-500\/10 { background-color: rgba(245, 158, 11, 0.1) !important; }
    .dark .dark\:text-amber-400 { color: #fbbf24 !important; }
    .dark .dark\:border-amber-500\/20 { border-color: rgba(245, 158, 11, 0.2) !important; }

    /* Hover States & Interactions */
    .dark .dark\:hover\:bg-blue-900\/30:hover { background-color: rgba(30, 58, 138, 0.3) !important; }
    .dark .dark\:hover\:text-blue-400:hover { color: #60a5fa !important; }
    .dark .dark\:hover\:bg-slate-800\/50:hover { background-color: rgba(30, 41, 59, 0.5) !important; }

    /* Pagination Overrides */
    .dark .pagination .page-link { background-color: #1e293b !important; border-color: #334155 !important; color: #cbd5e1 !important; }
    .dark .pagination .page-item.active .page-link { background-color: #3b82f6 !important; border-color: #3b82f6 !important; color: white !important; }
    .dark .pagination .page-item.disabled .page-link { background-color: #0f172a !important; color: #475569 !important; border-color: #1e293b !important; }
</style>
@endpush

@section('content')
    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight flex items-center gap-3 mb-1 transition-colors duration-500">
                <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 bg-slate-100 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-blue-900/30 border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 rounded-xl flex items-center justify-center transition-all duration-300 shadow-inner">
                    <i class="mdi mdi-arrow-left text-xl"></i>
                </a>
                Peringkat Toko (Leaderboard)
            </h2>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest pl-14 m-0 transition-colors duration-500">Analisis Kinerja Mitra Terdaftar</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden mb-8 transition-colors duration-500">
        
        {{-- TOOLBAR (SEARCH & FILTER) --}}
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between gap-4">
            <form action="{{ route('admin.dashboard.top_stores') }}" method="GET" class="flex flex-col sm:flex-row w-full gap-3">
                
                {{-- Pencarian --}}
                <div class="flex-1 max-w-md flex">
                    <span class="input-group-text bg-slate-50 text-slate-500 transition-colors duration-500"><i class="mdi mdi-magnify text-lg"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control-custom px-4 py-2 bg-slate-50 dark:bg-slate-800/50 transition-colors duration-500" placeholder="Cari nama toko...">
                </div>

                {{-- Sorting Dropdown --}}
                <div class="flex items-center gap-3">
                    <select name="sort" onchange="this.form.submit()" class="form-control-custom px-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 cursor-pointer min-w-[200px] transition-colors duration-500">
                        <option value="gmv" {{ request('sort') == 'gmv' ? 'selected' : '' }}>Urutkan: Penjualan Tertinggi</option>
                        <option value="order" {{ request('sort') == 'order' ? 'selected' : '' }}>Urutkan: Pesanan Terbanyak</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Urutkan: Rating Tertinggi</option>
                    </select>
                </div>
            </form>
        </div>

        {{-- TABEL UTAMA --}}
        <div class="overflow-x-auto table-container">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800 transition-colors duration-500">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">Rank</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Informasi Toko</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Tier Kasta</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Total GMV</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Pesanan Selesai</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Rating Ulasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 transition-colors duration-500">
                    @forelse($topToko as $index => $toko)
                    @php
                        // Menentukan ranking aslinya walaupun berpindah halaman (Pagination)
                        $rank = ($topToko->currentPage() - 1) * $topToko->perPage() + $index + 1;
                        
                        $initials = strtoupper(substr($toko->nama_toko ?? 'TK', 0, 2));
                        $logoPath = 'assets/uploads/logos/' . ($toko->logo_toko ?? '');
                        $hasLogo = !empty($toko->logo_toko) && file_exists(public_path($logoPath));
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-300">
                        
                        {{-- RANKING --}}
                        <td class="px-6 py-5 text-center w-20">
                            @if($rank == 1)
                                <i class="mdi mdi-medal text-amber-400 text-4xl drop-shadow-[0_5px_10px_rgba(251,191,36,0.5)]"></i>
                            @elseif($rank == 2)
                                <i class="mdi mdi-medal text-slate-300 text-3xl drop-shadow-[0_5px_10px_rgba(203,213,225,0.4)] dark:drop-shadow-[0_5px_10px_rgba(255,255,255,0.2)]"></i>
                            @elseif($rank == 3)
                                <i class="mdi mdi-medal text-orange-500 text-2xl drop-shadow-[0_5px_10px_rgba(234,88,12,0.4)]"></i>
                            @else
                                <div class="w-8 h-8 mx-auto rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-black text-slate-500 dark:text-slate-400 transition-colors duration-500">
                                    {{ $rank }}
                                </div>
                            @endif
                        </td>

                        {{-- INFO TOKO --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl shrink-0 overflow-hidden shadow-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center text-lg font-black text-slate-400 transition-colors duration-500">
                                    @if($hasLogo)
                                        <img src="{{ asset($logoPath) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-800 text-white dark:bg-slate-700 transition-colors duration-500">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-black text-slate-800 dark:text-white text-sm mb-1 transition-colors duration-500">{{ $toko->nama_toko }}</div>
                                    <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1 transition-colors duration-500">
                                        <i class="mdi mdi-location-enter text-blue-500"></i> {{ $toko->nama_kota ?? 'Lokasi Belum Diatur' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- TIER KASTA TOKO --}}
                        <td class="px-6 py-5">
                            @if($toko->tier_toko == 'official_store')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-50 dark:bg-purple-500/10 border border-purple-200 dark:border-purple-500/30 text-purple-600 dark:text-purple-400 rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors duration-500">
                                    <i class="mdi mdi-check-decagram text-sm"></i> Official
                                </span>
                            @elseif($toko->tier_toko == 'power_merchant')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-600 dark:text-emerald-400 rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors duration-500">
                                    <i class="mdi mdi-lightning-bolt text-sm"></i> Power
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors duration-500">
                                    <i class="mdi mdi-check-circle-outline text-sm text-blue-500"></i> Verified
                                </span>
                            @endif
                        </td>

                        {{-- GMV --}}
                        <td class="px-6 py-5">
                            <span class="font-black text-emerald-600 dark:text-emerald-400 text-base transition-colors duration-500 dark:text-glow-emerald">
                                Rp {{ number_format($toko->total_gmv, 0, ',', '.') }}
                            </span>
                        </td>

                        {{-- TOTAL ORDER --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <i class="mdi mdi-package-variant-closed text-slate-400 text-lg transition-colors duration-500"></i>
                                <span class="font-black text-slate-800 dark:text-white text-base transition-colors duration-500">{{ number_format($toko->total_order) }}</span>
                            </div>
                        </td>

                        {{-- RATING --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-1.5 bg-yellow-50 dark:bg-amber-500/10 w-fit px-3 py-1.5 rounded-xl border border-yellow-100 dark:border-amber-500/20 transition-colors duration-500">
                                <i class="mdi mdi-star text-yellow-500 dark:text-amber-400 text-base leading-none transition-colors duration-500"></i>
                                <span class="font-black text-yellow-700 dark:text-white text-sm transition-colors duration-500">{{ number_format($toko->rating, 1) }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center bg-slate-50/50 dark:bg-slate-800/30 transition-colors duration-500">
                            <i class="mdi mdi-store-search-outline text-6xl text-slate-300 dark:text-slate-600 mb-4 block transition-colors duration-500"></i>
                            <h6 class="text-lg font-black text-slate-600 dark:text-slate-300 mb-1 transition-colors duration-500">Toko tidak ditemukan.</h6>
                            <p class="text-sm font-bold text-slate-400 dark:text-slate-500 m-0 transition-colors duration-500">Coba gunakan kata kunci pencarian yang lain.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 transition-colors duration-500 flex justify-center">
            {{ $topToko->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection