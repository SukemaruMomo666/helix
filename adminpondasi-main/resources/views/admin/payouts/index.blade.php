@extends('layouts.admin')

@section('title', 'Pusat Keuangan & Payout')

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM FINANCIAL Payout CSS       == */
    /* ========================================= */

    /* Hover Lift Effect */
    .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-5px); }

    /* Custom Scrollbar */
    .table-wrapper::-webkit-scrollbar { height: 6px; }
    .table-wrapper::-webkit-scrollbar-track { background: transparent; }
    .table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .dark .table-wrapper::-webkit-scrollbar-thumb { background: #475569; }
    .dark .table-wrapper::-webkit-scrollbar-thumb:hover { background: #64748b; }

    /* Neon Glow for Dark Mode Financials */
    .dark .text-glow-indigo { text-shadow: 0 0 15px rgba(99, 102, 241, 0.4); }
    .dark .text-glow-emerald { text-shadow: 0 0 15px rgba(16, 185, 129, 0.4); }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */

    /* Card, Tabel, & Container */
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-900\/50 { background-color: rgba(15, 23, 42, 0.5) !important; }
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-800\/40 { background-color: rgba(30, 41, 59, 0.4) !important; }
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }

    .dark .dark\:border-slate-800 { border-color: #1e293b !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:border-slate-700\/50 { border-color: rgba(51, 65, 85, 0.5) !important; }

    /* Modal & Form Overrides */
    .dark .modal-content { background-color: #0f172a !important; border: 1px solid #1e293b !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important; color: #f8fafc !important; }
    .dark .modal-header, .dark .modal-footer { border-color: #1e293b !important; background-color: #0f172a !important; }
    .dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    .dark .form-control { background-color: #1e293b !important; border-color: #334155 !important; color: #f8fafc !important; }
    .dark .form-control:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important; }
    .dark .form-control::placeholder { color: #64748b !important; }

    /* Special Sections (Amber alerts, Gradient KPI) */
    .dark .dark\:from-indigo-900\/80 { --tw-gradient-from: rgba(49, 46, 129, 0.8) !important; }
    .dark .dark\:to-blue-900\/80 { --tw-gradient-to: rgba(30, 58, 138, 0.8) !important; }
    .dark .dark\:bg-amber-500\/10 { background-color: rgba(245, 158, 11, 0.1) !important; }
    .dark .dark\:bg-emerald-500\/10 { background-color: rgba(16, 185, 129, 0.1) !important; }
    .dark .dark\:bg-rose-500\/10 { background-color: rgba(244, 63, 94, 0.1) !important; }
    .dark .dark\:border-rose-500\/30 { border-color: rgba(244, 63, 94, 0.3) !important; }
    .dark .dark\:border-amber-500\/20 { border-color: rgba(245, 158, 11, 0.2) !important; }
    .dark .dark\:border-emerald-500\/20 { border-color: rgba(16, 185, 129, 0.2) !important; }

    /* Typography */
    .dark .dark\:text-white { color: #ffffff !important; }
    .dark .dark\:text-slate-100 { color: #f1f5f9 !important; }
    .dark .dark\:text-slate-200 { color: #e2e8f0 !important; }
    .dark .dark\:text-slate-300 { color: #cbd5e1 !important; }
    .dark .dark\:text-slate-400 { color: #94a3b8 !important; }
    .dark .dark\:text-indigo-400 { color: #818cf8 !important; }
    .dark .dark\:text-emerald-400 { color: #34d399 !important; }
    .dark .dark\:text-rose-400 { color: #fb7185 !important; }
    .dark .dark\:text-blue-400 { color: #60a5fa !important; }

    /* Pagination */
    .dark .pagination .page-link { background-color: #1e293b !important; border-color: #334155 !important; color: #cbd5e1 !important; }
    .dark .pagination .page-item.active .page-link { background-color: #3b82f6 !important; border-color: #3b82f6 !important; color: white !important; }
</style>
@endpush

@section('content')

{{-- HEADER HALAMAN --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-1 transition-colors duration-300">
            Pusat Pencairan Dana
        </h2>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 transition-colors duration-300">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors text-decoration-none">Dashboard</a>
            <i class="mdi mdi-chevron-right text-sm"></i>
            <span class="text-blue-600 dark:text-blue-400">Manajemen Payout (B2B)</span>
        </div>
    </div>
    <div class="bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 px-4 py-2.5 rounded-xl flex items-center gap-3 transition-colors duration-300">
        <i class="mdi mdi-bank-transfer text-indigo-500 text-xl"></i>
        <span class="text-xs font-bold text-indigo-700 dark:text-indigo-400">Pastikan transfer manual dilakukan sebelum melakukan konfirmasi di sistem.</span>
    </div>
</div>

{{-- 1. FINANCIAL METRICS (KPI) --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    {{-- Card 1: Pending Amount (Highlight) --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-blue-700 dark:from-indigo-900/80 dark:to-blue-900/80 p-6 rounded-[2rem] shadow-xl shadow-indigo-500/20 dark:shadow-none transition-all duration-300 group hover-lift">
        <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
            <i class="mdi mdi-cash-sync text-[140px] text-white"></i>
        </div>
        <div class="relative z-10">
            <div class="flex justify-between items-center mb-4">
                <span class="text-[10px] font-black text-indigo-100 uppercase tracking-widest">Perlu Ditransfer (Pending)</span>
                <i class="mdi mdi-timer-sand text-indigo-200 text-xl animate-pulse"></i>
            </div>
            <h3 class="text-3xl font-black text-white mb-2 font-mono">Rp {{ number_format($stats['total_pending_amount'], 0, ',', '.') }}</h3>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md bg-white/20 text-white text-[10px] font-black tracking-widest uppercase">{{ $stats['total_pending_count'] }} Permintaan</span>
            </div>
        </div>
    </div>

    {{-- Card 2: Completed --}}
    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-6 rounded-[2rem] shadow-sm hover-lift transition-colors duration-300 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <i class="mdi mdi-check-circle text-[100px] text-emerald-500"></i>
        </div>
        <div class="relative z-10">
            <div class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Sukses Dibayar (Bulan Ini)</div>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 dark:text-glow-emerald">Rp {{ number_format($stats['total_completed_amount'], 0, ',', '.') }}</h3>
            <div class="mt-2 text-[11px] font-bold text-slate-500">Aliran dana keluar sukses terverifikasi.</div>
        </div>
    </div>

    {{-- Card 3: Rejected --}}
    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-6 rounded-[2rem] shadow-sm hover-lift transition-colors duration-300 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <i class="mdi mdi-close-octagon text-[100px] text-rose-500"></i>
        </div>
        <div class="relative z-10">
            <div class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Permintaan Ditolak</div>
            <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 leading-none">{{ number_format($stats['total_rejected']) }} <span class="text-sm font-bold text-slate-500">Kasus</span></h3>
            <div class="mt-4 flex items-center gap-1.5 text-[11px] font-bold text-amber-600 bg-amber-50 dark:bg-amber-500/10 px-2.5 py-1 rounded-lg w-fit">
                <i class="mdi mdi-alert-circle"></i> Butuh Tinjauan Ulang
            </div>
        </div>
    </div>
</div>

{{-- 2. TABEL PAYOUT --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[1.5rem] shadow-sm overflow-hidden transition-colors duration-300">

    {{-- Header Filter & Search --}}
    <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col lg:flex-row justify-between items-center gap-4 transition-colors">

        <div class="flex p-1 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700/50 overflow-x-auto w-full lg:w-auto hide-scrollbar">
            @foreach(['pending' => 'Perlu Diproses', 'completed' => 'Selesai', 'rejected' => 'Ditolak'] as $val => $label)
                <a href="{{ route('admin.payouts.index', ['status' => $val, 'search' => $search]) }}"
                   class="px-5 py-2 text-xs font-black capitalize rounded-lg transition-all text-decoration-none outline-none whitespace-nowrap {{ $status_filter == $val ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-white shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('admin.payouts.index') }}" method="GET" class="relative w-full lg:w-80">
            <input type="hidden" name="status" value="{{ $status_filter }}">
            <i class="mdi mdi-magnify absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-lg"></i>
            <input type="text" name="search" value="{{ $search }}"
                   class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                   placeholder="Cari nama toko atau ID...">
        </form>
    </div>

    {{-- Table Data --}}
    <div class="overflow-x-auto table-wrapper">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">ID & Waktu</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Toko (Mitra)</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Rekening Tujuan</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Jumlah</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                @forelse($payouts as $p)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors duration-200">
                    <td class="px-6 py-5">
                        <div class="text-sm font-black text-slate-800 dark:text-white font-mono">#PAY-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-wider">{{ \Carbon\Carbon::parse($p->tanggal_request)->format('d M Y, H:i') }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm font-black text-slate-800 dark:text-slate-200">{{ $p->nama_toko }}</div>
                        <div class="text-[11px] font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1.5 mt-0.5"><i class="mdi mdi-email-outline"></i> {{ $p->email_pemilik }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl w-60 shadow-inner dark:shadow-none">
                            <div class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-1">{{ $p->rekening_bank ?? 'N/A' }}</div>
                            <div class="text-sm font-black text-slate-800 dark:text-white font-mono tracking-wider">{{ $p->nomor_rekening ?? 'ERR-EMPTY' }}</div>
                            <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-1">a.n {{ $p->atas_nama_rekening ?? '-' }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-lg font-black text-blue-600 dark:text-white dark:text-glow-indigo font-mono">Rp {{ number_format($p->jumlah_payout, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-5">
                        @php
                            $st = strtolower($p->status);
                            $badgeCls = $st == 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border-amber-200 dark:border-amber-500/20' :
                                      ($st == 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20' :
                                      'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 border-rose-200 dark:border-rose-500/20');
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $badgeCls }}">
                            <i class="mdi {{ $st == 'pending' ? 'mdi-clock-outline' : ($st == 'completed' ? 'mdi-check-circle' : 'mdi-close-circle') }} text-sm leading-none"></i>
                            {{ $p->status }}
                        </span>
                        @if($p->tanggal_proses)
                            <div class="text-[9px] font-bold text-slate-400 mt-2">DIPROSES: <br>{{ \Carbon\Carbon::parse($p->tanggal_proses)->format('d/m/y H:i') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-5 text-center">
                        @if($p->status == 'pending')
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" class="flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[11px] font-black rounded-xl shadow-md shadow-emerald-500/20 transition-all outline-none btn-proses"
                                        data-bs-toggle="modal" data-bs-target="#prosesModal"
                                        data-id="{{ $p->id }}"
                                        data-toko="{{ $p->nama_toko }}"
                                        data-jumlah="{{ number_format($p->jumlah_payout, 0, ',', '.') }}"
                                        data-bank="{{ $p->rekening_bank }}"
                                        data-rekening="{{ $p->nomor_rekening }}"
                                        data-owner="{{ $p->atas_nama_rekening }}">
                                    <i class="mdi mdi-bank-transfer text-lg"></i> BAYAR
                                </button>
                                <button type="button" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 border border-rose-200 dark:border-rose-500/30 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all btn-tolak"
                                        data-bs-toggle="modal" data-bs-target="#tolakModal"
                                        data-id="{{ $p->id }}">
                                    <i class="mdi mdi-close-circle-outline text-xl"></i>
                                </button>
                            </div>
                        @else
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><i class="mdi mdi-lock text-sm"></i> Terkunci</div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-20 px-6 bg-slate-50/50 dark:bg-transparent transition-colors">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800/50 mb-4 transition-colors">
                            <i class="mdi mdi-cash-register text-4xl text-slate-300 dark:text-slate-600"></i>
                        </div>
                        <h5 class="text-base font-black text-slate-700 dark:text-slate-300 mb-1">Tidak ada data penarikan</h5>
                        <p class="text-xs font-bold text-slate-500 m-0">Gunakan tab status di atas untuk melihat riwayat data lainnya.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row justify-between items-center gap-4 transition-colors">
        <div class="text-xs font-bold text-slate-500 dark:text-slate-400">
            Menampilkan <span class="text-blue-600 dark:text-blue-400 font-black">{{ $payouts->firstItem() ?? 0 }}</span> - <span class="text-blue-600 dark:text-blue-400 font-black">{{ $payouts->lastItem() ?? 0 }}</span> dari <span class="text-slate-800 dark:text-white font-black">{{ $payouts->total() }}</span> permintaan
        </div>
        <div>{{ $payouts->links('pagination::bootstrap-5') }}</div>
    </div>
</div>

{{-- MODAL PROSES TRANSFER MANUAL --}}
<div class="modal fade" id="prosesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-[2rem] overflow-hidden border-0 shadow-2xl transition-colors duration-300">
            <form id="formProses" method="POST" action="">
                @csrf
                <input type="hidden" name="action" value="approve">
                <div class="modal-header p-6 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
                    <h5 class="text-base font-black text-slate-800 dark:text-white m-0 flex items-center gap-2">
                        <i class="mdi mdi-bank-transfer text-emerald-500 text-2xl"></i> Konfirmasi Transfer Manual
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-8 bg-slate-50/50 dark:bg-slate-900">
                    <div class="text-center mb-6">
                        <div class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">JUMLAH DANA TRANSFER</div>
                        <div class="text-4xl font-black text-blue-600 dark:text-white font-mono tracking-tighter">Rp <span id="mdl-jumlah">0</span></div>
                    </div>

                    <div class="p-6 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm text-center mb-6">
                        <div class="text-xs font-black text-indigo-500 dark:text-indigo-400 uppercase tracking-widest mb-1" id="mdl-bank">BANK</div>
                        <div class="text-2xl font-black text-slate-800 dark:text-white font-mono tracking-widest mb-2" id="mdl-rekening">0000000000</div>
                        <div class="text-sm font-bold text-slate-500 dark:text-slate-300">a.n <span id="mdl-owner" class="text-slate-800 dark:text-white font-black uppercase">Nama Pemilik</span></div>
                    </div>

                    <div class="flex gap-4 p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-2xl">
                        <i class="mdi mdi-shield-alert text-amber-500 text-2xl flex-shrink-0"></i>
                        <p class="text-xs font-bold text-amber-800 dark:text-amber-200 m-0 leading-relaxed">
                            <strong>WAJIB:</strong> Anda harus mentransfer dana secara manual via e-Banking ke rekening di atas sebelum menekan konfirmasi ini. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>
                <div class="modal-footer p-6 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex gap-3">
                    <button type="button" class="flex-1 px-5 py-3 rounded-xl font-bold text-sm text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors outline-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="flex-[1.5] px-6 py-3 rounded-xl font-black text-sm text-white bg-emerald-500 hover:bg-emerald-600 shadow-lg shadow-emerald-500/30 transition-all outline-none">KONFIRMASI TERKIRIM</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TOLAK PAYOUT --}}
<div class="modal fade" id="tolakModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-[2rem] overflow-hidden border-0 shadow-2xl">
            <form id="formTolak" method="POST" action="">
                @csrf
                <input type="hidden" name="action" value="reject">
                <div class="modal-header p-6 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
                    <h5 class="text-base font-black text-rose-600 flex items-center gap-2 m-0"><i class="mdi mdi-alert-circle text-xl"></i> Tolak Penarikan Dana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-6 dark:bg-slate-900">
                    <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">ALASAN PENOLAKAN</label>
                    <textarea name="catatan_admin" class="form-control w-full p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all resize-none shadow-inner dark:shadow-none" rows="4" required placeholder="Contoh: Nomor rekening tidak valid atau data mitra belum lengkap..."></textarea>
                    <div class="mt-3 flex gap-2 items-center text-[10px] font-bold text-slate-500 dark:text-slate-400 px-1">
                        <i class="mdi mdi-information-outline text-sm"></i> Dana akan secara otomatis dikembalikan ke saldo dompet Mitra Toko.
                    </div>
                </div>
                <div class="modal-footer p-6 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex gap-3">
                    <button type="button" class="px-5 py-3 rounded-xl font-bold text-sm text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 outline-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-3 rounded-xl font-black text-sm text-white bg-rose-500 hover:bg-rose-600 shadow-md transition-all outline-none">KONFIRMASI TOLAK</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logika Pengisian Data Modal Proses Bayar
        document.querySelectorAll('.btn-proses').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('mdl-jumlah').innerText = this.getAttribute('data-jumlah');
                document.getElementById('mdl-bank').innerText = this.getAttribute('data-bank');
                document.getElementById('mdl-rekening').innerText = this.getAttribute('data-rekening');
                document.getElementById('mdl-owner').innerText = this.getAttribute('data-owner');

                // Pastikan base URL ini sesuai dengan struktur URL Anda
                document.getElementById('formProses').action = `/admin/payouts/${id}/process`;
            });
        });

        // Logika Pengisian Data Modal Tolak
        document.querySelectorAll('.btn-tolak').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('formTolak').action = `/admin/payouts/${id}/process`;
            });
        });
    });
</script>
@endpush
