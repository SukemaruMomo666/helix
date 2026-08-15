@extends('layouts.seller')

@section('title', 'Rekening Bank Toko')

@push('styles')
{{-- Plugin TomSelect untuk searchable Dropdown --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* Styling khusus TomSelect agar menyatu dengan Tailwind UI */
    .ts-wrapper { padding: 0 !important; border: none !important; }
    .ts-control { border-radius: 0.75rem !important; border: 1px solid #e2e8f0 !important; padding: 0.75rem 1rem !important; background-color: #f8fafc !important; font-size: 0.875rem !important; font-weight: 600 !important; color: #0f172a !important; box-shadow: none !important; transition: all 0.2s !important; }
    .ts-control.focus { border-color: #2563eb !important; background-color: #ffffff !important; box-shadow: 0 0 0 2px rgba(37,99,235,0.2) !important; }
    .ts-dropdown { border-radius: 0.75rem !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; font-size: 0.875rem !important; font-weight: 600 !important; padding: 0.5rem !important; }
    .ts-dropdown .option { border-radius: 0.5rem !important; padding: 0.5rem 1rem !important; }
    .ts-dropdown .option.active, .ts-dropdown .option:hover { background-color: #eff6ff !important; color: #2563eb !important; }
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
    @if($errors->any())
        <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({title: 'Gagal Menyimpan!', text: 'Periksa kembali isian formulir Anda.', icon: 'error', customClass: { popup: 'rounded-3xl' }}));</script>
    @endif

    {{-- 1. HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm flex-shrink-0">
            <i class="mdi mdi-bank text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Rekening Bank Toko</h1>
            <p class="text-sm font-medium text-slate-500 mt-0.5">Kelola rekening bank utama untuk menerima pencairan penghasilan toko Anda.</p>
        </div>
    </div>

    {{-- 2. SECURITY ALERT --}}
    <div class="bg-blue-50 border border-blue-200 border-l-4 border-l-blue-600 p-5 rounded-2xl flex items-start gap-4 shadow-sm mb-8">
        <i class="mdi mdi-shield-check text-2xl text-blue-600 leading-none"></i>
        <div>
            <h5 class="text-sm font-black text-blue-900 mb-1">Informasi Keamanan Dana</h5>
            <p class="text-xs font-medium text-blue-800 leading-relaxed m-0">
                Pastikan <b>Nama Pemilik Rekening</b> sama persis dengan identitas (KTP) pendaftar toko untuk mencegah penolakan transfer oleh sistem bank. Proses pencairan dana memakan waktu maksimal 1x24 jam kerja.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8">

            {{-- LOGIKA: TAMPILKAN REKENING ATAU TOMBOL TAMBAH --}}
            @if($toko->rekening_bank && $toko->nomor_rekening)

                {{-- KARTU REKENING AKTIF (Gaya Kartu ATM Mewah) --}}
                <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-300 relative overflow-hidden group">
                    {{-- Aksen Garis Biru Kiri --}}
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-600"></div>
                    {{-- Watermark Icon Latar --}}
                    <i class="mdi mdi-bank-transfer absolute -right-6 -bottom-6 text-9xl text-slate-50 opacity-50 transform -rotate-12 pointer-events-none group-hover:scale-110 transition-transform duration-500"></i>

                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">

                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 text-3xl flex-shrink-0">
                                <i class="mdi mdi-credit-card-outline"></i>
                            </div>
                            <div>
                                <h5 class="text-lg font-black text-slate-900 mb-0.5">{{ $toko->rekening_bank }}</h5>
                                <div class="font-mono text-xl font-black text-blue-600 tracking-[0.2em] mb-1">
                                    {{ implode(' ', str_split($toko->nomor_rekening, 4)) }}
                                </div>
                                <div class="text-xs font-bold text-slate-500 uppercase tracking-widest">
                                    A.N. {{ $toko->atas_nama_rekening }}
                                </div>
                                <div class="mt-3">
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 border border-emerald-200 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                        <i class="mdi mdi-check-circle text-xs"></i> Rekening Utama Aktif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-row md:flex-col gap-3 w-full md:w-auto mt-4 md:mt-0">
                            <button type="button" onclick="openBankModal('edit')" class="flex-1 md:flex-auto flex items-center justify-center gap-1.5 px-5 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-900 hover:text-white hover:border-slate-900 text-sm font-bold rounded-xl transition-colors">
                                <i class="mdi mdi-pencil-outline text-base"></i> Ubah
                            </button>

                            <form action="{{ route('seller.finance.bank.destroy') }}" method="POST" class="flex-1 md:flex-auto m-0 delete-bank-form">
                                @csrf
                                <button type="button" class="w-full flex items-center justify-center gap-1.5 px-5 py-2.5 bg-white border border-red-200 text-red-500 hover:bg-red-50 hover:border-red-300 text-sm font-bold rounded-xl transition-colors btn-delete-confirm">
                                    <i class="mdi mdi-trash-can-outline text-base"></i> Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            @else

                {{-- KARTU KOSONG (EMPTY STATE TAMBAH REKENING) --}}
                <button type="button" onclick="openBankModal('add')" class="w-full bg-slate-50 hover:bg-blue-50 border-2 border-dashed border-slate-300 hover:border-blue-400 rounded-3xl p-10 flex flex-col items-center justify-center text-slate-500 hover:text-blue-600 transition-all duration-300 group">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 group-hover:scale-110 transition-transform mb-4">
                        <i class="mdi mdi-plus-thick text-3xl"></i>
                    </div>
                    <span class="text-base font-black">Tambah Rekening Bank Utama</span>
                    <p class="text-xs font-medium text-slate-400 mt-2 text-center max-w-sm">Anda harus mendaftarkan rekening tujuan sebelum dapat mencairkan saldo penjualan.</p>
                </button>

            @endif

        </div>
    </div>
</div>

{{-- 3. TAILWIND CUSTOM MODAL --}}
<div id="bankModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Background Overlay Gelap --}}
    <div id="modalOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0 duration-300" aria-hidden="true" onclick="closeBankModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            {{-- Modal Panel --}}
            <div id="modalPanel" class="relative transform overflow-visible rounded-3xl bg-white text-left shadow-2xl transition-all opacity-0 scale-95 duration-300 w-full sm:max-w-lg border border-slate-200">

                <form action="{{ route('seller.finance.bank.update') }}" method="POST" id="formBank">
                    @csrf

                    {{-- Modal Header --}}
                    <div class="bg-slate-50 px-6 py-5 border-b border-slate-100 flex items-center justify-between rounded-t-3xl">
                        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2"><i class="mdi mdi-bank-plus text-blue-600"></i> Rincian Rekening</h3>
                        <button type="button" onclick="closeBankModal()" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-red-100 text-slate-500 hover:text-red-500 flex items-center justify-center transition-colors">
                            <i class="mdi mdi-close text-lg leading-none"></i>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-5">

                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Pilih Bank <span class="text-red-500">*</span></label>
                            {{-- Select untuk diubah oleh TomSelect JS --}}
                            <select id="bank-select" name="nama_bank" placeholder="Ketik nama bank..." required>
                                <option value="">Pilih Bank Tujuan...</option>
                                @foreach ($daftar_bank as $bank)
                                    <option value="{{ $bank }}" {{ ($toko->rekening_bank == $bank) ? 'selected' : '' }}>
                                        {{ $bank }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Nomor Rekening <span class="text-red-500">*</span></label>
                            <input type="text" name="no_rekening" id="no_rekening" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none placeholder-slate-400 font-mono tracking-wider" value="{{ $toko->nomor_rekening }}" placeholder="Contoh: 1234567890" pattern="[0-9]+" title="Hanya boleh berisi angka" required>
                            <p class="text-[10px] font-bold text-slate-400 mt-1.5">*Tanpa spasi atau tanda hubung (-).</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Pemilik Rekening <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_pemilik" id="nama_pemilik" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none placeholder-slate-400 uppercase" value="{{ $toko->atas_nama_rekening }}" placeholder="Sesuai buku tabungan / KTP" required>
                        </div>

                    </div>

                    {{-- Modal Footer --}}
                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 rounded-b-3xl">
                        <button type="button" onclick="closeBankModal()" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-100 transition-colors">Batal</button>
                        <button type="submit" id="btnSaveBank" class="w-full sm:w-auto px-6 py-2.5 bg-slate-900 hover:bg-black text-white font-bold rounded-xl shadow-sm shadow-slate-900/20 transition-all flex items-center justify-center gap-2">
                            <i class="mdi mdi-content-save"></i> Simpan Rekening
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Plugin TomSelect JS --}}
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    // --- 1. MODAL TAILWIND LOGIC ---
    const bankModal = document.getElementById('bankModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalPanel = document.getElementById('modalPanel');

    function openBankModal(mode) {
        if(mode === 'add') {
            document.getElementById('formBank').reset();
            // Reset TomSelect jika sudah diinisialisasi
            if(window.tsInstance) {
                window.tsInstance.clear();
            }
        }

        bankModal.classList.remove('hidden');
        void bankModal.offsetWidth; // Force Reflow
        modalOverlay.classList.replace('opacity-0', 'opacity-100');
        modalPanel.classList.replace('opacity-0', 'opacity-100');
        modalPanel.classList.replace('scale-95', 'scale-100');
    }

    function closeBankModal() {
        modalOverlay.classList.replace('opacity-100', 'opacity-0');
        modalPanel.classList.replace('opacity-100', 'opacity-0');
        modalPanel.classList.replace('scale-100', 'scale-95');
        setTimeout(() => bankModal.classList.add('hidden'), 300);
    }

    document.addEventListener('DOMContentLoaded', function() {

        // --- 2. INISIALISASI TOM-SELECT ---
        window.tsInstance = new TomSelect("#bank-select", {
            create: true, // Izinkan input bank yang tidak ada di list
            sortField: { field: "text", direction: "asc" },
            dropdownParent: 'body', // Cegah terpotong Z-index Modal
            placeholder: "Cari atau Ketik Nama Bank..."
        });

        // --- 3. LOADING STATE SAAT SUBMIT ---
        document.getElementById('formBank').addEventListener('submit', function() {
            let btn = document.getElementById('btnSaveBank');
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-lg leading-none"></i> Menyimpan...';
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
        });

        // --- 4. SWEETALERT HAPUS REKENING ---
        document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                let form = this.closest('.delete-bank-form');

                Swal.fire({
                    title: 'Hapus Rekening?',
                    text: "Pencairan penghasilan toko Anda tidak dapat dilakukan jika rekening utama dihapus.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', // Merah Tailwind
                    cancelButtonColor: '#64748b',  // Slate Tailwind
                    confirmButtonText: 'Ya, Hapus Rekening',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-3xl' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-base"></i> Memproses...';
                        btn.disabled = true;
                        form.submit();
                    }
                });
            });
        });

    });
</script>
@endpush
