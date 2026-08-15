@extends('layouts.admin')

@section('title', 'Laporan & Analitik Platform')

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM ANALYTICS CSS              == */
    /* ========================================= */
    .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-4px); }

    .table-wrapper::-webkit-scrollbar { height: 6px; }
    .table-wrapper::-webkit-scrollbar-track { background: transparent; }
    .table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .dark .table-wrapper::-webkit-scrollbar-thumb { background: #475569; }

    /* Date Picker Icon Inversion */
    input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.6; transition: 0.2s; }
    input[type="date"]::-webkit-calendar-picker-indicator:hover { opacity: 1; }
    .dark input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }

    /* Pagination */
    .dark .pagination .page-link { background-color: #1e293b; border-color: #334155; color: #cbd5e1; }
    .dark .pagination .page-item.active .page-link { background-color: #3b82f6; border-color: #3b82f6; color: white; }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-900\/80 { background-color: rgba(15, 23, 42, 0.8) !important; }
    .dark .dark\:bg-slate-900\/50 { background-color: rgba(15, 23, 42, 0.5) !important; }
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-800\/80 { background-color: rgba(30, 41, 59, 0.8) !important; }
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:bg-slate-800\/40 { background-color: rgba(30, 41, 59, 0.4) !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }

    .dark .dark\:border-slate-800 { border-color: #1e293b !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:border-slate-700\/50 { border-color: rgba(51, 65, 85, 0.5) !important; }

    /* Specific Elements */
    .dark .dark\:from-emerald-900\/10 { --tw-gradient-from: rgba(6, 78, 59, 0.1) !important; }
    .dark .dark\:bg-emerald-500\/20 { background-color: rgba(16, 185, 129, 0.2) !important; }
    .dark .dark\:bg-blue-500\/10 { background-color: rgba(59, 130, 246, 0.1) !important; }
    .dark .dark\:bg-rose-500\/10 { background-color: rgba(244, 63, 94, 0.1) !important; }
    .dark .dark\:bg-amber-500\/10 { background-color: rgba(245, 158, 11, 0.1) !important; }
    .dark .dark\:bg-emerald-500\/10 { background-color: rgba(16, 185, 129, 0.1) !important; }

    /* Typography */
    .dark .dark\:text-white { color: #ffffff !important; }
    .dark .dark\:text-slate-200 { color: #e2e8f0 !important; }
    .dark .dark\:text-slate-300 { color: #cbd5e1 !important; }
    .dark .dark\:text-slate-400 { color: #94a3b8 !important; }
    .dark .dark\:text-blue-400 { color: #60a5fa !important; }
    .dark .dark\:text-rose-400 { color: #fb7185 !important; }
    .dark .dark\:text-emerald-400 { color: #34d399 !important; }
    .dark .dark\:text-amber-400 { color: #fbbf24 !important; }

    /* Hover States */
    .dark .dark\:hover\:bg-slate-700:hover { background-color: #334155 !important; }
    .dark .dark\:hover\:bg-slate-800:hover { background-color: #1e293b !important; }
</style>
@endpush

@section('content')

{{-- HEADER HALAMAN --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-6">
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-1 transition-colors duration-300">
            Laporan Finansial
        </h2>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 transition-colors duration-300">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors text-decoration-none">Dashboard</a>
            <i class="mdi mdi-chevron-right text-sm"></i>
            <span class="text-blue-600 dark:text-blue-400">Analitik Pendapatan</span>
        </div>
        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-2 m-0 max-w-xl leading-relaxed">
            Transparansi arus kas, perhitungan biaya potongan Payment Gateway (Midtrans), dan ringkasan laba bersih platform Pondasikita.
        </p>
    </div>
</div>

{{-- 1. TOOLBAR & FILTER --}}
<form action="{{ route('admin.reports.index') }}" method="GET" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 mb-8 shadow-sm transition-colors duration-300 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">

    <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 p-1.5 rounded-xl shadow-inner dark:shadow-none w-full sm:w-auto">
            <i class="mdi mdi-calendar-range text-slate-400 dark:text-slate-500 ml-2 text-lg"></i>
            <input type="date" name="start_date" value="{{ $start_date ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required class="bg-transparent border-none text-xs font-black text-slate-700 dark:text-slate-200 outline-none cursor-pointer focus:ring-0 p-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase">s/d</span>
            <input type="date" name="end_date" value="{{ $end_date ?? \Carbon\Carbon::now()->format('Y-m-d') }}" required class="bg-transparent border-none text-xs font-black text-slate-700 dark:text-slate-200 outline-none cursor-pointer focus:ring-0 p-1">
        </div>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-md shadow-blue-600/20 transition-all outline-none w-full sm:w-auto flex items-center justify-center gap-2">
            <i class="mdi mdi-filter-variant text-base"></i> Terapkan
        </button>
    </div>

    <div class="flex items-center gap-3 w-full xl:w-auto">
        <button type="button" class="flex-1 xl:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-sm outline-none">
            <i class="mdi mdi-file-excel text-emerald-500 text-base"></i> Excel
        </button>
        <button type="button" class="flex-1 xl:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-sm outline-none">
            <i class="mdi mdi-file-pdf-box text-rose-500 text-base"></i> PDF
        </button>
    </div>
</form>

{{-- 2. KPI METRICS (ENTERPRISE STYLE) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    {{-- GMV --}}
    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-6 rounded-[2rem] shadow-sm hover-lift transition-colors duration-300 relative overflow-hidden group">
        <div class="absolute right-6 top-6 w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform">
            <i class="mdi mdi-shopping-outline"></i>
        </div>
        <div class="relative z-10">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 mt-1">Gross Merchandise Value</div>
            <div class="text-3xl font-black text-slate-800 dark:text-white leading-none mb-4 font-mono tracking-tight transition-colors duration-300">Rp {{ number_format($stats['gmv'] ?? 0, 0, ',', '.') }}</div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700 rounded-lg text-[10px] font-bold text-blue-600 dark:text-blue-400 transition-colors">
                <i class="mdi mdi-trending-up"></i> Total omzet kotor
            </div>
        </div>
    </div>

    {{-- BIAYA MIDTRANS --}}
    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-6 rounded-[2rem] shadow-sm hover-lift transition-colors duration-300 relative overflow-hidden group">
        <div class="absolute right-6 top-6 w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform">
            <i class="mdi mdi-bank-minus"></i>
        </div>
        <div class="relative z-10">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 mt-1">Biaya Midtrans (Gateway)</div>
            <div class="text-3xl font-black text-rose-600 dark:text-rose-400 leading-none mb-4 font-mono tracking-tight transition-colors duration-300">- Rp {{ number_format($stats['midtrans_costs'] ?? 0, 0, ',', '.') }}</div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700 rounded-lg text-[10px] font-bold text-rose-600 dark:text-rose-400 transition-colors">
                <i class="mdi mdi-alert-circle-outline"></i> Potongan otomatis
            </div>
        </div>
    </div>

    {{-- NET REVENUE --}}
    <div class="bg-white dark:bg-slate-800/40 border border-emerald-200 dark:border-emerald-700/50 p-6 rounded-[2rem] shadow-sm hover-lift transition-colors duration-300 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent dark:from-emerald-900/10 dark:to-transparent opacity-50 pointer-events-none"></div>
        <div class="absolute right-6 top-6 w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform border border-emerald-200 dark:border-emerald-500/30">
            <i class="mdi mdi-cash-check"></i>
        </div>
        <div class="relative z-10">
            <div class="text-[10px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest mb-1 mt-1">Laba Bersih Platform</div>
            <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 leading-none mb-4 font-mono tracking-tight transition-colors duration-300">Rp {{ number_format($stats['revenue'] ?? 0, 0, ',', '.') }}</div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500 text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm shadow-emerald-500/20 transition-colors">
                <i class="mdi mdi-shield-check"></i> Pendapatan Admin
            </div>
        </div>
    </div>

    {{-- AOV --}}
    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-6 rounded-[2rem] shadow-sm hover-lift transition-colors duration-300 relative overflow-hidden group">
        <div class="absolute right-6 top-6 w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-500 dark:text-amber-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform">
            <i class="mdi mdi-calculator"></i>
        </div>
        <div class="relative z-10">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 mt-1">Avg. Order Value</div>
            <div class="text-3xl font-black text-slate-800 dark:text-white leading-none mb-4 font-mono tracking-tight transition-colors duration-300">Rp {{ number_format($stats['aov'] ?? 0, 0, ',', '.') }}</div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700 rounded-lg text-[10px] font-bold text-slate-500 dark:text-slate-400 transition-colors">
                <i class="mdi mdi-chart-bubble"></i> Rata-rata per transaksi
            </div>
        </div>
    </div>
</div>

{{-- 3. CHART ANALYTICS --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 lg:p-8 mb-8 shadow-sm transition-colors duration-300">
    <div class="flex justify-between items-center mb-6">
        <h4 class="text-base font-black text-slate-800 dark:text-white flex items-center gap-2">
            <i class="mdi mdi-chart-areaspline text-blue-500 text-xl"></i> Tren Pertumbuhan Penjualan
        </h4>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:text-blue-500 transition-colors outline-none">
            <i class="mdi mdi-dots-vertical text-lg"></i>
        </button>
    </div>
    <div class="w-full h-[380px] relative">
        {{-- ID INI HARUS SAMA DENGAN SCRIPT (salesChart) --}}
        <canvas id="salesChart"></canvas>
    </div>
</div>

{{-- 4. DETAILED TRANSACTION TABLE --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[1.5rem] shadow-sm overflow-hidden mb-8 transition-colors duration-300">
    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-white dark:bg-slate-900/80 transition-colors duration-300">
        <h3 class="text-sm font-black text-slate-800 dark:text-white">Rincian Transaksi Terakhir</h3>
    </div>

    <div class="overflow-x-auto table-wrapper">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Invoice & Pelanggan</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Waktu Transaksi</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Total Transaksi</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Fee Midtrans</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                @forelse($recent_transactions as $trx)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors duration-200">
                    <td class="px-6 py-5 align-middle">
                        <span class="text-sm font-black text-blue-600 dark:text-blue-400 font-mono">{{ $trx->kode_invoice }}</span>
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase">{{ $trx->nama_pembeli }}</div>
                    </td>
                    <td class="px-6 py-5 align-middle">
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M Y') }}</span><br>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('H:i:s') }} WIB</span>
                    </td>
                    <td class="px-6 py-5 align-middle">
                        <span class="text-base font-black text-slate-800 dark:text-white font-mono">Rp {{ number_format($trx->total_final, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-6 py-5 align-middle">
                        <span class="text-sm font-black text-rose-500 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 px-2 py-1 rounded-md border border-rose-100 dark:border-rose-500/20 font-mono">- Rp {{ number_format($trx->midtrans_fee, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-6 py-5 align-middle">
                        @php
                            // PERBAIKAN BUG LOGIKA BADGE: Merah untuk batal/gagal, Hijau untuk Lunas
                            $st = strtolower($trx->status_pembayaran ?? 'pending');

                            if ($st == 'paid' || $st == 'dp_paid') {
                                $badgeCls = 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
                                $icon = 'mdi-check-circle';
                                $label = $st == 'paid' ? 'LUNAS' : 'DP LUNAS';
                            } elseif (in_array($st, ['failed', 'expired', 'cancelled', 'batal'])) {
                                $badgeCls = 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20';
                                $icon = 'mdi-close-circle';
                                $label = strtoupper($st);
                            } else {
                                $badgeCls = 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
                                $icon = 'mdi-clock-outline';
                                $label = strtoupper($st);
                            }
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $badgeCls }}">
                            <i class="mdi {{ $icon }} text-sm leading-none"></i> {{ $label }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-20 px-6 bg-slate-50/50 dark:bg-transparent transition-colors">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800/50 mb-4 transition-colors">
                            <i class="mdi mdi-text-box-search-outline text-4xl text-slate-300 dark:text-slate-600"></i>
                        </div>
                        <h5 class="text-base font-black text-slate-700 dark:text-slate-300 mb-1">Tidak ada transaksi ditemukan</h5>
                        <p class="text-xs font-bold text-slate-500 m-0">Tidak ada riwayat transaksi pada rentang tanggal filter yang dipilih.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-center transition-colors">
        <div class="pagination-wrapper">
            {{-- PERBAIKAN BUG: Appends agar filter tanggal dan pencarian tidak hilang saat klik halaman 2, 3 dst --}}
            {{ $recent_transactions->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // PERBAIKAN BUG ID: Gunakan ID canvas yang benar (salesChart)
    const canvas = document.getElementById('salesChart');
    if(!canvas) return;

    const ctx = canvas.getContext('2d');
    let chartInstance = null;

    // Pastikan variabel memiliki fallback array kosong jika tidak ada
    const labels = {!! json_encode($chart_labels ?? []) !!};
    const values = {!! json_encode($chart_values ?? []) !!};

    function renderChart() {
        const isDark = document.documentElement.classList.contains('dark');

        // Atur warna berdasarkan tema
        const gridColor = isDark ? 'rgba(51, 65, 85, 0.3)' : '#f1f5f9';
        const tickColor = '#64748b';
        const tooltipBg = isDark ? 'rgba(15, 23, 42, 0.9)' : '#ffffff';
        const tooltipText = isDark ? '#ffffff' : '#1e293b';
        const tooltipBorder = isDark ? 'rgba(51, 65, 85, 0.5)' : '#e2e8f0';

        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, isDark ? 'rgba(99, 102, 241, 0.4)' : 'rgba(79, 70, 229, 0.2)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

        if(chartInstance) chartInstance.destroy();

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total GMV',
                    data: values,
                    borderColor: isDark ? '#6366f1' : '#4f46e5',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: isDark ? '#0f172a' : '#ffffff',
                    pointBorderColor: isDark ? '#6366f1' : '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: isDark ? '#6366f1' : '#4f46e5',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleColor: tooltipText,
                        bodyColor: tooltipText,
                        titleFont: { size: 11, family: "'Plus Jakarta Sans', sans-serif", weight: 'bold' },
                        bodyFont: { size: 14, family: "'Plus Jakarta Sans', sans-serif", weight: '900' },
                        padding: 12,
                        displayColors: false,
                        cornerRadius: isDark ? 12 : 8,
                        borderColor: tooltipBorder,
                        borderWidth: 1,
                        boxShadow: isDark ? 'none' : '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: tickColor, font: { family: "'Plus Jakarta Sans', sans-serif", weight: 'bold', size: 10 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, drawBorder: false, borderDash: [5, 5] },
                        ticks: {
                            color: tickColor,
                            font: { family: "'Plus Jakarta Sans', sans-serif", weight: 'bold', size: 10 },
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'jt';
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }

    // Render pertama kali
    renderChart();

    // Pantau perubahan class 'dark' pada HTML (otomatis re-render grafik)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                renderChart();
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });
});
</script>
@endpush
