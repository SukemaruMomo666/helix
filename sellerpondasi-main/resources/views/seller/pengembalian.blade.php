@extends('layouts.seller')

@section('title', 'Pusat Resolusi Komplain')

@push('styles')
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 p-4 md:p-6 lg:p-8 font-sans text-slate-900 space-y-6">

    @if(session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({title: 'Berhasil!', text: '{{ session('success') }}', icon: 'success', customClass: { popup: 'rounded-3xl' }}));</script>
    @endif
    @if(session('error'))
        <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({title: 'Gagal!', text: '{{ session('error') }}', icon: 'error', customClass: { popup: 'rounded-3xl' }}));</script>
    @endif

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-800 shadow-sm flex-shrink-0">
            <i class="mdi mdi-keyboard-return text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pusat Resolusi Komplain</h1>
            <p class="text-sm font-medium text-slate-500">Kelola permintaan retur material dan pengembalian dana dari pembeli.</p>
        </div>
    </div>

    {{-- TABS FILTER --}}
    <div class="flex gap-2 overflow-x-auto pb-2 hide-scrollbar border-b border-slate-200">
        <a href="?status=" class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all {{ $currentFilter == '' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/20' : 'bg-transparent text-slate-500 hover:bg-slate-200 hover:text-slate-900' }}">Semua Komplain</a>
        <a href="?status=menunggu_respon" class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all {{ $currentFilter == 'menunggu_respon' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/20' : 'bg-transparent text-slate-500 hover:bg-slate-200 hover:text-slate-900' }}">Perlu Ditinjau</a>
        <a href="?status=disetujui" class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all {{ $currentFilter == 'disetujui' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/20' : 'bg-transparent text-slate-500 hover:bg-slate-200 hover:text-slate-900' }}">Disetujui (Refund)</a>
        <a href="?status=ditolak" class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all {{ $currentFilter == 'ditolak' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/20' : 'bg-transparent text-slate-500 hover:bg-slate-200 hover:text-slate-900' }}">Ditolak</a>
    </div>

    {{-- KONTEN PENGEMBALIAN --}}
    @if(empty($returns) || count($returns) == 0)
        <div class="bg-white border border-slate-200 rounded-3xl py-20 px-6 text-center shadow-sm flex flex-col items-center justify-center">
            <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center mb-6">
                <i class="mdi mdi-shield-check-outline text-5xl text-emerald-400"></i>
            </div>
            <h4 class="text-xl font-black text-slate-900 mb-2">Toko Anda Sangat Aman!</h4>
            <p class="text-sm font-medium text-slate-500 max-w-md">Tidak ada keluhan, retur, atau permintaan pengembalian dana saat ini.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($returns as $ret)
                @php
                    $badgeClass = 'bg-amber-50 text-amber-600 border-amber-200'; $statusText = 'Perlu Keputusan';
                    if($ret->status == 'disetujui') { $badgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-200'; $statusText = 'Selesai (Direfund)'; }
                    if($ret->status == 'ditolak') { $badgeClass = 'bg-red-50 text-red-600 border-red-200'; $statusText = 'Ditolak'; }
                @endphp

                <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-300">

                    {{-- Header Card --}}
                    <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-mono font-black text-blue-600 flex items-center gap-1.5"><i class="mdi mdi-ticket-confirmation-outline text-lg"></i>{{ $ret->id_return }}</span>
                            <div class="hidden sm:block w-1.5 h-1.5 bg-slate-300 rounded-full"></div>
                            <span class="text-sm font-bold text-slate-700 flex items-center gap-1.5"><i class="mdi mdi-receipt-text text-slate-400 text-lg"></i>{{ $ret->kode_invoice }}</span>
                            <div class="hidden sm:block w-1.5 h-1.5 bg-slate-300 rounded-full"></div>
                            <span class="text-xs font-bold text-slate-500 flex items-center gap-1.5"><i class="mdi mdi-calendar-clock text-slate-400 text-lg"></i>{{ date('d M Y, H:i', strtotime($ret->tanggal_pengajuan)) }}</span>
                        </div>
                        <span class="px-3 py-1.5 rounded-lg border text-xs font-black uppercase tracking-wide {{ $badgeClass }}">{{ $statusText }}</span>
                    </div>

                    {{-- Body Card (Grid) --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">

                        {{-- KIRI: Info Produk --}}
                        <div class="p-6">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Rincian Material & Pembeli</span>

                            <div class="flex items-start gap-4 mb-5">
                                <img src="{{ asset('assets/uploads/products/' . $ret->gambar_utama) }}" class="w-20 h-20 rounded-xl object-cover border border-slate-200 flex-shrink-0" onerror="this.src='https://placehold.co/100x100?text=No+Img'">
                                <div>
                                    <h6 class="text-base font-bold text-slate-900 leading-snug mb-2">{{ $ret->nama_barang }}</h6>
                                    <span class="inline-flex items-center bg-slate-100 text-slate-600 text-xs font-bold px-2.5 py-1 rounded-md border border-slate-200 mb-2">Qty: {{ $ret->jumlah }} Pcs</span>
                                    <div class="text-xs font-medium text-slate-500 flex items-center gap-1.5"><i class="mdi mdi-account-hard-hat text-lg"></i>Pembeli: <strong class="text-slate-900">{{ $ret->nama_pelanggan }}</strong></div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-600">Tuntutan Pengembalian Dana</span>
                                <span class="text-lg font-black text-red-500">Rp {{ number_format($ret->total_pengembalian, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- KANAN: Alasan & Bukti --}}
                        <div class="p-6">
                            <div class="bg-orange-50 border border-orange-200/60 border-dashed rounded-2xl p-5 h-full flex flex-col">
                                <span class="flex items-center gap-2 text-[10px] font-black text-orange-600 uppercase tracking-widest mb-3">
                                    <i class="mdi mdi-alert-circle-outline text-lg"></i> Kendala Pelanggan
                                </span>

                                <div class="text-sm text-orange-900 italic leading-relaxed mb-4 flex-grow">"{{ $ret->alasan }}"</div>

                                <div class="pt-4 border-t border-orange-200/50">
                                    <span class="block text-xs font-bold text-slate-700 mb-3">Bukti Foto / Unboxing:</span>
                                    <div class="flex gap-3">
                                        <img src="{{ asset('assets/uploads/returns/' . $ret->bukti_foto) }}" class="w-16 h-16 rounded-xl object-cover border border-slate-300 cursor-pointer hover:scale-105 hover:border-blue-500 hover:shadow-md transition-all" onclick="showProofImage(this.src)" onerror="this.src='https://placehold.co/80x80?text=Bukti'" title="Klik untuk perbesar">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Footer Actions --}}
                    <div class="bg-white px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <button type="button" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-900 hover:text-white hover:border-slate-900 rounded-xl text-sm font-bold transition-colors">
                            <i class="mdi mdi-forum-outline text-lg leading-none"></i> Diskusi Pembeli
                        </button>

                        @if($ret->status == 'menunggu_respon')
                            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                <form action="{{ route('seller.orders.return.process') }}" method="POST" class="m-0 w-full sm:w-auto">
                                    @csrf
                                    <input type="hidden" name="id_return" value="{{ $ret->id_return }}">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="button" class="btn-action-reject w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-red-500 text-red-500 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm font-bold transition-colors">
                                        <i class="mdi mdi-close-octagon text-lg leading-none"></i> Tolak Komplain
                                    </button>
                                </form>

                                <form action="{{ route('seller.orders.return.process') }}" method="POST" class="m-0 w-full sm:w-auto">
                                    @csrf
                                    <input type="hidden" name="id_return" value="{{ $ret->id_return }}">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="button" class="btn-action-approve w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-500 border border-emerald-500 text-white hover:bg-emerald-600 hover:border-emerald-600 rounded-xl text-sm font-bold shadow-sm shadow-emerald-500/20 transition-all">
                                        <i class="mdi mdi-check-decagram text-lg leading-none"></i> Setujui & Refund
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showProofImage(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Bukti Komplain Pembeli',
            showConfirmButton: false,
            showCloseButton: true,
            background: 'transparent',
            backdrop: 'rgba(15, 23, 42, 0.95)',
            customClass: { popup: 'rounded-3xl' }
        });
    }

    document.querySelectorAll('.btn-action-reject').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            let form = this.closest('form');
            Swal.fire({
                title: 'Tolak Komplain?',
                text: "Apakah Anda yakin menolak klaim ini? Pastikan Anda telah berdiskusi dengan pembeli mengenai kendala material.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-3xl' }
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    document.querySelectorAll('.btn-action-approve').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            let form = this.closest('form');
            Swal.fire({
                title: 'Setujui Refund?',
                html: "Dana transaksi ini akan <b>dikembalikan sepenuhnya ke pembeli</b>. Keputusan ini final dan uang akan dipotong dari estimasi saldo Anda.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Setujui Refund',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-3xl' }
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
</script>
@endpush
