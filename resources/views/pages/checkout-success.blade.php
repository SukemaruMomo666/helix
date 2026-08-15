@extends('layouts.app')
@section('title', 'Pembayaran - ' . $transaksi->kode_invoice)

@section('content')
<div class="bg-slate-50 dark:bg-slate-900 min-h-screen pt-24 pb-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">
        
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-10 shadow-xl shadow-emerald-500/5 border border-slate-100 dark:border-slate-700 text-center relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl -ml-20 -mb-20"></div>

            <div class="relative z-10">
                <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check-circle text-4xl"></i>
                </div>
                
                <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mb-2">Checkout Berhasil!</h1>
                <p class="text-slate-500 dark:text-slate-400 mb-8">Selesaikan pembayaran Anda agar pesanan segera diproses.</p>

                <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 mb-8 text-left">
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-slate-200 dark:border-slate-700">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">ID Tagihan</p>
                            <p class="font-black text-slate-800 dark:text-white">{{ $transaksi->kode_invoice }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Bayar</p>
                            <p class="font-black text-xl text-emerald-500">Rp{{ number_format($transaksi->total_final, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if($paymentGateway === 'midtrans')
                        <div class="text-center py-4">
                            <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">Silakan klik tombol di bawah untuk membuka halaman pembayaran Midtrans yang aman.</p>
                            <button id="pay-button" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg shadow-emerald-500/30 flex items-center justify-center gap-2">
                                <i class="fas fa-lock"></i> Bayar Sekarang
                            </button>
                        </div>
                    @else
                        <!-- QRIS DINAMIS -->
                        <div class="text-center">
                            <div class="inline-block p-4 bg-white rounded-2xl shadow-sm border border-slate-200 mb-4">
                                <div id="qrcode" class="mx-auto flex justify-center"></div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Buka aplikasi DANA, GoPay, OVO, ShopeePay, atau m-Banking Anda, lalu scan QRIS di atas.<br>Nominal <b>Rp{{ number_format($transaksi->total_final, 0, ',', '.') }}</b> akan terisi otomatis.</p>
                            
                            @php
                                $waText = "Halo Admin, saya sudah membayar pesanan dengan ID " . $transaksi->kode_invoice . " sebesar Rp" . number_format($transaksi->total_final, 0, ',', '.') . ". Mohon segera diproses ya.";
                                $waUrl = "https://wa.me/" . $waAdmin . "?text=" . urlencode($waText);
                            @endphp
                            <a href="{{ $waUrl }}" target="_blank" class="w-full bg-[#25D366] hover:bg-[#128C7E] text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg shadow-[#25D366]/30 flex items-center justify-center gap-2">
                                <i class="fab fa-whatsapp text-lg"></i> Cek Status Pembayaran (Konfirmasi WA)
                            </a>
                        </div>
                    @endif
                </div>

                <a href="{{ route('pesanan.index') }}" class="text-sm font-bold text-slate-500 hover:text-emerald-500 transition-colors">
                    Lihat Daftar Pesanan Saya
                </a>
            </div>
        </div>

    </div>
</div>

@if($paymentGateway === 'midtrans' && $transaksi->snap_token)
    @php
        $midtransSettings = DB::table('tb_pengaturan')->whereIn('setting_nama', ['midtrans_client_key', 'midtrans_is_production'])->pluck('setting_nilai', 'setting_nama');
        $clientKey = $midtransSettings['midtrans_client_key'] ?? '';
        $isProduction = ($midtransSettings['midtrans_is_production'] ?? '0') == '1';
        $snapJsUrl = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp
    <script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        document.getElementById('pay-button').onclick = function () {
            snap.pay('{{ $transaksi->snap_token }}', {
                onSuccess: function(result) {
                    window.location.href = "{{ route('pesanan.index') }}";
                },
                onPending: function(result) {
                    window.location.href = "{{ route('pesanan.index') }}";
                },
                onError: function(result) {
                    alert('Gagal memproses pembayaran!');
                },
                onClose: function () {
                    // Closed popup
                }
            });
        };
    </script>
@elseif($paymentGateway === 'qris_dinamis' && $transaksi->snap_token)
    <!-- QR Code Generator JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // snap_token for QRIS stores the dynamic QRIS string
        const qrisString = "{{ $transaksi->snap_token }}";
        new QRCode(document.getElementById("qrcode"), {
            text: qrisString,
            width: 250,
            height: 250,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M
        });
    </script>
@endif

@endsection
