@extends('layouts.admin')

@section('title', 'Pusat Resolusi & Komplain')

@push('styles')
<style>
    :root {
        --ds-bg: #f8fafc;
        --ds-border: #e2e8f0;
        --ds-danger: #e11d48;
        --ds-warning: #f59e0b;
        --ds-dark: #0f172a;
    }

    /* HEADER & STATS */
    .dispute-header { background: var(--ds-dark); border-radius: 16px; padding: 24px 30px; color: white; display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3); }
    .stat-badge { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); padding: 12px 20px; border-radius: 12px; text-align: center; }
    .stat-badge span { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 4px; }
    .stat-badge strong { font-size: 24px; color: white; line-height: 1; }

    /* CARD KASUS */
    .case-card { background: white; border: 1px solid var(--ds-border); border-radius: 16px; padding: 20px; margin-bottom: 16px; transition: 0.2s; position: relative; overflow: hidden; }
    .case-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: var(--ds-warning); }
    .case-card.selesai::before { background: #10b981; }
    .case-card.refund::before { background: var(--ds-danger); }

    .case-header { display: flex; justify-content: space-between; border-bottom: 1px dashed var(--ds-border); padding-bottom: 12px; margin-bottom: 16px; }
    .case-id { font-family: monospace; font-size: 16px; font-weight: 800; color: var(--ds-dark); }
    
    .party-box { padding: 12px; border-radius: 10px; background: var(--ds-bg); border: 1px solid var(--ds-border); height: 100%; }
    .party-title { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 8px; display: block; }
    
    /* BADGES */
    .badge-komplain { padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; }
    .bg-investigasi { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .bg-refund { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
    .bg-teruskan { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }

    .btn-judge { background: var(--ds-dark); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; transition: 0.2s; }
    .btn-judge:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    /* MODAL RUANG SIDANG */
    .court-modal .modal-content { border: none; border-radius: 20px; overflow: hidden; }
    .court-header { background: var(--ds-dark); color: white; padding: 20px 24px; border-bottom: 4px solid var(--ds-danger); }
    .evidence-img { width: 100%; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid var(--ds-border); cursor: pointer; transition: 0.2s; }
    .evidence-img:hover { opacity: 0.8; }
    .gavel-section { background: #f8fafc; border-top: 1px solid var(--ds-border); padding: 24px; border-radius: 0 0 20px 20px; }
    
    .decision-radio { display: none; }
    .decision-label { border: 2px solid var(--ds-border); border-radius: 12px; padding: 16px; cursor: pointer; display: flex; align-items: flex-start; gap: 12px; transition: 0.2s; background: white; }
    .decision-radio:checked + .decision-label.refund { border-color: var(--ds-danger); background: #fff1f2; }
    .decision-radio:checked + .decision-label.teruskan { border-color: #10b981; background: #ecfdf5; }
</style>
@endpush

@section('content')
<div class="dispute-header">
    <div>
        <h2 class="fw-bold mb-1"><i class="mdi mdi-gavel text-warning me-2"></i> Pusat Resolusi & Komplain</h2>
        <p class="mb-0 text-light" style="opacity: 0.8; font-size: 14px;">Mediasi dan selesaikan sengketa antara Pembeli dan Mitra Toko.</p>
    </div>
    <div class="d-flex gap-3">
        <div class="stat-badge">
            <span>Perlu Tindakan</span>
            <strong class="text-warning">{{ $stats['perlu_tindakan'] }}</strong>
        </div>
        <div class="stat-badge">
            <span>Kasus Selesai</span>
            <strong>{{ $stats['dana_dikembalikan'] + $stats['dana_diteruskan'] }}</strong>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success fw-bold border-0 shadow-sm rounded-3"><i class="mdi mdi-check-circle"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger fw-bold border-0 shadow-sm rounded-3"><i class="mdi mdi-alert"></i> {{ $errors->first() }}</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="btn-group shadow-sm bg-white p-1 rounded-3">
        <a href="{{ route('admin.disputes.index', ['status' => 'aktif']) }}" class="btn btn-sm border-0 {{ $status == 'aktif' ? 'bg-dark text-white fw-bold rounded-2' : 'text-muted' }} px-4">Kasus Aktif</a>
        <a href="{{ route('admin.disputes.index', ['status' => 'selesai']) }}" class="btn btn-sm border-0 {{ $status == 'selesai' ? 'bg-dark text-white fw-bold rounded-2' : 'text-muted' }} px-4">Riwayat Selesai</a>
    </div>
    <form action="{{ route('admin.disputes.index') }}" method="GET" style="width: 300px;">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-0"><i class="mdi mdi-magnify"></i></span>
            <input type="text" name="search" class="form-control border-0" placeholder="Cari Inv/Nama..." value="{{ $search }}">
        </div>
    </form>
</div>

@forelse($disputes as $d)
    @php
        $isSelesai = in_array($d->status_komplain, ['refund_pembeli', 'teruskan_dana_toko', 'selesai']);
        $cardClass = $d->status_komplain == 'refund_pembeli' ? 'refund' : ($isSelesai ? 'selesai' : '');
    @endphp
    
    <div class="case-card {{ $cardClass }}">
        <div class="case-header">
            <div>
                <span class="case-id">KMP-{{ str_pad($d->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="ms-3 text-primary fw-bold" style="font-family: monospace;">{{ $d->kode_invoice }}</span>
            </div>
            <div>
                <span class="text-muted small me-3"><i class="mdi mdi-clock-outline"></i> {{ \Carbon\Carbon::parse($d->created_at)->diffForHumans() }}</span>
                @if($d->status_komplain == 'investigasi' || $d->status_komplain == 'menunggu_tanggapan_toko')
                    <span class="badge-komplain bg-investigasi"><i class="mdi mdi-magnify"></i> Sedang Investigasi</span>
                @elseif($d->status_komplain == 'refund_pembeli')
                    <span class="badge-komplain bg-refund"><i class="mdi mdi-cash-refund"></i> Dana Direfund</span>
                @else
                    <span class="badge-komplain bg-teruskan"><i class="mdi mdi-check-decagram"></i> Dana ke Toko</span>
                @endif
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-md-3">
                <div class="party-box">
                    <span class="party-title">Pihak Penggugat (Pembeli)</span>
                    <strong class="d-block text-dark">{{ $d->nama_pembeli }}</strong>
                    <span class="small text-muted"><i class="mdi mdi-phone"></i> {{ $d->telepon_pembeli }}</span>
                </div>
            </div>
            <div class="col-md-1 text-center">
                <i class="mdi mdi-sword text-danger fs-3" style="opacity: 0.3;"></i>
            </div>
            <div class="col-md-3">
                <div class="party-box">
                    <span class="party-title">Pihak Tergugat (Toko)</span>
                    <strong class="d-block text-dark">{{ $d->nama_toko }}</strong>
                    <span class="small text-muted"><i class="mdi mdi-phone"></i> {{ $d->telepon_toko }}</span>
                </div>
            </div>
            <div class="col-md-5 d-flex justify-content-end align-items-center gap-4">
                <div class="text-end">
                    <span class="d-block text-muted small fw-bold text-uppercase">Nilai Sengketa</span>
                    <strong class="text-danger fs-5">Rp {{ number_format($d->total_final, 0, ',', '.') }}</strong>
                </div>
                @if(!$isSelesai)
                    <button class="btn-judge btn-sidang" 
                        data-bs-toggle="modal" data-bs-target="#modalSidang"
                        data-id="{{ $d->id }}"
                        data-inv="{{ $d->kode_invoice }}"
                        data-jenis="{{ str_replace('_', ' ', strtoupper($d->jenis_komplain)) }}"
                        data-alasan="{{ $d->alasan_komplain }}"
                        data-foto1="{{ $d->bukti_foto_1 ?? asset('assets/images/no-image.jpg') }}">
                        <i class="mdi mdi-gavel me-1"></i> Buka Sidang
                    </button>
                @else
                    <button class="btn btn-light border fw-bold px-4" disabled>Kasus Ditutup</button>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-5">
        <i class="mdi mdi-gavel text-muted" style="font-size: 5rem; opacity: 0.5;"></i>
        <h4 class="text-muted mt-3 fw-bold">Ruang Sidang Kosong</h4>
        <p class="text-muted">Tidak ada kasus sengketa atau komplain saat ini.</p>
    </div>
@endforelse

<div class="mt-3">
    {{ $disputes->links('pagination::bootstrap-5') }}
</div>

<div class="modal fade court-modal" id="modalSidang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="court-header">
                <h4 class="modal-title fw-bold mb-0"><i class="mdi mdi-scale-balance me-2"></i> Ruang Sidang Virtual</h4>
                <p class="mb-0 text-light mt-1" style="font-size: 13px;">Invoice: <span id="mdl-inv" class="fw-bold text-warning font-monospace"></span></p>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="formHakim" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="fw-bold text-danger text-uppercase mb-3 border-bottom pb-2">Tuntutan Pembeli</h6>
                            <div class="mb-3">
                                <span class="badge bg-danger mb-2" id="mdl-jenis"></span>
                                <p class="text-dark bg-light p-3 border rounded-3" id="mdl-alasan" style="font-size: 14px;"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-bold text-muted text-uppercase mb-3 border-bottom pb-2">Bukti Lampiran</h6>
                            <img src="" id="mdl-foto1" class="evidence-img shadow-sm" alt="Bukti Foto">
                            <small class="text-muted d-block mt-2 text-center"><i class="mdi mdi-magnify-plus-outline"></i> Klik untuk perbesar</small>
                        </div>
                    </div>
                </div>

                <div class="gavel-section">
                    <h6 class="fw-bold text-dark text-uppercase mb-3"><i class="mdi mdi-gavel text-warning fs-5"></i> Keputusan Hakim (Admin)</h6>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <input type="radio" name="keputusan" value="refund_pembeli" id="kep1" class="decision-radio" required>
                            <label class="decision-label refund" for="kep1">
                                <i class="mdi mdi-cash-refund fs-3 text-danger"></i>
                                <div>
                                    <strong class="d-block text-dark">Menangkan Pembeli</strong>
                                    <span class="small text-muted">Batalkan pesanan, dana akan dikembalikan ke dompet pembeli.</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="radio" name="keputusan" value="teruskan_dana_toko" id="kep2" class="decision-radio" required>
                            <label class="decision-label teruskan" for="kep2">
                                <i class="mdi mdi-store-check fs-3 text-success"></i>
                                <div>
                                    <strong class="d-block text-dark">Menangkan Toko</strong>
                                    <span class="small text-muted">Tolak komplain pembeli, pesanan dianggap selesai, teruskan dana ke toko.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold text-dark small mb-2">Catatan Putusan <span class="text-danger">*</span></label>
                        <textarea name="keputusan_admin" class="form-control" rows="3" placeholder="Contoh: Berdasarkan bukti foto, terlihat jelas keramik pecah karena kelalaian packing toko. Dana di-refund ke pembeli." required></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light p-4">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Tunda Sidang</button>
                    <button type="submit" class="btn btn-dark px-4 fw-bold"><i class="mdi mdi-gavel"></i> Ketuk Palu Putusan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Trigger Modal Ruang Sidang
        document.querySelectorAll('.btn-sidang').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                
                // Set konten modal
                document.getElementById('mdl-inv').innerText = this.getAttribute('data-inv');
                document.getElementById('mdl-jenis').innerText = this.getAttribute('data-jenis');
                document.getElementById('mdl-alasan').innerText = this.getAttribute('data-alasan');
                
                // Set gambar (Jika Anda menggunakan path storage public, sesuaikan)
                const fotoPath = this.getAttribute('data-foto1');
                document.getElementById('mdl-foto1').src = fotoPath;
                
                // Set Form Action
                document.getElementById('formHakim').action = `/portal-rahasia-pks/disputes/${id}/resolve`;
            });
        });
    });
</script>
@endpush