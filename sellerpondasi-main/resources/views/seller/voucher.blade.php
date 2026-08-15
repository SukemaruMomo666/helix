@extends('layouts.seller')

@section('title', 'Manajemen Voucher Toko')

@push('styles')
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
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
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl flex items-center gap-3 font-bold shadow-sm">
            <i class="mdi mdi-alert-circle text-xl"></i> Form tidak valid. Periksa kembali isian Anda.
        </div>
    @endif

    {{-- 1. HEADER & STATS --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm flex-shrink-0">
                <i class="mdi mdi-ticket-percent-outline text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Voucher</h1>
                <p class="text-sm font-medium text-slate-500 mt-0.5">Buat kode kupon potongan harga khusus untuk pelanggan setia Anda.</p>
            </div>
        </div>

        <div class="flex bg-white border border-slate-200 rounded-2xl p-2 shadow-sm w-full xl:w-auto overflow-x-auto hide-scrollbar">
            <div class="px-6 py-2 border-r border-slate-100 min-w-[140px]">
                <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Voucher Aktif</h6>
                <h3 class="text-xl font-black text-blue-600">{{ $stats['aktif'] ?? 0 }}</h3>
            </div>
            <div class="px-6 py-2 min-w-[140px]">
                <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Diklaim</h6>
                <h3 class="text-xl font-black text-slate-800">{{ $stats['terpakai'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    {{-- 2. TABS & TOOLBAR --}}
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden flex flex-col">

        <div class="flex gap-6 px-6 pt-4 border-b border-slate-100 overflow-x-auto hide-scrollbar bg-slate-50/50">
            <a href="?tab=semua" class="pb-3 text-sm font-bold whitespace-nowrap transition-colors border-b-2 {{ $currentTab == 'semua' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Semua Voucher</a>
            <a href="?tab=aktif" class="pb-3 text-sm font-bold whitespace-nowrap transition-colors border-b-2 {{ $currentTab == 'aktif' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Sedang Berjalan</a>
            <a href="?tab=habis" class="pb-3 text-sm font-bold whitespace-nowrap transition-colors border-b-2 {{ $currentTab == 'habis' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Kuota Habis</a>
            <a href="?tab=nonaktif" class="pb-3 text-sm font-bold whitespace-nowrap transition-colors border-b-2 {{ $currentTab == 'nonaktif' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Berakhir / Nonaktif</a>
        </div>

        <div class="p-4 sm:p-6 border-b border-slate-100 bg-white flex flex-col md:flex-row gap-4 justify-between">
            <form action="{{ route('seller.promotion.vouchers') }}" method="GET" class="relative w-full md:max-w-md group m-0">
                <input type="hidden" name="tab" value="{{ $currentTab }}">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="mdi mdi-magnify text-slate-400 group-focus-within:text-blue-600 transition-colors text-lg"></i>
                </div>
                <input type="text" name="search" placeholder="Cari Kode atau Nama Voucher..." value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:bg-white outline-none transition-all shadow-sm">
            </form>

            <button type="button" onclick="openVoucherModal()" class="w-full md:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm shadow-blue-600/20 transition-all flex-shrink-0">
                <i class="mdi mdi-plus-thick text-lg leading-none"></i> Buat Voucher Baru
            </button>
        </div>

        {{-- 3. TABEL VOUCHER --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Kode & Rincian</th>
                        <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Skema Diskon</th>
                        <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Syarat</th>
                        <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap min-w-[150px]">Pemakaian Kuota</th>
                        <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap text-center">Status</th>
                        <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($voucher_list as $vch)
                        @php
                            $isActive = $vch->status == 'AKTIF' && strtotime($vch->tanggal_berakhir) >= time();
                            $isHabis = $vch->kuota_terpakai >= $vch->kuota;
                            $badgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-200'; $statusText = 'Aktif';

                            if($isHabis) { $badgeClass = 'bg-slate-100 text-slate-600 border-slate-300'; $statusText = 'Habis'; $isActive = false; }
                            elseif(!$isActive || $vch->status == 'TIDAK_AKTIF') { $badgeClass = 'bg-red-50 text-red-600 border-red-200'; $statusText = 'Nonaktif'; }

                            $progress = min(100, ($vch->kuota_terpakai / max(1, $vch->kuota)) * 100);
                            $progColor = $progress >= 90 ? '#ef4444' : ($progress >= 60 ? '#f59e0b' : '#3b82f6');
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors group">

                            <td class="py-4 px-6">
                                <div class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 px-2.5 py-1 rounded-lg font-mono font-black text-sm mb-2 cursor-pointer hover:bg-amber-100" title="Copy Kode" onclick="navigator.clipboard.writeText('{{ $vch->kode_voucher }}'); Toast.fire({icon: 'success', title: 'Kode disalin!'})">
                                    <i class="mdi mdi-content-copy"></i> {{ $vch->kode_voucher }}
                                </div>
                                <div class="text-sm font-bold text-slate-800 line-clamp-2 leading-snug mb-1.5" title="{{ $vch->deskripsi }}">{{ $vch->deskripsi }}</div>
                                <div class="text-[11px] font-bold text-slate-400 flex items-center gap-1.5">
                                    <i class="mdi mdi-clock-outline"></i> {{ date('d M', strtotime($vch->tanggal_mulai)) }} - {{ date('d M Y', strtotime($vch->tanggal_berakhir)) }}
                                </div>
                            </td>

                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($vch->tipe_diskon == 'PERSEN')
                                    <div class="text-base font-black text-blue-600">Diskon {{ $vch->nilai_diskon }}%</div>
                                    <div class="text-[10px] font-black text-slate-400 mt-0.5 uppercase tracking-widest">Maks Rp {{ number_format($vch->maks_diskon, 0, ',', '.') }}</div>
                                @else
                                    <div class="text-base font-black text-blue-600">Rp {{ number_format($vch->nilai_diskon, 0, ',', '.') }}</div>
                                    <div class="text-[10px] font-black text-slate-400 mt-0.5 uppercase tracking-widest">Potongan Langsung</div>
                                @endif
                            </td>

                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="text-[11px] font-bold text-slate-500 mb-0.5">Min. Belanja</div>
                                <div class="text-sm font-black text-slate-800">Rp {{ number_format($vch->min_pembelian, 0, ',', '.') }}</div>
                            </td>

                            <td class="py-4 px-6">
                                <div class="w-full bg-slate-100 rounded-full h-2 mb-1.5 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%; background-color: {{ $progColor }};"></div>
                                </div>
                                <div class="flex justify-between text-[10px] font-bold text-slate-500">
                                    <span>{{ $vch->kuota_terpakai }} Terpakai</span>
                                    <span>{{ $vch->kuota }} Total</span>
                                </div>
                            </td>

                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-lg border text-[10px] font-black uppercase tracking-widest {{ $badgeClass }}">{{ $statusText }}</span>
                            </td>

                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-4">
                                    @if(!$isHabis)
                                        <label class="relative inline-flex items-center cursor-pointer" title="{{ $vch->status == 'AKTIF' ? 'Matikan Voucher' : 'Aktifkan Voucher' }}">
                                            <input type="checkbox" class="sr-only peer toggle-status" data-id="{{ $vch->id }}" {{ $vch->status == 'AKTIF' ? 'checked' : '' }}>
                                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                                        </label>
                                    @endif

                                    <form action="{{ route('seller.promotion.vouchers.destroy', $vch->id) }}" method="POST" class="m-0 form-delete">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="button" class="text-slate-400 hover:text-red-500 transition-transform hover:scale-110 btn-delete-confirm" title="Hapus Permanen">
                                            <i class="mdi mdi-trash-can-outline text-xl leading-none"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>    
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center opacity-60">
                                    <i class="mdi mdi-ticket-outline text-6xl text-slate-300 mb-4"></i>
                                    <h5 class="text-lg font-black text-slate-800 mb-1">Data Voucher Kosong</h5>
                                    <p class="text-sm font-medium text-slate-500">Tidak ada data yang cocok dengan kriteria filter Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($voucher_list->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center lg:justify-end">
                {{ $voucher_list->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

{{-- MODAL TAILWIND BUAT VOUCHER --}}
<div id="modalAddVoucher" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div id="modalOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0 duration-300" onclick="closeVoucherModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="modalPanel" class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all opacity-0 scale-95 duration-300 w-full sm:max-w-2xl border border-slate-200">

                <form action="{{ route('seller.promotion.vouchers.store') }}" method="POST">
                    @csrf

                    <div class="bg-slate-50 px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2"><i class="mdi mdi-ticket-percent text-blue-600"></i> Buat Voucher Baru</h3>
                        <button type="button" onclick="closeVoucherModal()" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-red-100 text-slate-500 hover:text-red-500 flex items-center justify-center transition-colors">
                            <i class="mdi mdi-close text-lg leading-none"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto hide-scrollbar">

                        {{-- Info Dasar --}}
                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Kode Voucher <span class="text-red-500">*</span></label>
                                    <input type="text" name="kode_voucher" class="w-full bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-600 outline-none uppercase" placeholder="Cth: TOKOHEMAT99" maxlength="12" required>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1">Maks. 12 Karakter (Tanpa Spasi).</p>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Total Kuota (Pcs) <span class="text-red-500">*</span></label>
                                    <input type="number" name="kuota" class="w-full bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-600 outline-none" placeholder="Cth: 100" min="1" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Nama / Deskripsi Promo <span class="text-red-500">*</span></label>
                                <input type="text" name="deskripsi" class="w-full bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-600 outline-none" placeholder="Cth: Diskon Akhir Tahun Khusus Kontraktor" required>
                            </div>
                        </div>

                        {{-- Pengaturan Diskon --}}
                        <div>
                            <h6 class="text-sm font-black text-slate-800 mb-3 flex items-center gap-2"><i class="mdi mdi-calculator text-amber-500"></i> Pengaturan Nilai Diskon</h6>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Tipe Diskon <span class="text-red-500">*</span></label>
                                    <select name="tipe_diskon" id="tipe_diskon" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none cursor-pointer" required>
                                        <option value="RUPIAH">Potongan Nominal (Rp)</option>
                                        <option value="PERSEN">Potongan Persentase (%)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Nilai Diskon <span class="text-red-500">*</span></label>
                                    <div class="flex border border-slate-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-600 focus-within:border-blue-600 transition-all">
                                        <span id="symbol_diskon" class="bg-slate-100 px-4 py-3 text-slate-500 font-black border-r border-slate-200">Rp</span>
                                        <input type="number" name="nilai_diskon" id="nilai_diskon" class="w-full bg-slate-50 px-4 py-3 text-sm font-bold outline-none" placeholder="0" min="1" required>
                                    </div>
                                </div>

                                <div id="box_maks_diskon" class="hidden">
                                    <label class="block text-[11px] font-black text-red-500 uppercase tracking-widest mb-1.5">Maks Potongan (Rp) <span class="text-red-500">*</span></label>
                                    <div class="flex border border-red-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-red-500 transition-all">
                                        <span class="bg-red-50 px-4 py-3 text-red-500 font-black border-r border-red-200">Rp</span>
                                        <input type="number" name="maks_diskon" id="maks_diskon" class="w-full bg-white px-4 py-3 text-sm font-bold outline-none" placeholder="Cth: 50000">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Minimal Belanja (Rp) <span class="text-red-500">*</span></label>
                                    <div class="flex border border-slate-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-600 transition-all">
                                        <span class="bg-slate-100 px-4 py-3 text-slate-500 font-black border-r border-slate-200">Rp</span>
                                        <input type="number" name="min_pembelian" class="w-full bg-slate-50 px-4 py-3 text-sm font-bold outline-none" placeholder="Cth: 100000" min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Periode --}}
                        <div>
                            <h6 class="text-sm font-black text-slate-800 mb-3 flex items-center gap-2"><i class="mdi mdi-calendar-clock text-indigo-500"></i> Periode Voucher Aktif</h6>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Waktu Mulai <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="tanggal_mulai" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Waktu Berakhir <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="tanggal_berakhir" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none" required>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 rounded-b-3xl">
                        <button type="button" onclick="closeVoucherModal()" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-100 transition-colors">Batal</button>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm shadow-blue-600/20 transition-all flex items-center justify-center gap-2">
                            <i class="mdi mdi-check-circle-outline"></i> Terbitkan Voucher
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- 1. MODAL LOGIC ---
    const modalAdd = document.getElementById('modalAddVoucher');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalPanel = document.getElementById('modalPanel');

    function openVoucherModal() {
        modalAdd.classList.remove('hidden');
        void modalAdd.offsetWidth;
        modalOverlay.classList.replace('opacity-0', 'opacity-100');
        modalPanel.classList.replace('opacity-0', 'opacity-100');
        modalPanel.classList.replace('scale-95', 'scale-100');
    }

    function closeVoucherModal() {
        modalOverlay.classList.replace('opacity-100', 'opacity-0');
        modalPanel.classList.replace('opacity-100', 'opacity-0');
        modalPanel.classList.replace('scale-100', 'scale-95');
        setTimeout(() => modalAdd.classList.add('hidden'), 300);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- 2. DYNAMIC FORM LOGIC ---
        const tipeDiskon = document.getElementById('tipe_diskon');
        const symbolDiskon = document.getElementById('symbol_diskon');
        const inputNilai = document.getElementById('nilai_diskon');
        const boxMaks = document.getElementById('box_maks_diskon');
        const inputMaks = document.getElementById('maks_diskon');

        tipeDiskon.addEventListener('change', function() {
            if (this.value === 'PERSEN') {
                symbolDiskon.textContent = '%';
                inputNilai.max = "100";
                inputNilai.placeholder = "Cth: 10";
                boxMaks.classList.remove('hidden');
                inputMaks.setAttribute('required', 'required');
            } else {
                symbolDiskon.textContent = 'Rp';
                inputNilai.removeAttribute('max');
                inputNilai.placeholder = "Cth: 50000";
                boxMaks.classList.add('hidden');
                inputMaks.removeAttribute('required');
                inputMaks.value = '';
            }
        });

        // --- 3. AJAX TOGGLE STATUS ---
        document.querySelectorAll('.toggle-status').forEach(toggle => {
            toggle.addEventListener('change', function() {
                let voucherId = this.dataset.id;
                let isActive = this.checked ? 1 : 0;
                let checkbox = this;

                checkbox.disabled = true; // Hindari spam klik

                fetch("{{ route('seller.promotion.vouchers.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ voucher_id: voucherId, is_active: isActive })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        Toast.fire({icon: 'success', title: isActive ? 'Voucher diaktifkan' : 'Voucher dinonaktifkan'})
                        .then(() => location.reload());
                    } else throw new Error();
                })
                .catch(() => {
                    Toast.fire({icon: 'error', title: 'Gagal update status!'});
                    checkbox.checked = !isActive;
                })
                .finally(() => checkbox.disabled = false);
            });
        });

        // --- 4. HAPUS VOUCHER ---
        document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                let form = this.closest('.form-delete');
                Swal.fire({
                    title: 'Hapus Voucher?',
                    text: "Voucher ini akan dihapus permanen. Pembeli yang sudah mengklaim mungkin tidak bisa menggunakannya lagi.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Hapus!',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-3xl' }
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    });
</script>
@endpush
