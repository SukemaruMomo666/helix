@extends('layouts.seller')

@section('title', 'Dompet & Penghasilan')

@push('styles')
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* Mencegah input number menampilkan panah atas/bawah */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type="number"] { -moz-appearance: textfield; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 p-4 md:p-6 lg:p-8 font-sans text-slate-900 space-y-6">

    {{-- SETUP SWEETALERT TOAST --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
            customClass: { popup: 'rounded-2xl shadow-lg border border-slate-100' }
        });
    </script>
    @if(session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => Toast.fire({icon: 'success', title: '{!! session('success') !!}'}));</script>
    @endif
    @if(session('error'))
        <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({title: 'Gagal!', text: '{!! session('error') !!}', icon: 'error', customClass: { popup: 'rounded-3xl' }}));</script>
    @endif

    {{-- 1. HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm flex-shrink-0">
            <i class="mdi mdi-wallet-outline text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dompet & Penghasilan</h1>
            <p class="text-sm font-medium text-slate-500 mt-0.5">Pantau pendapatan dari pesanan yang selesai dan tarik saldo ke rekening Anda.</p>
        </div>
    </div>

    {{-- 2. MAIN LAYOUT (KIRI 2, KANAN 1) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ================================================= --}}
        {{-- SISI KIRI (DOMPET & TABEL)                        --}}
        {{-- ================================================= --}}
        <div class="lg:col-span-8 space-y-8">

            {{-- KARTU SALDO UTAMA --}}
            <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm relative overflow-hidden">
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-blue-50 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pb-6 mb-6 border-b border-slate-100">
                    <div>
                        <span class="flex items-center gap-1.5 text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1">
                            <i class="mdi mdi-check-decagram text-emerald-500 text-base leading-none"></i> Saldo Aktif (Bisa Ditarik)
                        </span>
                        <div class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">
                            Rp {{ number_format($saldo_aktif, 0, ',', '.') }}
                        </div>
                    </div>

                    <button type="button" onclick="openPayoutModal()" class="w-full md:w-auto flex items-center justify-center gap-2 px-6 py-3.5 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-sm shadow-slate-900/20 transition-all flex-shrink-0">
                        <i class="mdi mdi-bank-transfer-out text-xl leading-none"></i> Tarik Saldo
                    </button>
                </div>

                <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-amber-50 border border-amber-200/60 p-4 rounded-2xl">
                        <span class="flex items-center gap-1.5 text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">
                            <i class="mdi mdi-timer-sand text-sm leading-none"></i> Dana Tertahan (Pending)
                        </span>
                        <div class="text-xl font-black text-amber-600 mb-1">Rp {{ number_format($penghasilan_pending, 0, ',', '.') }}</div>
                        <p class="text-[10px] font-bold text-amber-600/70">Dana cair setelah pembeli klik pesanan selesai.</p>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                        <span class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Omzet Bulan Ini</span>
                        <div class="text-xl font-black text-emerald-500 mb-1">Rp {{ number_format($dilepas_bulan_ini, 0, ',', '.') }}</div>
                        <p class="text-[10px] font-bold text-slate-400">Omzet kotor dari pesanan yang telah selesai.</p>
                    </div>
                </div>
            </div>

            {{-- TABEL RIWAYAT DANA MASUK --}}
            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm flex flex-col">
                <div class="flex gap-6 px-6 pt-4 border-b border-slate-100 overflow-x-auto hide-scrollbar bg-slate-50/50">
                    <a href="?tab=dilepas" class="pb-3 text-sm font-bold whitespace-nowrap transition-colors border-b-2 {{ $tab == 'dilepas' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Dana Masuk (Selesai)</a>
                    <a href="?tab=pending" class="pb-3 text-sm font-bold whitespace-nowrap transition-colors border-b-2 {{ $tab == 'pending' ? 'border-amber-500 text-amber-500' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Dana Tertahan</a>
                </div>

                <div class="p-4 sm:p-6 border-b border-slate-100 bg-white">
                    <form action="{{ route('seller.finance.income') }}" method="GET" class="flex flex-col sm:flex-row gap-3 m-0">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <input type="date" name="date" class="w-full sm:w-auto bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none transition-all cursor-pointer shadow-sm" value="{{ request('date') }}" onchange="this.form.submit()">
                        <div class="relative w-full flex-1 group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="mdi mdi-magnify text-slate-400 group-focus-within:text-blue-600 transition-colors text-lg"></i>
                            </div>
                            <input type="text" name="search" placeholder="Cari No. Invoice..." value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:bg-white outline-none transition-all shadow-sm">
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">No. Invoice</th>
                                <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Tanggal</th>
                                <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest text-right">Nominal Masuk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($transaksi_list as $tx)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6 font-mono font-black text-blue-600 text-sm whitespace-nowrap">{{ $tx->kode_invoice }}</td>
                                    <td class="py-4 px-6 text-xs font-bold text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($tx->tanggal_transaksi)->format('d M Y, H:i') }}</td>
                                    <td class="py-4 px-6 text-right font-black text-emerald-500 text-base whitespace-nowrap">+ Rp {{ number_format($tx->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-16 text-center opacity-60 font-bold">Tidak ada data transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================================================= --}}
        {{-- SISI KANAN (WIDGETS)                              --}}
        {{-- ================================================= --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- WIDGET REKENING (REALTIME DARI tb_toko) --}}
<div class="bg-gradient-to-br from-slate-900 to-slate-800 border border-slate-800 rounded-3xl p-6 text-white shadow-xl shadow-slate-900/10 relative overflow-hidden">
    <i class="mdi mdi-bank text-9xl absolute -right-4 -bottom-6 text-white/5 pointer-events-none transform -rotate-12"></i>

    <h4 class="text-sm font-black text-slate-300 uppercase tracking-widest mb-4 flex items-center gap-2 border-b border-slate-700 pb-3">
        <i class="mdi mdi-bank text-lg text-blue-400"></i> Rekening Penerima
    </h4>

    <div class="relative z-10">
        @if($perlu_isi_rekening)
            <div class="bg-amber-500/10 border border-amber-500/20 p-4 rounded-2xl mb-4">
                <p class="text-[11px] font-black text-amber-500 uppercase tracking-widest mb-1 flex items-center gap-1">
                    <i class="mdi mdi-alert-circle"></i> Rekening Belum Diatur
                </p>
                <p class="text-xs font-medium text-amber-200/70 leading-relaxed">
                    Silakan isi data rekening untuk mencairkan saldo.
                </p>
            </div>
        @else
            <p class="text-xs font-medium text-slate-400 mb-1">Saldo akan ditransfer ke:</p>
            {{-- Menggunakan nama kolom asli dari tb_toko: rekening_bank & nomor_rekening --}}
            <h5 class="text-lg font-black text-white mb-0.5 tracking-tight">
                {{ $toko->rekening_bank }} - {{ $toko->nomor_rekening }}
            </h5>
            {{-- Kolom atas_nama_rekening di tb_toko --}}
            <p class="text-[11px] font-black text-blue-400 tracking-widest uppercase">
                A.N. {{ $toko->atas_nama_rekening }}
            </p>
        @endif

        {{-- PERBAIKAN: Menggunakan route yang benar --}}
        <a href="{{ route('seller.finance.bank') }}" class="mt-5 w-full flex items-center justify-center px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold rounded-xl transition-colors backdrop-blur-md">
            {{ $perlu_isi_rekening ? 'Atur Rekening Sekarang' : 'Ubah Rekening' }}
        </a>
    </div>
</div>

            {{-- RIWAYAT PENARIKAN --}}
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i class="mdi mdi-history text-lg text-slate-300"></i> Riwayat Tarik Saldo
                </h4>
                @forelse($riwayat_payout as $rp)
                    <div class="py-3.5 flex justify-between items-center border-b border-slate-50 last:border-0">
                        <div>
                            <div class="text-sm font-black text-slate-900">Rp {{ number_format($rp->jumlah_payout, 0, ',', '.') }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase">{{ \Carbon\Carbon::parse($rp->tanggal_request)->format('d M Y') }}</div>
                        </div>
                        <span class="px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest {{ $rp->status == 'completed' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600' }}">
                            {{ $rp->status }}
                        </span>
                    </div>
                @empty
                    <p class="text-center py-4 text-xs font-bold text-slate-400">Belum ada riwayat.</p>
                @endforelse
            </div>

        </div>

    </div>
</div>

{{-- MODAL TARIK SALDO --}}
<div id="payoutModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div id="modalOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0 duration-300" onclick="closePayoutModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="modalPanel" class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all opacity-0 scale-95 duration-300 w-full sm:max-w-lg border border-slate-200">

                <form action="{{ route('seller.finance.payout') }}" method="POST" id="payoutForm">
                    @csrf
                    <div class="bg-slate-50 px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2"><i class="mdi mdi-bank-transfer-out text-blue-600"></i> Tarik Saldo Toko</h3>
                        <button type="button" onclick="closePayoutModal()" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-red-100 text-slate-500 hover:text-red-500 transition-colors"><i class="mdi mdi-close"></i></button>
                    </div>

                    <div class="p-6 space-y-6">
                        @if($perlu_isi_rekening)
                            <div class="bg-red-50 border border-red-100 p-4 rounded-2xl flex items-start gap-3">
                                <i class="mdi mdi-alert-circle text-red-500 text-xl"></i>
                                <div>
                                    <p class="text-sm font-black text-red-600 mb-1">Rekening Belum Diatur!</p>
                                    <p class="text-xs font-medium text-red-500">Silakan <a href="{{ route('seller.finance.bank') }}" class="underline font-bold">isi data rekening</a> terlebih dahulu untuk mencairkan saldo.</p>
                                </div>
                            </div>
                        @else
                            <div class="bg-blue-600 text-white p-5 rounded-2xl shadow-inner relative overflow-hidden">
                                <i class="mdi mdi-wallet absolute -right-2 -bottom-2 text-6xl text-white/10 transform -rotate-12 pointer-events-none"></i>
                                <span class="block text-[11px] font-black text-blue-200 uppercase tracking-widest mb-1">Saldo Tersedia:</span>
                                <span class="text-2xl font-black tracking-tight">Rp {{ number_format($saldo_aktif, 0, ',', '.') }}</span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Nominal Penarikan (Rp)</label>
                                <div class="flex border border-slate-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-600 transition-all" id="inputContainer">
                                    <span class="bg-slate-100 px-4 py-3.5 text-slate-500 font-black border-r border-slate-200 text-lg">Rp</span>
                                    <input type="number" name="jumlah_payout" id="inputPayout" class="w-full bg-slate-50 px-4 py-3.5 text-lg font-black text-slate-900 outline-none" placeholder="Min. 50000" min="50000" max="{{ $saldo_aktif }}" required>
                                </div>
                                <p class="text-[11px] font-bold text-red-500 mt-2 hidden" id="errorMsg">Nominal ditarik melebihi saldo aktif Anda!</p>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex items-start gap-3">
                                <i class="mdi mdi-information text-blue-500 text-xl leading-none"></i>
                                <p class="text-xs font-medium text-slate-600 leading-relaxed m-0">Dana akan ditransfer ke <span class="font-bold text-slate-900">{{ $rekening->nama_bank }} ({{ $rekening->nomor_rekening }})</span> dalam waktu 1x24 jam kerja.</p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" onclick="closePayoutModal()" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-100 transition-colors">Batal</button>
                        <button type="button" id="btnSubmitPayout" {{ $perlu_isi_rekening ? 'disabled' : '' }} class="px-6 py-2.5 bg-slate-900 hover:bg-black text-white font-bold rounded-xl shadow-sm transition-all disabled:opacity-50">Ajukan Penarikan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const payoutModal = document.getElementById('payoutModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalPanel = document.getElementById('modalPanel');

    function openPayoutModal() {
        payoutModal.classList.remove('hidden');
        void payoutModal.offsetWidth;
        modalOverlay.classList.replace('opacity-0', 'opacity-100');
        modalPanel.classList.replace('opacity-0', 'opacity-100');
        modalPanel.classList.replace('scale-95', 'scale-100');
    }

    function closePayoutModal() {
        modalOverlay.classList.replace('opacity-100', 'opacity-0');
        modalPanel.classList.replace('opacity-100', 'opacity-0');
        modalPanel.classList.replace('scale-100', 'scale-95');
        setTimeout(() => payoutModal.classList.add('hidden'), 300);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputPayout = document.getElementById('inputPayout');
        const btnSubmit = document.getElementById('btnSubmitPayout');
        const errorMsg = document.getElementById('errorMsg');
        const maxSaldo = {{ $saldo_aktif }};

        if(inputPayout) {
            inputPayout.addEventListener('input', function() {
                let val = parseInt(this.value) || 0;
                if(val > maxSaldo) {
                    errorMsg.classList.remove('hidden');
                    btnSubmit.disabled = true;
                } else {
                    errorMsg.classList.add('hidden');
                    btnSubmit.disabled = false;
                }
            });
        }

        if(btnSubmit) {
            btnSubmit.addEventListener('click', function(e) {
                e.preventDefault();
                let val = parseInt(inputPayout.value) || 0;
                if(val < 50000) {
                    Swal.fire({title: 'Nominal Terlalu Kecil', text: 'Minimal penarikan adalah Rp 50.000', icon: 'info', customClass: { popup: 'rounded-3xl' }});
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Penarikan',
                    html: `Tarik dana sebesar <b>Rp ${new Intl.NumberFormat('id-ID').format(val)}</b> ke rekening terdaftar?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0f172a',
                    confirmButtonText: 'Ya, Tarik Dana',
                    customClass: { popup: 'rounded-3xl' }
                }).then((result) => {
                    if(result.isConfirmed) {
                        btnSubmit.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Memproses...';
                        btnSubmit.disabled = true;
                        document.getElementById('payoutForm').submit();
                    }
                });
            });
        }
    });
</script>
@endpush