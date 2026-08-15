<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Toko extends Model
{
    use HasFactory;

    // 1. Konfigurasi Tabel
    protected $table = 'tb_toko';

    // PENTING: Gunakan $fillable agar sinkron dengan Toko::create() di AuthController
    protected $fillable = [
        'user_id',
        'nama_toko',
        'slug',
        'deskripsi_toko',
        'logo_toko',
        'banner_toko',
        'alamat_toko',
        'province_id',
        'city_id',
        'district_id',
        'kode_pos',
        'telepon_toko',
        'latitude',      // Persiapan jika nanti ada maps
        'longitude',     // Persiapan jika nanti ada maps
        'status',        // active, pending, suspended
        'status_operasional', // Buka, Tutup
        'layout_data'    // <-- DITAMBAHKAN UNTUK MENYIMPAN JSON DEKORASI TOKO
    ];

    // KUNCI DEWA: Beritahu Laravel bahwa layout_data adalah Array/JSON
    protected $casts = [
        'layout_data' => 'array',
    ];

    // 2. Agar atribut tambahan ini otomatis muncul saat data diubah jadi JSON
    protected $appends = ['banner_url', 'logo_url', 'initials', 'calculated_color'];

    // ================= RELASI (Hubungan Antar Tabel) =================

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function barang()
    {
        return $this->hasMany(Barang::class, 'toko_id', 'id');
    }

    // Relasi ke Wilayah (Pastikan Model Province/City/District sudah ada)
    // Jika belum ada modelnya, relasi ini tidak akan error selama tidak dipanggil
    public function province()
    {
        return $this->belongsTo(\App\Models\Province::class, 'province_id');
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\City::class, 'city_id');
    }

    public function district()
    {
        return $this->belongsTo(\App\Models\District::class, 'district_id');
    }

    // ================= ACCESSOR (Logika Tampilan) =================
    // Fitur ini tidak diubah karena sudah benar dan berguna untuk frontend

    /**
     * 1. URL Banner
     */
    public function getBannerUrlAttribute()
    {
        // Sesuaikan folder upload dengan AuthController (assets/uploads/...)
        $relativePath = 'assets/uploads/banners/' . $this->banner_toko;

        if (!empty($this->banner_toko) && File::exists(public_path($relativePath))) {
            return asset($relativePath);
        }

        // Return gambar default atau null
        return null;
    }

    /**
     * 2. URL Logo
     */
    public function getLogoUrlAttribute()
    {
        // Sesuaikan folder upload dengan AuthController (assets/uploads/logos)
        $relativePath = 'assets/uploads/logos/' . $this->logo_toko;

        if (!empty($this->logo_toko) && File::exists(public_path($relativePath))) {
            return asset($relativePath);
        }

        return null;
    }

    /**
     * 3. Inisial Nama Toko
     */
    public function getInitialsAttribute()
    {
        $nama = $this->nama_toko ?? 'Toko';
        if (empty($nama)) return "TK";

        $words = explode(" ", $nama);
        $acronym = "";

        foreach ($words as $w) {
            $acronym .= mb_substr($w, 0, 1);
        }

        return strtoupper(substr($acronym, 0, 2));
    }

    /**
     * 4. Warna Otomatis
     */
    public function getCalculatedColorAttribute()
    {
        if (!empty($this->attributes['color'])) {
            return $this->attributes['color'];
        }

        $colors = [
            '#e53935', '#d81b60', '#8e24aa', '#5e35b1',
            '#3949ab', '#1e88e5', '#039be5', '#00acc1',
            '#00897b', '#43a047', '#7cb342', '#c0ca33',
            '#fdd835', '#ffb300', '#fb8c00', '#f4511e'
        ];

        $index = crc32($this->nama_toko ?? 'Toko') % count($colors);
        return $colors[$index];
    }
}
